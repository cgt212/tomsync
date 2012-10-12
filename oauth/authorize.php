<?

require_once '../lib/init.php';

_check_authentication();

try { 
	$rs = $server->authorizeVerify();
	if($user == null) {
		$user = new User();
		error_log("RS");
		error_log(print_r($rs, true));
		if(!$user->findByUID($store->getUIDfromToken($rs['token']))) {
			error_log("Unable to find user");
			$uri = "/tomsync/login";
			if(!empty($_REQUEST['goto']))
				$uri .= "?goto=".urlencode($_POST['goto']);
			header("Location: ".$uri);
			die;
		}
	}
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		error_log("Post Data: ".print_r($_POST, true));
		$authorized = ($_POST['answer_button'] == "Allow") ? true : false;
		$server->authorizeFinish($authorized, $user->getUID());
	}
} catch(OAuthException $oe) {
	error_log("OAuth Exception: $oe");
	header("Location: /tomsync/login");
	die;
}

?>

<html>
  <head>
    <title>OAuth Permission</title>
  </head>
  <body>
    Would you like to authorize <span class="application-name"><?php echo $rs['application_title']; ?></span> 
    to access your data on this server?<br />
    <form name="form-buttons" method="post">
      <input type="submit" name="answer_button" value="Deny">
      <input type="submit" name="answer_button" value="Allow">
    </form>
  </body>
</html>
