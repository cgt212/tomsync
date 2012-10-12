<?

require_once 'user.php';
require_once '../config/config.php';

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

$db_conn = mysqli_connect(Config::$db_host, Config::$db_user, Config::$db_pass) || die(mysqli_error());

require_once 'oauth/OAuthStore.php';
$store = OAuthStore::instance('MySQL', array('conn' => $db_conn));

require_once 'oauth/OAuthServer.php';
$server = new OAuthServer();

?>
