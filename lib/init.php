<?

require_once 'user.php';
require_once 'logger.php';
require_once __DIR__.'/../config/siteconfig.php';

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

$db_conn = mysql_connect(SiteConfig::$db_host, SiteConfig::$db_user, SiteConfig::$db_pass);
Logger::log(print_r($db_conn, true));
if(!$db_conn) {
	Logger::log("DB error: ".mysql_error());
	die;
}
mysql_select_db(SiteConfig::$db_name, $db_conn);

require_once 'oauth/OAuthStore.php';
$store = OAuthStore::instance('MySQL', array('conn' => $db_conn));

require_once 'oauth/OAuthServer.php';
$server = new OAuthServer();

?>
