# Values

Let's set up some initial Value objects to represent the data we need.

## Step 0: Figure out what we need

A little bit of planning goes a long way. So let me think out loud about what this app will do. I find it easiest to
start with **actions:** what the app will need to do. In this case:

1. User creates a medicine with dosage information.
1. User updates a medicine with new information.
2. User records a dose, and the app updates the next dose.
3. User removes a dose that was incorrectly entered.
4. User archives a medicine they are no longer taking.
5. User removes a medicine they no longer need data for.
6. User un-archives a medicine they are taking again.

That's enough for us to define our _domain model_, the core of the application logic. And we can already see some nouns
and verbs that will guide us to our definitions:

1. Entities (things that are stored and acted on)
	1. User
	2. Medicine
	3. Dose
2. Commands (actions that are taken)
	1. Create Medicine
	2. Update Medicine
	3. Archive Medicine
	4. Restore Medicine (un-archive)
	5. Record Dose
	6. Delete Dose
3. Events (results of actions)
	1. Medicine Created
	2. Medicine Updated
	3. Medicine Archived
	4. Medicine Restored
	5. Dose Added
	6. Dose Deleted

That looks good to me!

## Step 1: Entities

An **Entity** in Cavatappi is an object with an `id`, specifically a UUID from `ramsey/uuid`. It can be randomly
generated or deterministically generated, but it has an ID.

Our **User** entity is going to look pretty bare for now, as we only need the ID:

```php
use Cavatappi\Foundation\DomainEvent\Entity;
use Cavatappi\Foundation\Value;
use Cavatappi\Foundation\Value\ValueKit;
use Ramsey\Uuid\UuidInterface;

readonly class User implements Value, Entity {
	use ValueKit;

	public function __construct(public UuidInterface $id) {}
}
```

But this gives us a chance to talk about `Value`. In Cavatappi, every class is either a **Value** or a **Service.**
Values store information, and Services encapsulate logic. It's how I stick to the [single-responsibility principle][sr].

[sr]: https://en.wikipedia.org/wiki/Single-responsibility_principle

Value objects _should_ be read-only, but this isn't enforced at the code level. (I tried; it breaks down too easily.)
That's why instead of a superclass, we have an `interface` and a `trait` defining three methods:

- `with` - Creates a clone of the object with the given property changes.
- `equals` - Tests the object for equality with the given object, accounting for inconsistencies in properties. (For
   example: if a property is `Stringable`, it compares the string values instead of the objects themselves.)
- `static reflect` - Pulls information from annotations and PHP's type system into a standard format that can be
   modified.

Most Value classes will only need to `use ValueKit` to get all this for free.

Our **Medicine** entity will have a little more data:

```php
readonly class Medicine implements Value, Entity, Validated {
	use ValueKit;

	public UuidInterface $id;

	public function __construct(
		public string $name,
		public UuidInterface $userId,
		?UuidInterface $id = null,
		public ?int $hourlyInterval = null,
		public ?int $dailyLimit = null,
		public bool $alert = false,
		public bool $archived = false,
		public ?DateTimeInterface $nextDose = null,
	) {
		$this->id = $id ?? UuidFactory::random();
		$this->validate();
	}

	public function validate(): void {
		if (isset($this->hourlyInterval) && $this->hourlyInterval <= 0) {
			throw new InvalidValueProperties('Hourly interval must be null or positive.', field: 'hourlyInterval');
		}
		if (isset($this->dailyLimit) && $this->dailyLimit <= 0) {
			throw new InvalidValueProperties('Hourly interval must be null or positive.', field: 'dailyLimit');
		}
	}
}
```

Here we're storing a name, the two pieces of timing information, and a couple of flags for application functions. We're
also introducing the `Validated` interface. Since some scenarios (like serialization or cloning) bypass the constructor,
this breaks validation out into a separate function, `validate`, that can be called outside of the constructor. *We
should still call it within the constructor!*

We're also adding a default ID. If one isn't provided (a.k.a. this is a new Medicine), the constructor will create one
using `UuidFactory`, a static class for working with UUIDs inside a Value object.

We'll round it out with **Dose** which is almost as simple as User:

```php
readonly class Dose implements Value, Entity {
	use ValueKit;

	public function __construct(
		public UuidInterface $id,
		public UuidInterface $medicineId,
		public DateTimeInterface $timestamp,
	) {
	}
}
```

## Step 2: Commands + Events

**Command** and **Event** objects represent input and output to the domain model. Just like a function has parameters
and a return value, a domain model takes command objects and dispatches events with the results. It helps disconnect
our core logic from how our application is built: whether we use a PHP-based frontend or an API, our domain model will
get the same input.

(Plus, it helps define how to test the app, which we _are_ going to do.)

For the sake of brevity, we'll just look at a couple of command/event pairs. First, adding a Medicine:

```php
use Cavatappi\Foundation\Command\Authenticated;
use Cavatappi\Foundation\Command\Command;
use Cavatappi\Foundation\Value\ValueKit;
use oddEvan\PillTimer\Entities\Medicine;
use Ramsey\Uuid\UuidInterface;

class AddMedicine implements Command, Authenticated {
	use ValueKit;

	public function __construct(
		public readonly Medicine $medicine,
		public readonly UuidInterface $userId,
	) {
	}
}
```

The class itself is pretty sparse. But doing this allows us to use the validation code we already put in the `Medicine`
entity class. The `AddMedicine` command basically says "add this Medicine."

The `Authenticated` interface requires a `userId` UUID property. It is assumed that the value of that property is a user
that has been _authenticated_: that is, their identity has been verified and they are who they say they are, at least
as far as our app is concerned. This authenticated user is the one performing the action, and it's whose _authorization_
we'll check later.

So why not just use `$medicine->userId`? Good question! In this particular case, I'm making it a separate property to
account for potential cases where a user is adding a Medicine for someone else. Maybe it's an import process, or an
administrator making a change. Maybe there's a use-case later on for a family plan? Either way, it feels safer to have
it be a separate property for now.

The corresponding Event looks similar:

```php
use Cavatappi\Foundation\DomainEvent\DomainEvent;
use Cavatappi\Foundation\Factories\UuidFactory;
use DateTimeImmutable;
use DateTimeInterface;
use oddEvan\PillTimer\Entities\Medicine;
use Ramsey\Uuid\UuidInterface;

class MedicineAdded implements DomainEvent {
	public readonly UuidInterface $id;
	public readonly DateTimeInterface $timestamp;

	public function __construct(
		public readonly Medicine $medicine,
		public readonly UuidInterface $userId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->timestamp = $timestamp ?? new DateTimeImmutable();
		$this->id = $id ?? UuidFactory::date($this->timestamp);
	}

	public string $type { get => self::class; }
	public UuidInterface $entityId { get => $this->medicine->id; }
	public UuidInterface $aggregateId { get => $this->medicine->id; }
}
```

This has some added data, mostly to satisfy the `DomainEvent` interface. There's a lot here, mostly to facilitate
indexing a stream of events:

- `entityId` is the ID for the entity being affected by this event, in this case the Medicine.
- `aggregateId` is the ID for a broader entity or group that this entity is part of. In this case, it's still the
  Medicine.
- `processId` is a way to denote events that are linked by a process, such as an import or remote system call.

We're also using the `UuidFactory::date` function to create a
[version 7 UUID](https://uuid.ramsey.dev/en/stable/rfc4122/version7.html) since events are created and stored in
sequential order.

To provide a little more context, here's a Command and Event for adding a Dose:

```php
readonly class AddDose implements Command, Authenticated {
	use ValueKit;

	public function __construct(
		public Dose $dose,
		public UuidInterface $userId,
	) {
	}
}
```

```php
class DoseAdded implements DomainEvent {
	public readonly UuidInterface $id;
	public readonly DateTimeInterface $timestamp;

	public function __construct(
		public readonly Dose $dose,
		public readonly UuidInterface $userId,
		?UuidInterface $id = null,
		?DateTimeInterface $timestamp = null,
		public readonly ?UuidInterface $processId = null,
	) {
		$this->timestamp = $timestamp ?? new DateTimeImmutable();
		$this->id = $id ?? UuidFactory::date($this->timestamp);
	}

	public string $type { get => self::class; }
	public UuidInterface $entityId { get => $this->dose->id; }
	public UuidInterface $aggregateId { get => $this->dose->medicineId; }
}
```

They're very simliar to the Medicine command and event. The most notable difference is while the `entityId` is the Dose
ID, the `aggregateId` is still the Medicine ID since that's the "group" the Dose belongs to.

## Wrapup

So wait, if the command and event are so similar, why not just combine them? Especially if the command is already
authenticated?

A big part of it is _intent._ A Command represents something that should happen, while an Event is something that has
happened. In between the two is the domain model code, including _authorization._ While we know _who_ is making the
request, it is up to the domain model to determine if they _can._ Not every Command will result in an Event.

As for how similar these Commands and Events are and how they look like copypasta, the very thing this framework wants
to avoid? Well, that's being worked on. Hopefully, as we get closer to version 1, this walkthrough will look a little
different.
