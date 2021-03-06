<?php

/**
 * @file
 * Contains Drupal\Composer\ClassLoader\TokenResolver.
 */

namespace Drupal\Composer\ClassLoader;


class TokenResolver implements TokenResolverInterface {

  /**
   * Path.
   *
   * @var string
   */
  protected $path;

  /**
   * Supported tokens.
   *
   * The key is the name of the token, the value is the class that instantiates.
   *
   * @var array
   */
  private $supportedTokens = array(
    'DRUPAL_ROOT' => 'Drupal\Composer\ClassLoader\Discovery\PathFinderCore',
    'DRUPAL_CONTRIB' => 'Drupal\Composer\ClassLoader\Discovery\PathFinderContrib',
  );

  /**
   * Constructs a TokenResolver object.
   *
   * @param string $tokenized_path
   *   The path containing a potential token.
   */
  public function __construct($tokenized_path = '') {
    $this->path = $tokenized_path;
  }

  /**
   * {@inheritdoc}
   */
  public function resolve() {
    // If the path is not tokenized, then return the NULL discovery object.
    if (file_exists($this->path)) {
      return new Discovery\PathFinderNull(array($this->path));
    }
    if (!$this->getToken()) {
      return NULL;
    }
    // Get the name of the class of the PathFinder depending on if we are
    // looking for Drupal root or contrib path.
    $class_name = $this->getClassName();
    // Remove the leading token from the tokenized path to get the relative path
    // (to the Drupal root or the module/theme path).
    $arguments[] = $this->cleanToken();
    // Add more arguments to the constructor, like the module name.
    $arguments = array_merge($arguments, $this->parseArguments());
    return new $class_name($arguments);
  }

  /**
   * {@inheritdoc}
   */
  public function hasToken() {
    return (bool) $this->getToken();
  }

  /**
   * {@inheritdoc}
   */
  public function trimPath() {
    if (!$token = $this->getToken()) {
      return $this->path;
    }
    $pos = strpos($this->path, $token);
    return substr($this->path, $pos);
  }

  /**
   * Removes the token from the tokenized path.
   *
   * @throws ClassLoaderException
   *   If no token can be found.
   *
   * @return string
   *   The cleaned path.
   */
  protected function cleanToken() {
    if ($token_name = $this->getToken()) {
      // Remove the token and arguments and return the path.
      $path = substr($this->path, strlen($token_name));
      return preg_replace('/<.*>/', '', $path);
    }
    $message = sprintf('No token could be found in "%s". Available tokens are: %s.', $this->path, implode(', ', array_keys($this->supportedTokens)));
    throw new ClassLoaderException($message);
  }

  /**
   * Checks if the current tokenized path contains a known token.
   *
   * @return string
   *   The token found. NULL otherwise.
   */
  protected function getToken() {
    // Iterate over the supported tokens to find the token in the tokenized
    // path.
    foreach (array_keys($this->supportedTokens) as $token_name) {
      if (strpos($this->path, $token_name) !== FALSE) {
        return $token_name;
      }
    }
    return NULL;
  }

  /**
   * Gets the class name corresponding to the token.
   *
   * @return string
   *   The class name.
   */
  protected function getClassName() {
    $token_name = $this->getToken();
    return $token_name ? $this->supportedTokens[$token_name] : NULL;
  }

  /**
   * Gets the arguments in the token.
   *
   * @return string[]
   *   A numeric array containing the token arguments.
   */
  protected function parseArguments() {
    $token_name = $this->getToken();
    $delimiter = '/';
    $matches = array();
    if (preg_match($delimiter . preg_quote($token_name) . '<(.+)>.*' . $delimiter, $this->path, $matches)) {
      // Some arguments were found.
      return explode(',', $matches[1]);
    }
    return array();
  }

}
