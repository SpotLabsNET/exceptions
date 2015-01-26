<?php

namespace Openclerk\Exceptions;

class UncaughtExceptionsMigration extends \Db\Migration {

  /**
   * Apply only the current migration.
   * @return true on success or false on failure
   */
  function apply(\Db\Connection $db) {
    $q = $db->prepare("CREATE TABLE uncaught_exceptions (
      id int not null auto_increment primary key,
      created_at timestamp not null default current_timestamp,

      message varchar(255) null,
      previous_message varchar(255) null,
      class_name varchar(255) null,
      filename varchar(255) null,
      line_number int null,
      raw blob null,

      argument_id int null,
      argument_type varchar(255) null,

      INDEX(class_name),
      INDEX(argument_type, argument_id)
    );");
    return $q->execute();
  }

}
