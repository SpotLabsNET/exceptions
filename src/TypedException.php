<?php

namespace Openclerk\Exceptions;

/**
 * Represents an exception that can have an additional argument.
 */
interface TypedException {

  /**
   * @return an integer
   */
  public function getArgumentId();

  /**
   * @return a string
   */
  public function getArgumentType();

}
