<?php

/**
 * Represents an exception that can have an additional argument.
 */
interface WrappedArgumentException {

  /**
   * @return an integer
   */
  public function getArgumentId();

  /**
   * @return a string
   */
  public function getArgumentType();

}
