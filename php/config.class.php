<?php

class config
{
  const db = 'mysql';
  // const db = 'pgsql';

  const mysqlHOST = '';
  const mysqlUSER = '';
  const mysqlPASS = '';
  const mysqlDB = '';

  const pgsqlHOST = '';
  const pgsqlUSER = '';
  const pgsqlPASS = '';
  const pgsqlDB = '';

  function driver()
  {
    return self::db;
  }

  function host()
  {
    $const = $this->driver() . "HOST";
    return constant("self::$const");
  }

  function user()
  {
    $const = $this->driver() . "USER";
    return constant("self::$const");
  }

  function pass()
  {
    $const = $this->driver() . "PASS";
    return constant("self::$const");
  }

  function db()
  {
    $const = $this->driver() . "DB";
    return constant("self::$const");
  }

}

?>