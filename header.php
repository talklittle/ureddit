<header><? $page = pathinfo($_SERVER['PHP_SELF']); $page = $page['basename']; ?>
    <a href="<?=PREFIX ?>/">
      <img src="<?=PREFIX ?>/img/logo.png" id="logo" alt="University of Reddit">
    </a>
     <nav <?=!logged_in() ? '' : 'style="font-size: 0.8em;"' ?>>
        <ul>
           <li>
						     <a href="<?=PREFIX ?>/" <?=$page == 'index.php' && strpos($_SERVER['REQUEST_URI'],"teachers") === false ? 'class="active"' : '' ?>>course catalog</a>
           </li>
          <?php
            if(!logged_in())
	      {
		?>
           <li>
              <a href="<?=PREFIX ?>/login" <?=$page == 'login.php' ? 'class="active"' : '' ?>>login</a> <small><em>or</em></small> <a href="<?=PREFIX ?>/register" <?=$page == 'register.php' ? 'class="active"' : '' ?>>register</a>
           </li>
		<?php
	      }
	    else
	      {
		$user = new user($dbpdo, $dbpdo->session('user_id'));
		?>
           <li>
		<a href="<?=PREFIX ?>/user/<?=$dbpdo->session('username') ?>" <?=$page == 'user.php' && $_GET['id'] == $dbpdo->session('username') ? 'class="active"' : '' ?>><?=$dbpdo->session('username') ?></a> <a href="<?=PREFIX ?>/messages"><img id="messages" <?=has_new_messages($dbpdo, $dbpdo->session('user_id')) ? 'src="' . PREFIX . '/img/new_messages.png" alt="new message(s)!"' : 'src="' . PREFIX . '/img/messages.png" alt="messages"' ?></img></a>
           </li>
           <li>
                <a href="<?=PREFIX ?>/settings" <?=$page == 'preferences.php' ? 'class="active"' : '' ?>>settings</a> - <a href="<?=PREFIX ?>/logout">log out<a>
           </li>
		<?php
	      }
           ?>

          <?php
	    if(logged_in() && $user->is_teacher())
	      {
		?>
           <li>
		<a href="<?=PREFIX ?>/teachers" <?=$page == 'index.php' && strpos($_SERVER['REQUEST_URI'],'teachers') !== false ? 'class="active"' : '' ?>>manage classes</a>
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