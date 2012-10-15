<?

require_once "lib/init.php";

_check_authentication();

session_destroy();

header("Location: ".SiteConfig::$url_root_dir."/login");

?>
