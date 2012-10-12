<?php
/*
require_once 'lib/user.php';

session_start();
$user = null;

if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated'] == true)) {
	//We know that there is no user
	$user = new User();
	if(!$user->findByUID($_SESSION['user_id'])) {
		header("Location: /login?goto=".urlencode($_SERVER['REQUEST_URI']));
		die;
	}
} else if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == false) {
	header("Location: /login?goto=".urlencode($_SERVER['REQUEST_URI']));
	die;
}

$DB_DSN = "mysql://authing:OAuthBites@localhost/oauth";
$info = parse_url($DB_DSN);
($GLOBALS['db_conn'] = mysql_connect($info['host'], $info['user'], $info['pass'])) || die(mysql_error());
mysql_select_db(basename($info['path']), $GLOBALS['db_conn']) || die(mysql_error());
unset($info);

require_once '/var/www/oauth/library/OAuthStore.php';
$store = OAuthStore::instance('MySQL', array('conn' => $GLOBALS['db_conn']));
 */

require_once 'lib/init.php';
require_once 'lib/oauth-web-utils.php';

_check_authentication();

?>

<html>
  <head>
    <title>User OAuth Dashboard</title>
    <link rel="stylesheet" type="text/css" href="/tomsync/css/tabs.css">
    <link rel="stylesheet" type="text/css" href="/tomsync/css/style.css">
    <script type="text/javascript" src="/tomsync/js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="/tomsync/js/script.js"></script>
  </head>
  <body>
  <div class="commands">
    <a href="/tomsync/logout">Logout</a>
  </div>
  <div id="tabs">
    <ul class="tablist">
      <li><a href="#token">My Consumer Tokens</a></li>
      <li><a href="#all-consumers">All Consumers</a></li>
      <li><a href="#my-consumers">My Consumers</a></li>
    </ul>
  </div>
  <div class="tab-wrapper">
    <div class="tabs" id="token">
<?php
$ctokens = $store->listConsumerTokens($user->getUID());
if(count($ctokens) < 1) {
	echo "<h2>You have no active consumer tokens</h2>\n";
} else {
	arrayofarrays2table($ctokens, 'token');
}
?>
    </div>
    <div class="tabs" id="all-consumers">
<?php
  $aconsumers = $store->listConsumerApplications();
  if(count($aconsumers) < 1) {
	  echo "<h2>There are currently no consumers registered on this system</h2>\n";
  } else {
	  arrayofarrays2table($aconsumers, 'all-consumers');
  }
?>
    </div>
    <div class="tabs" id="my-consumers">
<?php
$consumers = $store->listConsumers($user->getUID());
if(count($consumers) < 1) {
	echo "<h2>You have no registered consumers</h2>\n";
} else {
	arrayofarrays2table($consumers, 'my-consumers');
}
?>
    </div>
    <div class="tabs" id="formtab">
      <form name="form_consumer" method="post">
        <div class="input-label">Consumer Key:</div>
        <div class="input-text">
    	  <input type="text" name="consumer-key">
        </div>
        <div class="input-label">Consumer Secret:</div>
        <div class="input-text"><input type="text" name="consumer-secret"></div>
        <div class="input-label">Server URI:</div>
        <div class="input-text"><input type="text" name="server-uri"></div>
        <div class="input-text">Signature Method</div>
        <input type="radio" name="sig-method" value="HMAC-SHA1" checked>HMAC-SHA1<br />
        <input type="radio" name="sig-method" value="PLAINTEXT">PLAINTEXT<br />
        <div class="input-label">Request URI:</div>
        <div class="input-text"><input type="text" name="request-uri"></div>
        <div class="input-label">Authorize URI:</div>
        <div class="input-text"><input type="text" name="authorize-uri"></div>
        <div class="input-label">Access URI:</div>
        <div class="input-text"><input type="text" name="access-uri"></div>
      </form>
    </div>
  </div>
  </body>
</html>
