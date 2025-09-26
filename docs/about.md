# About Cavatappi

I'll assume you already know the benefits of using a framework, but if you don't, the
<abbr title="Too long, didn't read">TL;DR</abbr> is less boilerplate code for you to write and maintain, which leaves
you more time to focus on _your_ app.

So when I tell you that instead of using any of the existing frameworks I decided to build my own, you should have at
least one question:

## Why?

<div class="tenor-gif-embed" data-postid="13199396" data-share-method="host" data-aspect-ratio="1.77914" data-width="100%"><a href="https://tenor.com/view/why-huh-but-why-gif-13199396">But Why?</a></div> <script type="text/javascript" async src="https://tenor.com/embed.js"></script>

The short answer is _because I wanted to._

To elaborate, I was working on a [big project with a small name][smol] and wanted to truly understand what my code was
doing. I wanted to put the object-oriented lessons I was finally understanding into practice. I wanted something
testable, portable, and lightweight. And I had just enough of a grasp of the modern PHP ecosystem to feel like I could
do it, but not enough experience to tell me not to.

[smol]: https://smolblog.com/

This does beg another question: why should _you_ use Cavatappi? Let me be a little blunt: **you shouldn't.**

## Don't use this

Cavatappi, while born out of an existing project, does not currently have a production app to showcase it. It's ready
for me to show the world, but it's not in a place where I'm comfortable using it for real production work.

That being said, if the ideas on this site resonate with you and you want to help out, check out the [GitHub repo][gh]
and see what you can contribute.

[gh]: https://github.com/cavatappiphp/cavatappi

## Why the name, though?

I don't like cut-and-paste code. Lots of boilerplate tells me there's a chance for an abstraction, or at least a way to
provide some sane defaults. So that's why the framework is named after a pasta: because I don't like copypasta. I chose
this specific pasta because...

1. The domain name was available, and
2. It kinda looks like a snake.

Thus, Cavatappi.

## Axioms

Yes, it's reductive, but it helps get the ideas across. So here's some guiding principles for the Cavatappi framework:

1. **Defaults, not Dogma**: Build features and abstractions so that simple apps can be written with minimal code, but
   deviations from the norm don't require re-implementing the entire feature. Try to maximize what can be done before
	 a developer needs to abandon the framework's abstraction entirely.
2. **Signals, not Magic**: Use explicit signals like PHP's type system and annotations to opt into behavior. Don't
   change the framework's behavior because a file is in a particular folder or a parameter has a particular name.

## The Basic Idea

The basis for Cavatappi is that every class is either a **Value** or a **Service**:

- **Value** objects are (generally) immutable and used to store structured data. They should have no dependencies and
  be internally consistent. Value objects house _state_, not logic.
- **Service** objects are (generally) stateless and used to perform operations. They are given the dependencies they
  need at construction in order to work with structured data. Service objects house _logic_, not state.

## The Details

Cavatappi takes this overall philosophy and uses it to help you build a well-organized app. Some other concepts make
their way in, like:

- **Registries**: Find all classes that implement a particular interface and store information about them in a central
  location. This is currently the way to introduce extendability into applications.
- **Commands and Events**: Build a Domain Model that takes `Command` objects as input and dispatches `Event` objects
  with the result.
- **Reflection**: Use PHP's type system and annotations to build a meta-picture of classes that can be used to inform
  application behavior.
- **Domain-driven Design**: Isolate core application logic from general application concerns.

## Downstream Libraries

Despite the foolishness of this endeavor, Cavatappi does build off several existing PHP libraries:

- [Tukio][tukio] and [Serde][serde] by Larry Garfield
- [ramsey/uuid][uuid] by Ben Ramsey
- [Construct Finder][cf] by The League of Extraordinary Packages
- [nyholm/psr7][psr7] by Tobias Nyholm and Martijn van der Ven
- [PHPUnit][phpunit] by Sebastian Bergmann

[tukio]: https://github.com/crell/tukio
[serde]: https://github.com/crell/serde
[uuid]: https://uuid.ramsey.dev/
[cf]: https://github.com/thephpleague/construct-finder
[psr7]: https://github.com/Nyholm/psr7/tree/master
[phpunit]: https://docs.phpunit.de/en/12.3/
