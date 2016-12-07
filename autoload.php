<?php

use Drupal\Composer\ClassLoader\AutoloaderBootstrap;

// Register root dir
if (getenv('COMPOSER_CONFIGURATION_PATH') === false) {
  putenv('COMPOSER_CONFIGURATION_PATH=' . dirname(dirname(dirname(__DIR__))));
}

define('COMPOSER_CONFIGURATION_PATH', getenv('COMPOSER_CONFIGURATION_PATH'));

// Load Composer's autoloader.
$loader = require __DIR__ . '/../../autoload.php';

$autoloader_init = new AutoloaderBootstrap($loader);
$autoloader_init->register();
