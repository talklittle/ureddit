<header><? $page = pathinfo($_SERVER['PHP_SELF']); $page = $page['basename']; ?>
    <a href="<?=PREFIX ?>/">
      <img src="<?=PREFIX ?>/img/logo.png" id="logo" alt="University of Reddit">
    </a>
     <nav>
        <ul>
           <li>
	     <a href="<?=PREFIX ?>/" <?=$page == 'index.php' ? 'class="active"' : '' ?>>course catalog</a>
           </li>
          <?php
            if(!logged_in())
	      {
		?>
           <li>
              <a href="<?=PREFIX ?>/login" <?=$page == '.php' ? 'class="active"' : '' ?>>login</a> or <a href="<?=PREFIX ?>/register" <?=$page == 'register.php' ? 'class="active"' : '' ?>>register</a>
           </li>
		<?php
	      }
	    else
	      {
		$user = new user($dbpdo, $dbpdo->session('user_id'));
		?>
           <li>
		<a href="<?=PREFIX ?>/user/<?=$dbpdo->session('username') ?>" <?=$page == 'user.php' && $_GET['id'] == $dbpdo->session('username') ? 'class="active"' : '' ?>><?=$dbpdo->session('username') ?></a> <a href="<?=PREFIX ?>/messages"><img id="messages" <?=has_new_messages($dbpdo, $dbpdo->session('user_id')) ? 'src="' . PREFIX . '/img/new_messages.png" alt="new message(s)!"' : 'src="' . PREFIX . '/img/messages.png" alt="messages"' ?></img></a> <small>[<a href="<?=PREFIX ?>/settings" <?=$page == 'preferences.php' ? 'class="active"' : '' ?>>settings</a>]</small>
           </li>
		<?php
	      }
           ?>

          <?php
	    if(logged_in() && $user->is_teacher())
	      {
		?>
           <li>
		<a href="<?=PREFIX ?>/teachers" <?=$page == '.php' && strpos($_SERVER['PHP_SELF'],'teachers') !== false ? 'class="active"' : '' ?>>manage classes</a>
           </li>
		<?php
	      }
	    else
	      {
		?>
           <li>
              <a href="<?=PREFIX ?>/teach" <?=$page == 'teach.php' ? 'class="active"' : '' ?>>teach a class</a>
           </li>
		<?php
	      }
           ?>
           <li>
              <a href="<?=PREFIX ?>/help" <?=$page == 'help.php' ? 'class="active"' : '' ?>>help!</a>
           </li>
        </ul>
     </nav>
	 
  </header>