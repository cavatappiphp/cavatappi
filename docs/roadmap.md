# Roadmap

Everything is subject to change. Even this. _Especially_ this.

## Current

This is what's in the framework now

### Dependency Injection

- Using a [stupidly simple container][oec].
- Register Services automatically.
- Set up concrete implementations of interfaces and abstract classes.

[oec]: https://oddevan.com/2023/08/31/a-stupidly-simple.html

### Registries

- Surfacing a class to the system is as simple as `implements Interface`.
- Register different adapters, add services to the dependency injection container, or other uses.

### Commands and Events

- Using a custom-built Command Bus and [Tukio][tukio] for event dispatching.
- Define a domain model with Commands as input and Events as output.
- Auto-register Command handlers and Event listeners.
- Sets up event sourcing for data persistence.

[tukio]: https://github.com/crell/tukio

## Version 1

This is what I would consider the needed feature set before calling the framework ready for general use.

### API

- Set up a registry and configuration for an Endpoint class so that endpoints can be auto-registered.
- Use reflection tools to generate an [OpenAPI][swag] (or similar) spec.
- Integrate with (or provide adapter to) something like [Slim][slim] to handle routing.

[swag]: https://learn.openapis.org
[slim]: https://www.slimframework.com

### Markdown

- Registry system for [Markdown][md] customizations.

[md]: https://daringfireball.net/projects/markdown/

### Data Persistence

- Make it as easy to do [event sourcing][es] as most frameworks make [CRUD][crud].
- Registry for database tables.
- Integrate with [Doctrine DBAL][dbal].

[es]: https://en.wikipedia.org/wiki/Domain-driven_design#Event_sourcing
[crud]: https://en.wikipedia.org/wiki/Create,_read,_update_and_delete
[dbal]: https://www.doctrine-project.org/projects/doctrine-dbal/en/4.3/reference/introduction.html#introduction

### Authentication

- Integrate with some library (what's the PHP equivalent to [Omniauth][omni]?)
- Create extendable User entity
- Some kind of OAuth or JWT scheme to secure APIs
- Make this effortless. Like, really effortless. Like works-out-of-the-box effortless.

[omni]: https://omniauth.github.io/omniauth/

## Further Enhancements

The lofty ideas of things beyond version 1.

### UI Framework

- Build encapsulated UI components in PHP
- Use other design systems as a base and layer customizations on top
- Use reflection to make a form builder.
- Or just make a form builder.
- Seriously, can I have a form builder?

### Static Site Generation

- Have a way to generate HTML pages and save them to disk.
- Useful for making landing pages or outputting a single-page app

### Build System

- Generate hard-coded configurations and setups for Registries and other high-use classes.
- Optimize the system when it's in a stable place (staging/production) without compromising development speed.
- Allow other systems to hook in for generating documentation, web pages, etc.

### [Smolblog](https://smolblog.com/)

- That is why this whole thing started.