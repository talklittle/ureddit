<?php

class base {
  public $config = NULL;
  public $memcache = NULL;

  function __autoload($class)
  {
    require_once("$class.class.php");
  }

  function __construct($config)
  {
    $this->config = $config;
    if($this->config->memcache())
      {
	$this->memcache = new Memcache;
	if(!$this->memcache->pconnect($this->config->memcache_host(), $this->config->memcache_port()))
	  $this->error("Connection to memcached failed.");
      }
  }

  function log_to_feed($action, $object_id = NULL, $indirect_id = NULL)
  {
    $date = $this->timestamp();
    if($object_id == NULL)
      {
	if($indirect_id == NULL)
	  {
	    $this->dbpdo->query("INSERT INTO `activity` (`parent_id`, `action`,`child_id`, `indirect_id`,`datetime`) VALUES (?, ?, NULL, NULL, ?)", array($this->id, $action, $date));
	  }
	else
	  {
	    $this->dbpdo->query("INSERT INTO `activity` (`parent_id`, `action`,`child_id`, `indirect_id`, `datetime`) VALUES (?, ?, NULL, ?, ?)", array($this->id, $action, $indirect_id, $date));
	  }
      }
    else
      {
	if($indirect_id == NULL)
	  {
	    $this->dbpdo->query("INSERT INTO `activity` (`parent_id`, `action`,`child_id`, `indirect_id`,`datetime`) VALUES (?, ?, ?, NULL, ?)", array($this->id, $action, $object_id, $date));
	  }
	else
	  {
	    $this->dbpdo->query("INSERT INTO `activity` (`parent_id`, `action`,`child_id`, `indirect_id`,`datetime`) VALUES (?, ?, ?, ?, ?)", array($this->id, $action, $object_id, $indirect_id, $date));
	  }
      }
  }

  function seo_string($str)
  {
    $str = strtolower($str);
    $str = preg_replace('/[[:punct:]]/','-',$str);
    $str = preg_replace('/\s+/','-',$str);
    return $str;
  }

  function process_text($text)
  {
    if($this->config->use_markdown() == true)
      return Markdown(htmlspecialchars(stripslashes($text)));
    return nl2br(htmlspecialchars(stripslashes($text)));
  }

  function memcache_get($key)
  {
    return $this->memcache->get($key);
  }

  function memcache_set($key, $value)
  {
    $this->memcache->set($key, $value, FALSE, 3600);
  }

  function memcache_delete($key)
  {
    $this->memcache->delete($key);
  }

  function timestamp($format = "Y-m-d H:i:s")
  {
    return date($format);
  }

  function session($key)
  {
    if(isset($_SESSION) && isset($_SESSION[$key]))
      return $_SESSION[$key];
    return NULL;
  }

  function error($arg, $log = false)
  {
    if($log)
      {
	$trace = debug_backtrace();
	$this->log("=====================\n");
	$this->log("ERROR: " . $arg . "\n");
	$this->log(implode('\n',$trace));
	$this->log("=====================\n");
      }
    die($arg);
  }

  function log($msg, $logfile = "log.txt")
  {
    if(!($fh = fopen($logfile, "at")))
      die("<strong>ERROR</strong>: Could not open $logfile for logging.");
    $fwrite($fh, $msg."\n");
    @fclose($fh);
  }

  function display()
  {
    echo " == " . get_class($this) . "<br />";
    foreach($this as $key => $value)
      if(is_object($value) && (is_subclass_of($value,"base") || get_class($value) == "base"))
	{
	  echo "$key...<br>";
	  $value->display();
	}
      else 
	if(!is_object($value))
	  if(is_array($value))
	    {
	      echo "$key => ";
	      print_r($value);
	      echo "<br />";
	    }
	  else
	    echo "$key => $value<br />";
    echo " /// " . get_class($this) . "<br />";;
  }
}

?>