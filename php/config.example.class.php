<?php

class config
{
  const db = 'mysql';
  // const db = 'pgsql';

  const mysqlHOST = 'localhost';
  const mysqlUSER = '';
  const mysqlPASS = '';
  const mysqlDB = '';

  const pgsqlHOST = '';
  const pgsqlUSER = '';
  const pgsqlPASS = '';
  const pgsqlDB = '';

  const private_key = "-----BEGIN RSA PRIVATE KEY-----";

  const twitterConsumerKey = "";
  const twitterConsumerSecret = "";
  const twitterAccessToken = "";
  const twitterAccessTokenSecret = "";

  const use_memcache = true;
  const memcache_host = 'localhost';
  const memcache_port = '11211';

  const use_markdown = true;

  const google_analytics_email = '';
  const google_analytics_password = '';
  const google_analytics_profile_id = '';

  function host()
  {
    $const = $this::db . "HOST";
    return constant("self::$const");
  }

  function user()
  {
    $const = $this::db . "USER";
    return constant("self::$const");
  }

  function pass()
  {
    $const = $this::db . "PASS";
    return constant("self::$const");
  }

  function db()
  {
    $const = $this::db . "DB";
    return constant("self::$const");
  }

}

?>