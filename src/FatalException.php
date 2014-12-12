<?php

namespace Openclerk;

class FatalException /* cannot extend Exception, since getMessage() etc are final */ implements TypedException {
  var $wrapped;

  public function __construct($error) {
    $this->wrapped = $error;
  }

  public function getMessage() {
    return $this->wrapped['message'];
  }

  public function getFile() {
    return $this->wrapped['file'];
  }

  public function getCode() {
    return $this->wrapped['type'];
  }

  public function getLine() {
    return $this->wrapped['line'];
  }

  public function getPrevious() {
    return null;
  }

  public function getArgumentId() {
    return $this->wrapped['type'];
  }

  public function getArgumentType() {
    return "error";
  }
}
