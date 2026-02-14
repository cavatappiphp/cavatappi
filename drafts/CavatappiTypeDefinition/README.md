# Cavatappi Type Definition

A language to quickly define data objects complete with validation, documentation, and composition.

## Really?

No.

This is a trap. I _know_ it's a trap. But in this business, you either retire a coder, or you live long enough to
attempt a custom programming language.

That's too dramatic. Never mind. But I did take programming language fundamentals in college, and it's kind of stuck
with me.

## What this is?

Anyway, a lot of Cavatappi's (hopeful) magic is in inferring as much as possible from the types, annotations, and
structure of Value objects. This language would be an attempt at something purpose-built as opposed to a mashup of
various interfaces and attributes. It would likely compile to PHP, though done right it could also go to other languages
like Swift and Ruby (while adding type checking to the latter).

So far largely inspired by TypeScript, but the focus would be on creating the data objects. Any sort of logic statements
would be in service of small formatting or validation functions that can't be handled with attributes.

## Any sort of plan?

`while (!$done) :`

- [ ] Validate the approach (i.e. have a production-level Cavatappi app using Value objects)
- [ ] Get examples together
- [ ] Write a grammar, probably for [ANTLR](https://www.antlr.org/)
- [ ] Generate PHP code

`endwhile;`