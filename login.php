<?php

/*
 * Login web page for TomSync
 *
 * Copyright 2012 Chris Tsonis <cgt212@whatbroke.com>
 *
 * This file is part of TomSync.
 *
 * TomSync is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TomSync is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TomSync.  If not, see <http://www.gnu.org/licenses/>.
 */

require_once 'config/siteconfig.php';
require_once 'lib/logger.php';

session_start();

$title = "Tomboy Sync Login";

if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated'] == true) &&
   isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
	require_once 'lib/user.php';
	$user = new User();
	if(!$user->findByUID($_SESSION['user_id'])) {
		unset($_SESSION['user_id']);
		unset($_SESSION['authenticated']);
		unset($_SESSION['username']);
		$uri = SiteConfig::$url_root_dir."/login";
		if(!empty($_REQUEST['goto'])) {
			$uri .= "?goto=".urlencode($_REQUEST['goto']);
		}
		header("Location: $uri");
		die;
	}
	if(!empty($_REQUEST['goto'])) {
		header("Location: .".$_REQUEST['goto']);
	} else {
		header('Location: '.SiteConfig::$url_root_dir.'/dashboard');
	}
	die;
}

if(isset($_POST['username']) && isset($_POST['password'])) {
	require_once 'lib/user.php';
	$user = new User();

	if($user->authenticate($_POST['username'], $_POST['password'])) {
		$_SESSION['authenticated'] = true;
		$_SESSION['user_id'] = $user->getUID();
		$_SESSION['username'] = $_POST['username'];
		if(!empty($_REQUEST['goto'])) {
			header('Location: '.$_REQUEST['goto']);
		} else {
			header('Location: '.SiteConfig::$url_root_dir.'/dashboard');
		}
		die;
	} else {
		$error = "Invalid username/password";
	}
}

?>

<HTML>
  <head>
    <title><? echo $title." Page" ?></title>
    <link rel="stylesheet" type="text/css" href="<?= SiteConfig::$url_root_dir ?>/css/login.css" />
  </head>
  <body>
    <div class="left-column"></div>
    <div class="login-container">
      <div class="login-container-title"><h2><? echo $title ?></h2></div><br />
      <? if(!empty($error)) { echo "<div class=\"div-error\">$error</div><br />\n"; } ?>
      <div class="input-container">
        <form action="login" method="post">
	  <div class="login-inputs">
	    <input class="login-input" id="uname" type="text" name="username"><br />
	    <input class="login-input" id="passwd" type="password" name="password">
<?
if(!empty($_REQUEST['goto'])) {
	echo "<input type=\"hidden\" name=\"goto\" value=\"".$_REQUEST['goto']."\">\n";
}
?>
	  </div>
	  <div class="input-buttons">
	    <input class="login-button" id="submit" type="submit" name="submit" value="Login">
	  </div>
	</form>
      </div>
    </div>
  </body>
</html>
