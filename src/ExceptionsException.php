<?php

namespace Openclerk\Exceptions;

/**
 * Represents an exception on the level of the {@code openclerk/exceptions} library.
 */
class ExceptionsException extends \Exception {

  /**
   * @param $previous wrapped exception
   */
  function __construct($message, \Exception $previous = null) {
    parent::__construct($message, 0, $previous);
  }
}

