<?

require_once 'user.php';

session_start();
$user = null;

function _check_authentication() {
	global $user;
	$user = false;

	if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated'] == true)) {
		$user = new User();
		if(!$user->findByUID($_SESSION['user_id'])) {
			header("Location: /tomsync/login?goto=".urlencode($_SERVER['REQUEST_URI']));
			die;
		}
	} else if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == false) {
		header("Location: /tomsync/login?goto=".urlencode($_SERVER['REQUEST_URI']));
		die;
	}
	return $user;
}

$DB_DSN = "mysql://tomboy:tomboyIncremental@localhost/tomsync";
$info = parse_url($DB_DSN);
($GLOBALS['db_conn'] = mysql_connect($info['host'], $info['user'], $info['pass'])) || die(mysql_error());
mysql_select_db(basename($info['path']), $GLOBALS['db_conn']) || die(mysql_error());
unset($info);

require_once 'oauth/OAuthStore.php';
$store = OAuthStore::instance('MySQL', array('conn' => $GLOBALS['db_conn']));

require_once 'oauth/OAuthServer.php';
$server = new OAuthServer();

?>
