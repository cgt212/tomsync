<?php

$oauth_root = "/tomsync/oauth";
$api_url = "/tomsync/api/1.0";

function api_10_root($user) {
	global $oauth_root, $api_url;
	$url_host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
	$url_host .= $_SERVER['SERVER_NAME'];
	if($user != null) {
		$ret['user-ref'] = array(	'api-ref' => $url_host.$api_url."/".$user->getUsername(),
						'href' => $url_host.$api_url."/user/".$user->getUsername());
	}
	$ret["oauth_request_token_url"] = $url_host.$oauth_root."/request_token";
	$ret["oauth_authorize_url"] = $url_host.$oauth_root."/authorize";
	$ret["oauth_access_token_url"] = $url_host.$oauth_root."/access_token";
	$ret["api-version"] = "1.0";
	header('Content-type: text/json');
	echo json_encode($ret);
}

function api_10_user($user) {
	global $oauth_root, $api_url;
	$url_host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
	$url_host .= $_SERVER['SERVER_NAME'];
	if($user == null) {
		error_log("Empty user trying to access user API - Problem");
		echo "Authntication required\n";
		return false;
	}
	$ret['user-name'] = $user->getUsername();
	$ret['first-name'] = $user->getFirstName();
	$ret['last-name'] = $user->getLastName();
	$ret['notes-ref'] = array(	'api-ref' => $url_host.$api_url."/".$user->getUsername()."/notes",
					'href' => $url_host."/user/".$user->getUsername()."/notes");
	$ret['latest-sync-revision'] = $user->getLatestSyncRevision();
	$ret['current-sync-guid'] = $user->getSyncUUID();

	header('Content-type: text/json');
	echo json_encode($ret);
	return true;
}

function api_10_notes($user) {

	require_once 'notestore.php';

	global $oauth_root, $api_url;
	$store = new NoteStore($user);
	$since = 0;
	$content = false;
	$ret = array();

	$url_host = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
	$url_host .= $_SERVER['SERVER_NAME'];

	if($user == null) {
		error_log("Empty user trying to access notes API - Should have been detected");
		echo "Authentication Required";
		return false;
	}

	//Check what we are expected to do here
	if($_SERVER['REQUEST_METHOD'] == 'GET') {
		if(isset($_GET['since'])) {
			$since = $_GET['since'];
		}
		//check if the include_notes flag is set
		$content = (isset($_GET['include_notes']) && ($_GET['include_notes'] == true)) ? true : false;
		$store->loadNotes($content);
		$ret['latest-sync-revision'] = $user->getLatestSyncRevision();
		$ret['notes'] = $store->getNoteList($content, $since);
		header('Content-type: text/json');
		echo json_encode($ret);
	} else if($_SERVER['REQUEST_METHOD'] == 'PUT') {
		//Gather the updates from the clients
		$updates = "";
		$updates = file_get_contents("php://input");

		$out = fopen("/tmp/oauth_output", 'w');
		fwrite($out, $updates);
		$changes = json_decode($updates);
		$change_list = (array) $changes;
		fwrite($out, "\n\n".print_r($change_list, true));
		fclose($out);
		if(isset($change_list['latest-sync-revision']) &&
		  $change_list['latest-sync-revision'] != $user->getLatestSyncRevision() + 1) {
			error_log("Out of sync - don't know how to recover from this error");
			echo "Sync error";
			exit;
		}
		$user->incrementLatestSyncRevision();
		foreach($change_list['note-changes'] as $c) {
			$change = (array) $c;
			$note = $store->findNoteByGUID($change['guid']);
			if(isset($change['command']) && ($change['command'] == "delete")) {
				error_log("Deleting note: ".$note->getGUID());
				$note->delete();
				continue;
			} else {
				$note->updateNote($change);
			}
		}
		$store->loadNotes(true);
		$ret['latest-sync-revision'] = $user->getLatestSyncRevision();
		$ret['notes'] = $store->getNoteList(false);
		header('Content-type: text/json');
		echo json_encode($ret);
	} else {
		error_log("Invalid request method on notes API");
		echo "Invalid request type";
		return false;
	}
}

?>
