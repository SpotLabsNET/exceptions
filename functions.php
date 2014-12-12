<?php

if (!function_exists('db')) {
  // this is because we don't have a reference to a \Db\Connection at the time of exceptions
  throw new \Openclerk\ExceptionsException("db() function needs to be defined to use openclerk/exceptions");
}

function openclerk_exceptions_exception_handler($e) {
  $extra_args = array();
  $extra_query = "";

  header('HTTP/1.0 500 Internal Server Error');
  // TODO
  // if (function_exists('my_content_type_exception_handler')) {
  //   my_content_type_exception_handler($e);
  // } else {
    echo "Error: " . htmlspecialchars($e->getMessage());
    if (\Openclerk\Config::get('display_errors', false)) {
      // only display trace locally
      echo "<br>Trace:";
      print_exception_trace($e);
    }
  // }

  // logging
  log_uncaught_exception($e, $extra_args, $extra_query);
  die;
}

set_exception_handler('openclerk_exceptions_exception_handler');

/**
 * Allows for capturing fatal errors (missing includes, undefined functions etc)
 */
function openclerk_exceptions_fatal_handler() {
  $error = error_get_last();
  if ($error['type'] == E_ERROR || $error['type'] == E_CORE_ERROR || $error['type'] == E_COMPILE_ERROR) {
    log_uncaught_exception(new \Openclerk\FatalException($error));

    // events
    \Openclerk\Events::trigger('exception_fatal', $error);
  }
}

register_shutdown_function('openclerk_exceptions_fatal_handler');

function log_uncaught_exception($e, $extra_args = array(), $extra_query = "") {
  // events
  \Openclerk\Events::trigger('exception_uncaught', $e);

  // logging
  if ($e instanceof \Openclerk\WrappedArgumentException) {
    // unwrap it
    $extra_args[] = $e->getArgumentId();
    $extra_args[] = $e->getArgumentType();
    $extra_query .= ", argument_id=?, argument_type=?";
    $e = $e->getCause();
  }
  if (get_class($e) !== false) {
    $extra_args[] = get_class($e);
    $extra_query .= ", class_name=?";
  }
  $q = db()->prepare("INSERT INTO uncaught_exceptions SET
    message=?,
    previous_message=?,
    filename=?,
    line_number=?,
    raw=?,
    created_at=NOW() $extra_query");
  try {
    $serialized = serialize($e);
  } catch (Exception $e) {
    $serialized = $e->getMessage() . ": " . print_r($e, true);
  }
  return $q->execute(array_join(array(
    // clamp messages to 255 characters
    mb_substr($e->getMessage(), 0, 255),
    mb_substr($e->getPrevious() ? $e->getPrevious()->getMessage() : "", 0, 255),
    mb_substr($e->getFile(), 0, 255),
    $e->getLine(),
    mb_substr($serialized, 0, 65535),
  ), $extra_args));
}
