# Setup

This is mostly setting up [Composer](https://getcomposer.org) with the Cavatappi packages and some extra tricks I like
to have.

## Step 1: `composer init`

Yeah, I'm basic. I've already got Composer installed via [Homebrew](https://brew.sh), so I'll run its built-in
step-by-step initializer:

```shell
brew install composer
composer init
```

I'm only going to use the Foundation package for now while things get set up. And while Cavatappi itself uses the
[ASF license](https://www.apache.org/licenses/LICENSE-2.0), I'm going to restrict _this_ project's code to the
[GNU Affero GPL license](https://www.gnu.org/licenses/agpl-3.0.html).

(That being said, **all code samples in the documentation are
[public domain via CC0](https://creativecommons.org/publicdomain/zero/1.0/)**, so reuse and modify anything you see here
for whatever purpose you want!)

At the end, I'll have a `composer.json` file that looks something like this:

```json
{
	"name": "oddevan/pilltimer",
	"description": "Backend for the web version of PillTimer",
	"type": "project",
	"require": {
		"cavatappi/foundation": "^0.1.0"
	},
	"license": "AGPL-3.0",
	"autoload": {
		"psr-4": {
			"oddEvan\\PillTimer\\": "src/"
		}
	},
	"authors": [
		{
			"name": "Evan Hildreth",
			"email": "me@eph.me"
		}
	]
}
```

Normally in a Composer project we would need to include the autoloader, but we're not there yet. And we won't be for
some time.

## Step 2: Standards and Practices

I like having some reasonable coding standards in place, if only because it means I can auto-format my code. Because
this is a personal project and not necessarily intended for other developers to build off of, I'm mostly going to limit
the included standards to code formatting and forgo the more documentation-heavy ones.

I use [PHP_CodeSniffer] mostly because that's what dominates the WordPress ecosystem and therefore what I learned first.

```shell
composer require --dev squizlabs/php_codesniffer
```

And then add a `.phpcs.xml` config file that looks like this (suspiciously similar to Cavatappi's):

```xml
<?xml version="1.0"?>
<ruleset name="PillTimer Standards" namespace="oddEvan\PillTimer">
	<description>Coding standards for PillTimer.</description>

	<file>./src</file>

	<exclude-pattern>*/src-test/*</exclude-pattern>
	<exclude-pattern>*/tests/*</exclude-pattern>

	<arg name="extensions" value="php"/>
	<arg name="tab-width" value="2"/>
	<arg name="colors"/>
	<arg value="s"/>

	<ini name="memory_limit" value="64M"/>

	<autoload>./vendor/autoload.php</autoload>

	<!-- Use PSR-12 as our base -->
	<rule ref="PSR12">
		<exclude name="Generic.WhiteSpace.DisallowTabIndent"/>
		<exclude name="Generic.WhiteSpace.ScopeIndent"/>
		<exclude name="PSR2.Classes.ClassDeclaration.OpenBraceNewLine"/>
		<exclude name="PSR2.Methods.FunctionCallSignature.Indent"/>
		<exclude name="Squiz.Functions.MultiLineFunctionDeclaration.BraceOnSameLine"/>
		<exclude name="Squiz.Functions.MultiLineFunctionDeclaration.Indent"/>
	</rule>
	<rule ref="PSR2.Methods.FunctionCallSignature.Indent">
		<properties>
			<property name="indent" value="2"/>
		</properties>
	</rule>

	<!-- Use tabs, please. Please. Seriously. -->
	<!-- https://alexandersandberg.com/articles/default-to-tabs-instead-of-spaces-for-an-accessible-first-environment/ -->
	<rule ref="Generic.WhiteSpace.DisallowSpaceIndent"/>
	<rule ref="Generic.WhiteSpace.ScopeIndent">
		<properties>
			<property name="indent" value="2"/>
			<property name="tabIndent" value="true"/>
			<property name="ignoreIndentationTokens" type="array">
				<element value="T_COMMENT"/>
				<element value="T_DOC_COMMENT_OPEN_TAG"/>
			</property>
		</properties>
	</rule>

	<!-- Make structures look nicer -->
	<rule ref="Generic.Classes.OpeningBraceSameLine"/>
	<rule ref="Generic.Functions.OpeningFunctionBraceKernighanRitchie"/>
</ruleset>
```

That's enough to tie into my code editor, but I like having some command line shortcuts, especially when it comes to
auto-formatting. I'll add this to my `composer.json` file:

```json
	"scripts": {
		"lint": "./vendor/squizlabs/php_codesniffer/bin/phpcs",
		"lintfix": "./vendor/squizlabs/php_codesniffer/bin/phpcbf"
	}
```

OK, enough messing around. On to the next step: [defining our data model](02-values.md)!
