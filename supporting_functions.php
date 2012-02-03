<?php

define('COOKIE_SESSID','ureddit_sessid');
define('PREFIX','/dev');
define('USE_MARKDOWN','true');

function signup_button($user, $class_id)
{

/*
class statuses:

0 cancelled
1 has not begun, open for signups
2 has not begun, closed to signups
3 running, open for signups
4 running, closed to signups
5 finished
*/
  if($user === false)
    $class = new course($user, $class_id);
  else
    $class = new course($user->dbpdo, $class_id);
  $status = $class->get_attribute_value('status');
  echo "<div id=\"button" . $class->id . "\">\n";

  if(!logged_in())
    {
      $text = array("0" => "canceled", "1" => "+add", "2" => "closed", "3" => "+add", "4" => "closed", "5" => "finished");
      ?>
      <div class="signup-button">
        <a href="<?=PREFIX ?>/login" class="link-signup-button"><?=$text[$status] ?></a>
      </div></div>
      <?php
      return;
    }

  if(!$user->is_taking_class($class->id)) // if student is not in class
    {
      if(!$user->is_teaching_class($class->id))
	{
          if($status == "1" || $status == "3")
          {
            ?>
            <div class="signup-button">
	      <a onclick="$.get('<?=PREFIX ?>/enroll.php',{id: '<?=$class->id ?>'}, function(data) { $('#button<?=$class->id ?>').html(data) });" class="link-signup-button">
	      +add
	      </a>
	    </div>
	    <?php
          } elseif($status == "5") {
            ?>
            <div class="signup-button">
	      <a class="link-signup-button">
	      finished
	      </a>
	    </div>
	    <?php
          } else {
            ?>
            <div class="signup-button">
	      <a class="link-signup-button">
	      closed
	      </a>
	    </div>
	    <?php
          }
	}
      else
	{
	  ?>
	  <div class="teacher-button">
	    <a href="<?=PREFIX ?>/teachers/" class="link-signup-button">
	    teacher
	    </a>
	  </div>
	  <?php 
	}
    }
  else
    {
      ?>
  <div class="deregister-button">
    <a onclick="$.get('<?=PREFIX ?>/drop_class.php',{id: '<?=$class->id ?>'}, function(data) { $('#button<?=$class->id ?>').html(data) });" class="link-signup-button">
    -drop
    </a>
  </div>
     <?
    }
  echo "</div>\n";
}

function display_schedule($user)
{
  $user->get_schedule();
  $categories = array();

  foreach($user->schedule as $class_id)
    {
      $class = new course($user->dbpdo, $class_id);
      $class->get_categories();
      foreach($class->categories as $category_id)
	$categories[$category_id][] = $class;
    }
  foreach($categories as $category_id => $classes)
    {
      $category = new category($user->dbpdo, $category_id);
      ?>
      <div id="category<?=$cat->id ?>">
      <div class="category">
	<div class="category-name">
	<?=$category->value ?>
	</div>
       <?php
	 foreach($classes as $class)
	 $class->display();
      ?>
      </div>
      </div>
      <?php
    }
}

function list_teacher_classes($user)
{
  $user->get_taught_classes();
  $categories = array();
  foreach($user->teaching as $class_id)
    {
      $class = new course($user->dbpdo, $class_id);
      $class->get_categories();
      foreach($class->categories as $category_id)
	$categories[$category_id][] = $class;
    }
  foreach($categories as $category_id => $classes)
    {
      $category = new category($user->dbpdo, $category_id);
  ?>
      <div id="category<?=$cat->id ?>">
      <div class="category">
	<div class="category-name">
	<?=$category->value ?>
	</div>
       <?php
	 foreach($classes as $class)
	 $class->display();
      ?>
      </div>
      </div>
      <?php
    }
}

function object_type_value_to_id($dbpdo, $type, $value)
{
  return $dbpdo->query("SELECT `id` FROM `objects` WHERE `type` = ? AND `value` = ?",
			   array(
				 $type,
				 $value
				 ));
}

function tweet($config,$status)
{
  return;
  $t = new Twitter($config->twitterConsumerKey, $config->twitterConsumerSecret, $config->twitterAccessToken, $config->twitterAccessTokenSecret);
  $t->send($status);
}

function category_dropdown($dbpdo, $name, $selected_val = "")
{
  $categories = $dbpdo->query("SELECT * FROM `objects` WHERE `type` = ? ORDER BY `value` ASC", array('category'));
  ?><select name="<?=$name ?>" class="teach"><?php
  foreach($categories as $cat)
  {
    ?><option <?=($cat['id'] == $selected_val ? "SELECTED" : "") ?> value="<?=$cat['id'] ?>"><?=$cat['value'] ?></option><?php
  }
  ?></select><?
}

function num_sent_messages($user)
{
  $sent = $user->dbpdo->query("SELECT COUNT(*) FROM associations WHERE type = ? AND parent_id = ?", array('message','$user->id'));
  return $sent[0]['COUNT(*)'];
}

function num_messages($user)
{
  $num = $user->dbpdo->query("SELECT COUNT(*) FROM associations WHERE type = ? AND child_id = ?", array('message','$user->id'));
  return $num[0]['COUNT(*)'];
}

// this function is not original, it was found online
// I have lost my record of who the author was; if found,
// I will give credit
function encrypt($toEncrypt,$privatekey)
{
  $priv = openssl_pkey_get_private ($privatekey);

  $toEncrypt = unpack('H*', $toEncrypt);
  $toEncrypt = $toEncrypt[1];

  $result = "";

  while(strlen($toEncrypt)%16 != 0){
    $toEncrypt .= "00";
  }

  $iv = "1234567812345678";
  for($i = 0; $i < strlen($toEncrypt); $i+=16){
    $p = substr($toEncrypt, $i, 16);
    $x = $p ^ $iv;

    if(!openssl_private_encrypt($x, $e, $priv, OPENSSL_NO_PADDING)){
      throw new Exception(openssl_error_string());
    }

    $iv = $e ^ $p;

    $result .= $e;
  }

  $result = unpack('H*', $result);
  return $result[1];
}

function post($name, $default = "")
{
  if(!empty($_POST) && isset($_POST[$name]))
    return htmlspecialchars(stripslashes($_POST[$name]));
  return htmlspecialchars(stripslashes($default));
}

function display_messages($user, $offset = 0, $limit=1)
{
    $found = 0;
    $unread = array();
    $user->get_inbox($offset, $limit);
    arsort($user->inbox);
    ?><div class="category">
    <div class="category-name">Unread Messages</div><?php
       foreach($user->inbox as $association_id)
       {
	 $read = false;
	 $association = $user->dbpdo->query("SELECT * FROM associations WHERE id = ?", array($association_id));
	 $association_attributes = $user->dbpdo->query("SELECT * FROM association_attributes WHERE association_id = ?", array($association_id));
	 $sender = new user($user->dbpdo, $association[0]['parent_id']);
	 
	 foreach($association_attributes as $attr)
	   {
	     switch($attr['type'])
	       {
	       case "subject":
		 $subject = $attr['value'];
		 break;
	       case "body":
		 $body = $attr['value'];
		 break;
	       case "read":
		 $read = true;
		 break;
	       }
	   }
	 if($read)
	   continue;
	 $unread[] = $association_id;
	 $found = 1;
      ?>
      <div class="class">
        <div class="class-name"><?=$subject ?></div>
        <div class="class-desc"><?=$body ?></div>
        <div class="class-info-noindent">from <strong><?=$sender->value ?></strong> at <?=$association[0]['creation'] ?> [<a href="<?=PREFIX ?>/user/<?=$sender->value ?>" class="link-class-desc">reply</a>]</div>
      </div>
      <?php
       }

    if($found == 0)
    {
      ?>
      <div class="class-white">
        <div class="class-desc"><em>you have no new messages</em></div>
      </div>
      <?php
    }
    $found = 0;

    ?></div>
    <div class="category">
    <div class="category-name">Read Messages</div><?php
       foreach($user->inbox as $association_id)
       {
	 $read = false;
	 $association = $user->dbpdo->query("SELECT * FROM associations WHERE id = ?", array($association_id));
	 $association_attributes = $user->dbpdo->query("SELECT * FROM association_attributes WHERE association_id = ?", array($association_id));
	 $sender = new user($user->dbpdo, $association[0]['parent_id']);
	 
	 foreach($association_attributes as $attr)
	   {
	     switch($attr['type'])
	       {
	       case "subject":
		 $subject = $attr['value'];
		 break;
	       case "body":
		 $body = $attr['value'];
		 break;
	       case "read":
		 $read = true;
		 break;
	       }
	   }
	 if(!$read)
	   continue;
      $found = 1;
      ?>
      <div class="class">
        <div class="class-name"><?=$subject ?></div>
        <div class="class-desc"><?=$body ?></div>
        <div class="class-info-noindent">from <strong><?=$sender->value ?></strong> at <?=$association[0]['creation'] ?> [<a href="<?=PREFIX ?>/user/<?=$sender->value ?>" class="link-class-desc">reply</a>]</div>
      </div>
      <?php
       }
    if($found == 0)
    {
      ?>
      <div class="class-white">
        <div class="class-desc"><em>you have no messages</em></div>
      </div>
      <?php
    }
    ?></div>
    </div><?php
	$date = $user->timestamp();
	foreach($unread as $id)
	  {
	    $user->dbpdo->query("DELETE FROM association_attributes WHERE association_id = ? AND type = ? AND value = ?",
			      array(
				    $id,
				    'unread',
				    'true'
				    ));
	    $user->dbpdo->query("INSERT INTO association_attributes (association_id, type, value, ring, creation, modification) VALUES (?, ?, ?, ?, ?, ?)",
			      array(
				    $id,
				    "read",
				    "true",
				    0,
				    $date,
				    $date
				    ));
	  }
}

function display_sent_messages($user, $offset = 0, $limit=1) {
  $found = 0;
    ?><div class="category">
    <div class="category-name">Sent Messages</div><?php
       $user->get_outbox($offset, $limit);
  arsort($user->outbox);
  foreach($user->outbox as $association_id)
    {
      $association = $user->dbpdo->query("SELECT * FROM associations WHERE id = ?", array($association_id));
      $association_attributes = $user->dbpdo->query("SELECT * FROM association_attributes WHERE association_id = ?", array($association_id));
      $recepient = new user($user->dbpdo, $association[0]['child_id']);

      foreach($association_attributes as $attr)
	{
	  switch($attr['type'])
	    {
	    case "subject":
	      $subject = $attr['value'];
	      break;
	    case "body":
	      $body = $attr['value'];
	      break;
	    }
	}
      $found = 1;
      ?>
      <div class="class">
        <div class="class-name"><?=$subject ?></div>
        <div class="class-desc"><?=$body ?></div>
        <div class="class-info-noindent">
          to <strong><?=$recepient->value ?></strong> at <?=$association[0]['creation'] ?> [<a href="<?=PREFIX ?>/user/<?=$receipient->value ?>" class="link-class-desc">send another</a>]
        </div>
      </div>
      <?php
    }

    if($found == 0)
    {
      ?>
      <div class="class-white">
        <div class="class-desc"><em>you haven<?="'"?>t sent any messages yet</em></div>
      </div>
      <?php
    }
}

function generate_random_password()
{
    $pass = "";
    for($i = 0; $i < 16; $i++)
      $pass .= chr((rand(0,5) < 5 ? (int) rand(65,90) : (int)rand(97,122)));
    return $pass;
}

function send_user_to($place,$domain="ureddit.com",$msg)
{
  $s = isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0 ? "s" : "";
  header("Location: http$s://" . $domain .PREFIX . $place);
  die();
}

function process($text) 
{
  if(USE_MARKDOWN == "true")
    return Markdown(htmlspecialchars(stripslashes($text)));
  return nl2br(htmlspecialchars(stripslashes($text)));
}

function logged_in()
{
  if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] != "true")
    return false;
  return true;
}

function logout($dbpdo)
{
  $dbpdo->query("DELETE FROM `sessions` WHERE `object_id` = ?", array($dbpdo->session('user_id')));
  setcookie('ureddit_sessid',"",time()-60*60*24);
  
  $_SESSION = array();
  session_destroy();
  send_user_to("/");
}

function cookie_exists()
{
   if(isset($_COOKIE[COOKIE_SESSID]))
     return true;
   return false;
}

function login($user)
{
  $_SESSION['logged_in'] = "true";
  $_SESSION['user_id'] = $user->id;
  $_SESSION['username'] = $user->value;
}

function send_email($from, $to, $subject, $message)
{

  $fHeaders = "To: " . $to . "\n";
  $fHeaders .= "From: " . $from . "\n";
  
  $fHeaders .= "Subject: " . encode_header ($subject) . "\n";
  $fHeaders .= "MIME-Version: 1.0\n";
  //$fHeaders .= "Content-Type: text/plain; charset=utf-8\n";
  //$fHeaders .= "Content-Transfer-Encoding: 8bit\n";
  $fHeaders .= "Content-Type: text/html; charset=utf-8\r\n";

  $fHeaders .= "<html><body>\n";
  $fHeaders .= $message;
  $fHeaders .= "</body></html>\n";
  
  $errno = "0";
  $errstr = "0";
  $fh = @fsockopen ('localhost', '25', $errno, $errstr, '30');
  if ($fh)
    {
      $res = smtp_get_response($fh);
      fputs ($fh, "EHLO localhost\r\n");
      $res = smtp_get_response($fh);
      fputs ($fh, "MAIL FROM:<$from>\r\n");
      $res = smtp_get_response($fh);
      fputs ($fh, "RCPT TO:<$to>\r\n");
      $res = smtp_get_response($fh);
      fputs ($fh, "DATA\r\n");
      $res = smtp_get_response($fh);
      fputs ($fh, "$fHeaders\r\n.\r\n");
      $res = smtp_get_response($fh);
      fputs ($fh, "QUIT\r\n");
      $res = smtp_get_response($fh);
      fclose ($fh);
      return true;
    }
  else 
    return false;
}

function encode_header($string, $default_charset = "utf-8")
{
    if (strtolower ($default_charset) == 'iso-8859-1')
    {
        $string = str_replace ("\240",' ',$string);
    }

    $j = strlen ($string);
    $max_l = 75 - strlen ($default_charset) - 7;
    $aRet = array ();
    $ret = '';
    $iEncStart = $enc_init = false;
    $cur_l = $iOffset = 0;

    for ($i = 0; $i < $j; ++$i)
    {
        switch ($string{$i})
        {
        case '=':
        case '<':
        case '>':
        case ',':
        case '?':
        case '_':
            if ($iEncStart === false)
            {
                $iEncStart = $i;
            }
            $cur_l+=3;
            if ($cur_l > ($max_l-2))
            {
                $aRet[] = substr ($string,$iOffset,$iEncStart-$iOffset);
                $aRet[] = "=?$default_charset?Q?$ret?=";
                $iOffset = $i;
                $cur_l = 0;
                $ret = '';
                $iEncStart = false;
            }
            else
            {
                $ret .= sprintf ("=%02X",ord($string{$i}));
            }
            break;
        case '(':
        case ')':
            if ($iEncStart !== false)
            {
                $aRet[] = substr ($string,$iOffset,$iEncStart-$iOffset);
                $aRet[] = "=?$default_charset?Q?$ret?=";
                $iOffset = $i;
                $cur_l = 0;
                $ret = '';
                $iEncStart = false;
            }
            break;
        case ' ':
            if ($iEncStart !== false)
            {
                $cur_l++;
                if ($cur_l > $max_l)
                {
                    $aRet[] = substr ($string,$iOffset,$iEncStart-$iOffset);
                    $aRet[] = "=?$default_charset?Q?$ret?=";
                    $iOffset = $i;
                    $cur_l = 0;
                    $ret = '';
                    $iEncStart = false;
                }
                else
                {
                    $ret .= '_';
                }
            }
            break;
        default:
            $k = ord ($string{$i});
            if ($k > 126)
            {
                if ($iEncStart === false)
                {
                    // do not start encoding in the middle of a string, also take the rest of the word.
                    $sLeadString = substr ($string,0,$i);
                    $aLeadString = explode (' ',$sLeadString);
                    $sToBeEncoded = array_pop ($aLeadString);
                    $iEncStart = $i - strlen ($sToBeEncoded);
                    $ret .= $sToBeEncoded;
                    $cur_l += strlen ($sToBeEncoded);
                }
                $cur_l += 3;
                // first we add the encoded string that reached it's max size
                if ($cur_l > ($max_l-2))
                {
                    $aRet[] = substr ($string,$iOffset,$iEncStart-$iOffset);
                    $aRet[] = "=?$default_charset?Q?$ret?= ";
                    $cur_l = 3;
                    $ret = '';
                    $iOffset = $i;
                    $iEncStart = $i;
                }
                $enc_init = true;
                $ret .= sprintf ("=%02X", $k);
            }
            else
            {
                if ($iEncStart !== false)
                {
                    $cur_l++;
                    if ($cur_l > $max_l)
                    {
                        $aRet[] = substr ($string,$iOffset,$iEncStart-$iOffset);
                        $aRet[] = "=?$default_charset?Q?$ret?=";
                        $iEncStart = false;
                        $iOffset = $i;
                        $cur_l = 0;
                        $ret = '';
                    }
                    else
                    {
                        $ret .= $string{$i};
                    }
                }
            }
            break;
        }
    }
    if ($enc_init)
    {
        if ($iEncStart !== false)
        {
            $aRet[] = substr ($string,$iOffset,$iEncStart-$iOffset);
            $aRet[] = "=?$default_charset?Q?$ret?=";
        }
        else
        {
            $aRet[] = substr ($string,$iOffset);
        }
        $string = implode ('',$aRet);
    }
    return $string;
}

function pacrypt ($pw, $pw_db="")
{
  $pw = stripslashes($pw);
  $password = "";
  $salt = "uofr!336";
  
  $split_salt = preg_split ('/\$/', $pw_db);
  if (isset ($split_salt[2])) {
    $salt = $split_salt[2];
  }
  $password = md5crypt ($pw, $salt);
  $password = escape_string ($password);
  return $password;
}

function escape_string ($string)
{
  // if the string is actually an array, do a recursive cleaning.
  // Note, the array keys are not cleaned.
  if(is_array($string)) {
    $clean = array();
    foreach(array_keys($string) as $row) {
      $clean[$row] = escape_string($string[$row]);  
    }
    return $clean;
  }
  if (get_magic_quotes_gpc ())
    {
      $string = stripslashes($string);
    }
  if (!is_numeric($string))
    {
      $escaped_string = ($string);
    }
  else
    {
      $escaped_string = $string;
    }
  return $escaped_string;
}

function md5crypt ($pw, $salt="", $magic="")
{
    $MAGIC = "$1$";

    if ($magic == "") $magic = $MAGIC;
    $slist = explode ("$", $salt);
    if ($slist[0] == "1") $salt = $slist[1];

    $salt = substr ($salt, 0, 8);
    $ctx = $pw . $magic . $salt;
    $final = hex2bin (md5 ($pw . $salt . $pw));

    for ($i=strlen ($pw); $i>0; $i-=16)
    {
        if ($i > 16)
        {
            $ctx .= substr ($final,0,16);
        }
        else
        {
            $ctx .= substr ($final,0,$i);
        }
    }
    $i = strlen ($pw);

    while ($i > 0)
    {
        if ($i & 1) $ctx .= chr (0);
        else $ctx .= $pw[0];
        $i = $i >> 1;
    }
    $final = hex2bin (md5 ($ctx));

    for ($i=0;$i<1000;$i++)
    {
        $ctx1 = "";
        if ($i & 1)
        {
            $ctx1 .= $pw;
        }
        else
        {
            $ctx1 .= substr ($final,0,16);
        }
        if ($i % 3) $ctx1 .= $salt;
        if ($i % 7) $ctx1 .= $pw;
        if ($i & 1)
        {
            $ctx1 .= substr ($final,0,16);
        }
        else
        {
            $ctx1 .= $pw;
        }
        $final = hex2bin (md5 ($ctx1));
    }
    $passwd = "";
    $passwd .= to64 (((ord ($final[0]) << 16) | (ord ($final[6]) << 8) | (ord ($final[12]))), 4);
    $passwd .= to64 (((ord ($final[1]) << 16) | (ord ($final[7]) << 8) | (ord ($final[13]))), 4);
    $passwd .= to64 (((ord ($final[2]) << 16) | (ord ($final[8]) << 8) | (ord ($final[14]))), 4);
    $passwd .= to64 (((ord ($final[3]) << 16) | (ord ($final[9]) << 8) | (ord ($final[15]))), 4);
    $passwd .= to64 (((ord ($final[4]) << 16) | (ord ($final[10]) << 8) | (ord ($final[5]))), 4);
    $passwd .= to64 (ord ($final[11]), 2);
    return "$magic$salt\$$passwd";
}

function hex2bin ($str)
{
    $len = strlen ($str);
    $nstr = "";
    for ($i=0;$i<$len;$i+=2)
    {
        $num = sscanf (substr ($str,$i,2), "%x");
        $nstr.=chr ($num[0]);
    }
    return $nstr;
}

function to64 ($v, $n)
{
  $ITOA64 = "./0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
  $ret = "";
  while (($n - 1) >= 0)
    {
      $n--;
      $ret .= $ITOA64[$v & 0x3f];
      $v = $v >> 6;
    }
  return $ret;
}

function has_new_messages($dbpdo, $user_id)
{
  $unread = $dbpdo->query("SELECT * FROM associations AS a INNER JOIN association_attributes AS aa ON aa.type = 'unread' AND aa.association_id = a.id AND a.child_id = ?", array($user_id));
  return (bool) count($unread);
}

function smtp_get_response ($fh)
{
  $res ='';
  do
    {
      $line = fgets($fh, 256);
      $res .= $line;
    }
  while (preg_match("/^\d\d\d\-/", $line));
  return $res;
}

?>