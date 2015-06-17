<?php

use Drupal\Composer\ClassLoader\AutoloaderBootstrap;

return function ($loader) {
  return new AutoloaderBootstrap($loader);
};
