<?php

define('COOKIE_SESSID','ureddit_sessid');
define('PREFIX','');
define('USE_MARKDOWN','true');
define('SRVDOMAIN','http' . (isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0 ? "s" : "") . '://uofreddit.com');

function render($__template, $__context=null) 
{
  if (is_array($__context))
    {
      foreach ($__context as $__key => $__value)
	{
	  $$__key = $__value;
	}
    }
  
  include($__template);
}

function first_letter_subdir($str)
{
  return $str[0] . '/' . $str;
}

function latest_blog_post($dbpdo)
{
  if(config::wordpress)
    {
      $res = $dbpdo->query("SELECT `post_title`, `post_name`, `post_date` FROM `wp_posts` WHERE `post_status`='publish' AND `post_type`='post' ORDER BY `ID` DESC LIMIT 1", array());
      $year = date("Y", strtotime($res[0]['post_date']));
      $month = date("m", strtotime($res[0]['post_date']));
      $day = date("d", strtotime($res[0]['post_date']));
      return array('title' => $res[0]['post_title'], 'url' => '/blog/' . $year . '/' . $month . '/' . $day . '/' . $res[0]['post_name']);
    }
  else
    return array('title' => "Install WordPress or remove this box.", 'url' => "http://wordpress.com");
}

function latest_commit($dbpdo)
{
  if(config::use_memcache)
    {
      if(!($val = $dbpdo->memcache->get('latest_commit')))
	{
	  $fdata = new SimpleXMLElement(stripslashes(file_get_contents("https://github.com/ureddit/ureddit/commits/master.atom")), true);
	  //$fdata = new SimpleXMLElement(file_get_contents("github.txt"), true);
	  $val = array('title' => '' . $fdata->entry[0]->title, 'url' => '' . $fdata->entry[0]->link[0]['href']);
	  $dbpdo->memcache_set('latest_commit',$val);
	}
    }
  else
    {
      $fdata = new SimpleXMLElement(stripslashes(file_get_contents("https://github.com/ureddit/ureddit/commits/master.atom")), true);
      //$fdata = new SimpleXMLElement(file_get_contents("github.txt"), true);
      $val = array('title' => $fdata->entry[0]->title, 'url' => $fdata->entry[0]->link[0]['href']); 
    }

  return $val;
}

function translate_class_id($dbpdo,$old_id)
{
  $translation = $dbpdo->query("SELECT new_id FROM class_id_translation WHERE old_id = ?", array($old_id));
  if(count($translation) > 0)
    return $translation[0]['new_id'];
  return false;
}

function votebox($class, $user = false)
{
  $score = $class->calculate_score();

  if($user !== false)
    {
      if(isset($user->votes['downvoted']) && in_array($class->id, $user->votes['downvoted']))
	{
	  echo '<img src="' . SRVDOMAIN . PREFIX . '/img/down-filled.png" alt="-1\'d" class="downvoted" onclick="$.post(\'' . PREFIX . '/vote.php\', {action: \'remove\', id: \'' . $class->id . '\'}, function(response) {$(\'#class' . $class->id . ' > .content > .voting\').html(response);})">';
	}
      else
	{
	  echo '<img src="' . SRVDOMAIN . PREFIX . '/img/down.png" alt="-1" class="downvote" onclick="$.post(\'' . PREFIX . '/vote.php\', {action: \'downvote\', id: \'' . $class->id . '\'}, function(response) {$(\'#class' . $class->id . ' > .content > .voting\').html(response);})">';
	}
      if(isset($user->votes['upvoted']) && in_array($class->id, $user->votes['upvoted']))
	{
	  echo '<img src="' . SRVDOMAIN . PREFIX . '/img/up-filled.png" alt="+1\'d" class="upvoted" onclick="$.post(\'' . PREFIX . '/vote.php\', {action: \'remove\', id: \'' . $class->id . '\'}, function(response) {$(\'#class' . $class->id . ' > .content > .voting\').html(response);})">';
	}
      else
	{
	  echo '<img src="' . SRVDOMAIN . PREFIX . '/img/up.png" alt="+1" class="upvote" onclick="$.post(\'' . PREFIX . '/vote.php\', {action: \'upvote\', id: \'' . $class->id . '\'}, function(response) {$(\'#class' . $class->id . ' > .content > .voting\').html(response);})">';
	}
    }
  else
    {
      echo '<a href="' . PREFIX . '/login"><img src="' . SRVDOMAIN . PREFIX . '/img/down.png" alt="-1" class="downvote"></a>';
      echo '<a href="' . PREFIX . '/login"><img src="' . SRVDOMAIN . PREFIX . '/img/up.png" alt="+1" class="upvote"></a>';

    }
  echo $score . '&nbsp;';
}

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
        <a href="<?=PREFIX ?>/login"><button class="button-add"><?=$text[$status] ?></button></a>
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
	      <a onclick="$.get('<?=PREFIX ?>/enroll.php',{id: '<?=$class->id ?>'}, function(data) { $('#button<?=$class->id ?>').html(data) });">
	      <button class="button-add">
	      +add
	      </button>
	      </a>
	    </div>
	    <?php
          } elseif($status == "5") {
            ?>
            <div class="signup-button">
	      <a class="link-signup-button">
	      <button class="button-add">
	      finished
	      </button>
	      </a>
	    </div>
	    <?php
	  } elseif($status == "2" || $status == "4") {
            ?>
            <div class="signup-button">
	      <a class="link-signup-button">
	      <button class="button-add">
	      closed
	      </button>
	      </a>
	    </div>
	    <?php
	  } elseif($status == "0") {
            ?>
            <div class="signup-button">
	      <button class="button-add">
	      canceled
	      </button>
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
  <div class="signup-button">
    <a onclick="$.get('<?=PREFIX ?>/drop_class.php',{id: '<?=$class->id ?>'}, function(data) { $('#button<?=$class->id ?>').html(data) });" class="link-signup-button">
    <button class="button-added">
    enrolled
    </button>
    </a>
  </div>
     <?
    }
  echo "</div>\n";
}

function get_feed($user)
{
  $items = array();
  $actions = $user->dbpdo->query("SELECT * FROM `activity` WHERE `parent_id` = ? ORDER BY `datetime` DESC LIMIT 25", array($user->id));
  foreach($actions as $action)
    {
      if(is_null($action['child_id']))
	$items[] = '<li><strong>' . $user->value . '</strong> ' . $action['action'] . '<br><small><em>' . $action['datetime'] . '</em></small></li>';
      else
	{
	  $object = new object($user->dbpdo, $action['child_id']);
	  $items[] = '<li><strong>' . $user->value . '</strong> ' . $action['action'] . ' <strong>' . $object->value . '</strong><br><small><em>' . $action['datetime'] . '</em></small></li>';
	}
    }
  if(empty($items))
    $items[] = "<em>no user activity found</em>";
  return $items;
}

function display_schedule($user)
{
  $user->get_schedule();

  $categories = array();
  $sorted_categories = array();

  foreach($user->schedule as $class_id)
    {
      $class = new course($user->dbpdo, $class_id);
      if($class->get_attribute_value('status') == '0')
	continue;
      $class->get_categories();
      foreach($class->categories as $category_id)
	$categories[$category_id][] = $class;
    }
  foreach($categories as $category_id => &$classes)
    {
      $category = new category($user->dbpdo, $category_id);
      $category_objects[$category_id] = $category;
      $sorted_categories[$category_id] = $category->value;
    }

  asort($sorted_categories);

  foreach($sorted_categories as $category_id => $category_value)
    {
      ?>
      <div class="category">
	<div class="content">
	<?php
	  echo $category_value;
          foreach($categories[$category_id] as $class)
	    $class->display_with_container();
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
      <div id="category<?=$category->id ?>" class="category">
	<div class="content">
	<?=$category->value ?>
       <?php
	 foreach($classes as $class)
	 $class->display_with_container(true);
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

function latest_reddit_post($dbpdo)
{
  if(config::use_memcache)
    {
      if(!($val = $dbpdo->memcache_get('latest_reddit_post')))
	{
	  $json = json_decode(file_get_contents('/srv/http/ureddit.com/public_html/reddit.json'), true);
	  $val = array('url' => 'http://reddit.com' . $json['data']['children'][0]['data']['permalink'], 'title' => $json['data']['children'][0]['data']['title']);
	  $dbpdo->memcache_set('latest_reddit_post', $val);
	}
    }
  else
    {
      $json = json_decode(file_get_contents('/srv/http/ureddit.com/public_html/reddit.json'), true);
      $val = array('url' => 'http://reddit.com' . $json['data']['children'][0]['data']['permalink'], 'title' => $json['data']['children'][0]['data']['title']);
    }

  return $val;
}

function latest_tweet($dbpdo)
{
  $config = $dbpdo->config;
  if(config::use_memcache)
    {
      if(!($val = $dbpdo->memcache_get('latest_tweet')))
	{
	  try
	    {
	      $t = new Twitter($config::twitterConsumerKey, $config::twitterConsumerSecret, $config::twitterAccessToken, $config::twitterAccessTokenSecret);
	      $latest = $t->load(Twitter::ME,1);
	      $val = array('text' => Twitter::clickable($latest->status->text), 'url' => 'http://twitter.com/uofreddit/status/' . $latest->status->id);
	      $dbpdo->memcache_set('latest_tweet',$val,60);
	    }
	  catch (TwitterException $e)
	    {
	      return array('text' => 'Error fetching tweets. Click to go to the @uofreddit Twitter feed.', 'url' => 'http://twitter.com/uofreddit');
	    }
	}
    }
  else
    {
      try
	{
	  $t = new Twitter($config::twitterConsumerKey, $config::twitterConsumerSecret, $config::twitterAccessToken, $config::twitterAccessTokenSecret);
	  $latest = $t->load(Twitter::ME,1);
	  $val = array('text' => Twitter::clickable($latest->status->text), 'url' => 'http://twitter.com/uofreddit/status/' . $latest->status->id);
	  memcache_set('latest_tweet',$val,300);
	}
      catch (TwitterException $e)
	{
	  return array('text' => 'Error fetching tweets. Click to go to the @uofreddit Twitter feed.', 'url' => 'http://twitter.com/uofreddit');
	}
    }
  return $val;
}

function tweet($config,$status)
{
  $t = new Twitter($config::twitterConsumerKey, $config::twitterConsumerSecret, $config::twitterAccessToken, $config::twitterAccessTokenSecret);
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

function display_messages($user, $offset = 0, $limit=15)
{
    $found = 0;
    $unread = array();
    $user->get_inbox($offset, $limit*2);

    for($i = 0; $i < count($user->inbox)/2; $i++)
	 {
	   if($user->get_object_type($user->inbox[2*$i]['parent_id']) == 'class')
	     {
	       $sender = new user($user->dbpdo, $user->inbox[2*$i]['parent_id']);
	       if($sender->type == 'class')
		 {
		   $author = $sender->dbpdo->query("SELECT value FROM association_attributes WHERE association_id = ? AND type = ?",
						 array(
						       $user->inbox[2*$i]['association_id'],
						       'author'
						       ));
		   $author = new user($sender->dbpdo, $author[0]['value']);
		 }
		 
	       $found = 1;
	     }
	   else
	     {
	       $sender = new user($user->dbpdo, $user->inbox[2*$i]['parent_id']);
	       $found = 1;
	     }
      ?>
      <div class="message">
      <div class="content">
        <div class="subject"><?=$user->inbox[2*$i]['value'] ?></div>
	 <div class="body"><?=$user->process_text($user->inbox[2*$i+1]['value']) ?></div>
	 <div class="signature">from <strong><?=($sender->type == 'class' ? $author->value . '</strong> (regarding class <strong><a href="' . PREFIX . "/class/" . $sender->id . '">' . $sender->value . '</a></strong>)' : $sender->value) . '</strong>'?> at <?=$user->inbox[2*$i]['creation'] ?> <a href="<?=PREFIX ?>/user/<?=($sender->type == 'class' ? $author->value : $sender->value) ?>" class="link-class-desc">[reply]</a></div>
      </div>
	 </div>
      <?php
       }

    if($found == 0)
    {
      ?>
      <div class="message">
        <p><em>you have no new messages</em></p>
      </div>
      <?php
    }
    $user->dbpdo->query("UPDATE `associations` SET `type` = ? WHERE type = ? AND child_id = ?",
			array(
			      'read_mass_message',
			      'unread_mass_message',
			      $user->id
			      ));
    
    $user->dbpdo->query("UPDATE `associations` SET `type` = ? WHERE  type = ? AND child_id = ?",
			array(
			      'read_message',
			      'unread_message',
			      $user->id
			      ));
    
}

function display_sent_messages($user, $offset = 0, $limit=15)
{
    $found = 0;
    $unread = array();
    $user->get_outbox($offset, $limit*2);
    for($i = 0; $i < count($user->outbox)/2; $i++)
	 {
	   $receipient = new user($user->dbpdo, $user->outbox[2*$i]['child_id']);
	   $found = 1;
      ?>
      <div class="message">
      <div class="content">
        <div class="subject"><?=$user->outbox[2*$i]['value'] ?></div>
	 <div class="body"><?=$user->process_text($user->outbox[2*$i+1]['value']) ?></div>
        <div class="signature">to <strong><?=$receipient->value ?></strong> at <?=$user->outbox[$i]['creation'] ?> <a href="<?=PREFIX ?>/user/<?=$recepient->value ?>" class="link-class-desc">[reply]</a></div>
      </div>
      </div>
      <?php
       }

    if($found == 0)
    {
      ?>
      <div class="message">
        <p><em>you have no new messages</em></p>
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

function send_user_to($place,$domain="ureddit.com",$http_code = NULL)
{
  $s = isset($_SERVER['HTTPS']) && strlen($_SERVER['HTTPS']) > 0 ? "s" : "";
  if($http_code !== NULL)
    header("HTTP/1.1 $http_code");
  header("Location: http$s://" . str_replace('universityofreddit','ureddit',$_SERVER['SERVER_NAME']) . PREFIX . $place);
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
  setcookie(COOKIE_SESSID,"",time()-60*60*24);
  
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
  setcookie(COOKIE_SESSID,session_id(),time()-60*60*24);
}

function send_email($from, $to, $subject, $message, $internalid = false)
{

  $fHeaders = "To: " . $to . "\n";
  $fHeaders .= "From: " . $from . "\n";
  
  $fHeaders .= "Subject: " . encode_header ($subject) . "\n";
  $fHeaders .= "MIME-Version: 1.0\n";
  if($internalid !== false)
    $fHeaders .= "Association: $internalid\r\n";
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

function pacrypt ($pw, $salt,$pw_db="")
{
  $pw = stripslashes($pw);
  $password = "";

  //$salt = "uofr!336";
  
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

/*
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
*/

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
  $unread = $dbpdo->query("SELECT COUNT(*) FROM associations WHERE child_id = ? AND (type = ? OR type = ?)",array($user_id, 'unread_message', 'unread_mass_message'));
  return !($unread[0]['COUNT(*)'] == '0');
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