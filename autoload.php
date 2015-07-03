<?php

use Drupal\Composer\ClassLoader\AutoloaderBootstrap;

// Load Composer's autoloader.
$loader = require __DIR__ . '/../../autoload.php';

$autoloader_init = new AutoloaderBootstrap($loader);
$autoloader_init->register();
