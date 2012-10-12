<?

require_once "lib/init.php";

_check_authentication();

session_destroy();

header("Location: /tomsync/login");

?>
