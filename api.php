<?php

//require_once '/var/www/oauth/example/server/core/init.php';
require_once 'lib/init.php';

function _path_splitter() {
	$path = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	$ret = array();
	foreach($path as $arg) {
		if($arg != '') {
			array_push($ret, $arg);
		}
	}
	return $ret;
}

//Check OAuth validation here so that it is covered for the rest of the script
$user = null;
$server = new OAuthServer();
$store = OAuthStore::instance("MySQL", array('conn' => $GLOBALS['db_conn']));

if(OAuthRequestVerifier::requestIsSigned()) {
	try {
		$user_id = $server->verify();
		$user = new User();
		if(!$user->findByUID($user_id)) {
			error_log("There was an error locating the user with ID: $user_id\n");
			header('HTTP/1.1 401 Unauthorized');
			header('WWW-Authenticate: OAuth realm=""');
			header('Content-Type: text/plain; charset=utf8');
			echo "Unkown user";
			exit;
		}
	} catch(OAuthException2 $e) {
		//There is a problem with the OAuth Signature
		error_log("API  Request: ".$e->getMessage()."\n");
		header('HTTP/1.1 401 Unauthorized');
		header('WWW-Authenticate: OAuth realm=""');
		header('Content-Type: text/plain; charset=utf8');
		echo $e->getMessage();
		exit;
	}
}

$path = _path_splitter();
$depth = count($path);
error_log(print_r($path, true));
$start = 0;
while($path[$start] != 'api')
	$start++;

$depth -= $start;

// Make sure that there is a verison
if($depth == 1) {
	echo "Must call on a version of the API\n";
} else {
	switch($path[$start + 1]) {
		case "1.0":
			require_once 'lib/api_10.php';
			if($depth == 2) {
				// Root API call
				api_10_root($user);
			} else if($user != null && $depth == 3) {
				error_log("Calling on User API...\n");
				if(!api_10_user($user)) {
					error_log("There was a failure in the execution of the User API\n");
				} else {
					error_log("User API Completed Successfully\n");
				}
			} else if($user != null && $depth == 4) {
				error_log("Reached Notes API\n");
				api_10_notes($user);
			} else if($user != null && $depth == 5) {
				api_10_note($user, $path[4 + $start]);
			} else {
				if($user == null) {
					error_log("User not logged in while trying to access API\n");
					echo "Authentication required\n";
				} else {
					error_log("Too many arguments for the 1.0 API\n");
					echo "Incorrect argument count";
				}
			}
			break;
		case "1.1":
			echo "API 1.1 not implemented yet\n";
		default:
			echo "Unknown API\n";
	}
}

?>
