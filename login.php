<?php

session_start();

$title = "Tomboy Sync Login";

Logger::log("Session Data: ".print_r($_SESSION, true), LOG_DEBUG);

if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated'] == true) &&
   isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
	require_once 'lib/user.php';
	$user = new User();
	if(!$user->findByUID($_SESSION['user_id'])) {
		unset($_SESSION['user_id']);
		unset($_SESSION['authenticated']);
		unset($_SESSION['username']);
		$uri = "/tomsync/login";
		if(!empty($_REQUEST['goto'])) {
			$uri .= "?goto=".urlencode($_REQUEST['goto']);
		}
		header("Location: $uri");
		die;
	}
	if(!empty($_REQUEST['goto'])) {
		header("Location: .".$_REQUEST['goto']);
	} else {
		header('Location: /tomsync/dashboard');
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
			header('Location: '.'/tomsync/dashboard');
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
    <link rel="stylesheet" type="text/css" href="/tomsync/css/login.css" />
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
