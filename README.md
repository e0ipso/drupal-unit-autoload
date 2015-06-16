[![Coverage Status](https://coveralls.io/repos/mateu-aguilo-bosch/drupal-unit-autoload/badge.svg)](https://coveralls.io/r/mateu-aguilo-bosch/drupal-unit-autoload) [![Build Status](https://travis-ci.org/mateu-aguilo-bosch/drupal-unit-autoload.svg?branch=master)](https://travis-ci.org/mateu-aguilo-bosch/drupal-unit-autoload)

[![Gitter](https://badges.gitter.im/Join%20Chat.svg)](https://gitter.im/mateu-aguilo-bosch/drupal-unit-autoload?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)

# Drupal Unit Autoload

Have you ever wanted to add **PHPUnit*t* tests to your Drupal 7 module? Well, you should. This tool aims to help you to deal
with autoloading the classes that you have in your Drupal installation.

## The Problem
The main problem arises when the class -or code- that you are testing depends on classes declared in other modules, or
Drupal core itself.

Imagine that you are testing your `Car` class. That class depends on `\DrupalCacheInterface` (you are using a mock cache
provider that **has** to implement that interface), and also depends on several classes from the service container
module. After all you are injecting services in your `Car` class to be able to mock them afterwards, and that may
require to have the `Drupal\service_container\` available to you during tests.

Since you are doing unit testing, you may not want to bootstrap Drupal to have the database available in order to be
able to check in the registry to find all those classes.

At this point you can think _I will just use Composer's autoloader and define where to find those classes and
namespaces_. This is when you realize that Drupal allows you to install contrib modules in **many** locations. That
makes it impossible to ship your module with the relative paths that you need.

Imagine this possibility:
  - Our custom module (the one that will have unit testing) is installed in `sites/example.org/modules/contrib/racing_modules/car`.
  - The modules that the _car_ module depends on are installed in:
    - `sites/all/modules/essentials/service_container`.
    - `sites/default/modules/contrib/dependency`.

It seems that if you wanted to provide the path to `includes/cache.inc` to make `\DrupalCacheInterface` available, then
you would need to add a path like: `../../../../../../includes/cache.inc`. But what if someone decides to install your
`car` module in `sites/all/modules/car`? That path you provided in the module will not work in that situation. The
correct one would be `../../../includes/cache.inc`. Basically every site installation may need a different path.

The problem that this project aims to solve is to give you a way to provide a single path in your code that will work in
all those scenarios.

## The Solution
Meet the Drupal Unit Autoload.

The only thing that you need to do is add a new `composer.json` key with tokens in the path.

Inside the folder where you have your unit tests you will need to have a `composer.json` file that has:

```js
{
  "require-dev": {
    "phpunit/phpunit": "4.7.*",
    "mockery/mockery": "0.9.*",
    "mateu-aguilo-bosch/drupal-unit-autoload": "0.1.*"
  },
  "autoload": {
    "psr-0": {
      … This is usual Composer business …
    },
    "psr-4": {
      … This is usual Composer business …
    }
  },
  "class-loader": {
    "drupal-path": {
      "\\DrupalCacheInterface": "DRUPAL_ROOT/includes/cache.inc",
      "\\ServiceContainer": "DRUPAL_CONTRIB<service_container>/lib/ServiceContainer.php",
      "\\Drupal": "DRUPAL_CONTRIB<service_container>/lib/Drupal.php"
    },
    "psr-4": {
      "Drupal\\service_container\\": "DRUPAL_CONTRIB<service_container>/src",
      "Drupal\\Core\\": [
        "DRUPAL_CONTRIB<service_container>/lib/Core",
        "DRUPAL_CONTRIB<contrib>/src/DrupalCore"
      ]
    }
  }
}
```

Running `composer install` on that folder will download PHPUnit, Mockery -and all of the tools that you use for your
tests-. Additionally it will download this project, that is what `"mateu-aguilo-bosch/drupal-unit-autoload": "0.1.*"` is
for.

At this point you only need is add the new _class-loader_ key in your composer file. In there you have two options:
  - Provide class names and the files where they are found.
  - Provide psr-4 and psr-0 namespace prefixes and the path where they are mapped. This is very simmilar to what
    composer does, but with the magic that finds where the Drupal root is and where the contrib modules are installed.

In the paths that you provide, you will be able to include two tokens: `DRUPAL_ROOT` and `DRUPAL_CONTRIB<modulename>`.
Those tokens will be expanded to the real paths that they represent. This way, providing `DRUPAL_CONTRIB<ctools>` can end up expanding in:
  - `/var/www/docroot/sites/all/modules/ctools` in one Drupal installation.
  - `/User/Sites/drupal-site/sites/default/modules/contrib/ctools` in another installation.
  
The important thing to note is that your code ships with the same _tokenized_ path for everyone, without caring about
where the dependencies are installed.
