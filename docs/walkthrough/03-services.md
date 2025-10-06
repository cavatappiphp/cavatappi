# Services

We've got lots of data, now let's _do_ something with it!

## Step 0: Figure out what we need

Sorry, but that's _always_ step zero. In this case, let's go back to our list of actions:

1. User creates a medicine with dosage information.
1. User updates a medicine with new information.
2. User records a dose, and the app updates the next dose.
3. User removes a dose that was incorrectly entered.
4. User archives a medicine they are no longer taking.
5. User removes a medicine they no longer need data for.
6. User un-archives a medicine they are taking again.

I'm seeing a few potential **Service** objects here. Let's lay them out:

1. Medicine Management (create/update/delete/archive)
2. Medicine Repository (data storage)
3. Dose Management (create/delete)
4. Dose Repository (data storage)
5. Next Dose Calculation

Now, are 1-4 strictly single-responsibility? No. But making a separate service for every create, update, and delete
operation feels _exceedingly_ tedious. We can map **Command** objects to individual methods in the service, so one
service can handle multiple commands.

The repositories are separate so that we can separate our domain logic from our data storage. In our domain model,
each one will just be an `interface`. We'll get to the actual database storage... later.

Finally, our service for next dose calculation is its own thing for a couple of reasons. One, it's a significantly
different pattern from the others. Two, instead of handling commands, it's going to react to **Event** objects.
Whenever an event happens that could change when the next dose of medicine is, this service will recalculate the next
dose and send its own event to mark the change.

## Step 1: Repo Interfaces

Since the other services will depend on the repository interfaces, we'll define them first:

```php
use oddEvan\PillTimer\Entities\Medicine;
use Ramsey\Uuid\UuidInterface;

interface MedicineRepo {
	public function has(UuidInterface $medicineId): bool;
	public function get(UuidInterface $medicineId): ?Medicine;
	public function setNextDose(UuidInterface $medicineId, DateTimeInterface $timestamp): void;
}
```

I like to use `has` and `get` to mirror [PSR-11](https://www.php-fig.org/psr/psr-11/), but that's a personal choice.
There's also a `setNextDose` method for reasons we'll get into in a bit.

Dose needs a slightly different method:

```php
interface DoseRepo {
	/** @return Dose[] */
	public function dosesForMedicineInLastDay(UuidInterface $medicineId): array;
}
```

Calculating the next dose is only going to need doses from the last 24 hours, so that's what this method is limited to.

Now that we have these interfaces, we can type-hint against them in our services.

## Step 2: Management Services

These are all pretty similar (and something I'm hoping to provide some shortcuts for in the future), so here's the idea:

```php
use Cavatappi\Foundation\Command\{CommandHandler, CommandHandlerService};
use Cavatappi\Foundation\Exceptions\{ActionNotAuthorized, InvalidValueProperties};
use Cavatappi\Foundation\Factories\UuidFactory;
use oddEvan\PillTimer\Commands\AddMedicine;
use oddEvan\PillTimer\Events\MedicineAdded;
use Psr\EventDispatcher\EventDispatcherInterface;

class MedicineService implements CommandHandlerService {
	public function __construct(
		private MedicineRepo $repo,
		private EventDispatcherInterface $eventBus
	) {
	}

	#[CommandHandler]
	public function addMedicine(AddMedicine $cmd): void {
		if ($this->repo->has($cmd->medicine->id)) {
			throw new InvalidValueProperties("A medicine with the ID {$cmd->medicine->id} already exists");
		}
		if (!$cmd->userId->equals($cmd->medicine->userId)) {
			throw new ActionNotAuthorized('You cannot create a Medicine for someone else.');
		}

		$this->eventBus->dispatch(new MedicineAdded(
			medicine: $cmd->medicine,
			userId: $cmd->userId,
		));
	}
}
```

First off, we declare dependencies in the constructor: the `MedicineRepo` we defined earlier along with an
`EventDispatcherInterface` from [PSR-14](http://www.php-fig.org/psr/psr-14/), which we use to dispatch the event.

As an example of a `CommandHandler`, we have one for the `AddMedicine` command. Remember, the only preconditions for
a command object are

1. It is a valid object according to its conditions, and
2. If it is an `Authenticated` command, the `userId` property represents the user issuing the command.

Everything else is domain-specific. So for PillTimer, we check:

1. Is the ID already in use? This is a create method, so there shouldn't already be a Mediicne with this ID.
2. Is the user authorized? For now, we're only checking if the user is creating a medicine for themselves. In the future
   we may call out to a separate service for more fine-grained permissions.

If all the conditions are met, we should save the Medicine. To do that, we dispatch the `MedicineAdded` event and add
the appropriate information.

With that, the domain model's work is done, right? Eh, not quite.

## Step 3: Ephemeral Data

The idea behind an event-sourced system is to store the events with their necessary data; everything else is ephemeral.
For PillTimer, that includes the time of the next dose. It's not something explicitly entered by the user, it's
calculated using:

1. The doses in the last 24 hours,
2. The time allowed between doses, and
3. The number of allowed doses per 24 hours.

That's why, instead of having a "Next Dose Time Set" event, we have a method on the `MedicineRepo` to add the
information to the Medicine object. Other methods could include keeping the information in a separate repository or
calculating it on-the-fly every time it's needed. This method feels the most straightforward to me, at least right now.

So how do we calculate this when we need to? We listen for any events that might change the next dose time and run our
code then. There are two ways we could do this:

1. List all the events to listen for in our service, or
2. Declare an interface for all appropriate events to implement.

I personally think that either method is valid, especially on a small app like this for an event intended to be used
by its own domain model. The second feels like less typing, so I'm going to go with that one.

First, we declare a new interface:

```php
interface ChangesNextDoseTime {
	public function doseTime(): ?DateTimeInterface;
}
```

We could have made it completely empty, but this will make the code simpler in our service without compromising the
Events themselves.

Next, we add the new interface to our existing events and implement the new method. For `DoseAdded`, it's simple:

```php
class DoseAdded implements DomainEvent, ChangesNextDoseTime {
	// ...
	public function doseTime(): ?DateTimeInterface {
		return $this->dose->timestamp;
	}
}
```

We would do the same for a `DoseDeleted` event (which is good to know since normally we wouldn't include info from the
entity on an event deleting it).

For `MedicineAdded`, though, we forgo the interface entirely. A new medicine won't have any doses, so it won't have a
time for a next dose. `MedicineUpdated`, though, will, since it could change the timing:

```php
class MedicineUpdated implements DomainEvent, ChangesNextDoseTime {
	// ...
	public function doseTime(): ?DateTimeInterface {
		return null;
	}
}
```

Since there's no new dosage information, we return `null`.

Once we've gotten all our events updated, we can create the new service:

```php
use Cavatappi\Foundation\DomainEvent\EventListenerService;
use Cavatappi\Foundation\DomainEvent\ProjectionListener;
use DateTimeImmutable;
use oddEvan\PillTimer\Events\ChangesNextDoseTime;

class NextDoseService implements EventListenerService {
	public function __construct(
		private MedicineRepo $medicineRepo,
		private DoseRepo $doseRepo
	) {
	}

	public const TWENTY_FOUR_HOURS = 24 * 60 * 60;

	#[ProjectionListener]
	public function recalculate(ChangesNextDoseTime $event) {
		$doseTime = $event->doseTime()?->getTimestamp() ?? null;
		if (isset($doseTime) && time() - $doseTime > self::TWENTY_FOUR_HOURS) {
			// A dose older than the last 24 hours was changed; we can safely ignore it.
			return;
		}

		$medicine = $this->medicineRepo->get($event->aggregateId);
		$doses = $this->doseRepo->dosesForMedicineInLastDay($event->aggregateId);
		usort($doses, fn($doseA, $doseB) => $doseA->timestamp->getTimestamp() - $doseB->timestamp->getTimestamp());

		if (empty($doses)) {
			// No existing doses; we can safely ignore the event.
			return;
		}

		$this->medicineRepo->setNextDose(
			medicineId: $event->aggregateId,
			timestamp: self::calculate($doses, $medicine->hourlyInterval, $medicine->dailyLimit),
		);
	}

	private static function calculate(array $doses, ?int $interval, ?int $limit): DateTimeImmutable {
		// idk; stuff?
		return new DateTimeImmutable();
	}
}
```

(Yes, that's a placeholder.)

There are two types of event listener attributes: `EventListener` and `ProjectionListener`. Both will be called when
the given type-hinted event is dispatched. To save the whole event-sourcing conversation for later, we'll just say that
a Projection-style listener shouldn't have side effects like calling other services, issuing commands, or the like.
Since this is only calculating derived data, it's a Projection.

## Wrapup

So that's a look at how Service classes work in Cavatappi. We're looking at incoming Commands, outgoing Events, and
even consuming some of those downstream events.

And all this means our system is now ready to test! Aren't you excited?
