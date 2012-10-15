<?php

require_once __DIR__.'/../config/siteconfig.php';

$base_url = SiteConfig::$protocol.$_SERVER['SERVER_NAME'].SiteConfig::$url_root_dir;
$api_uri = "/api/1.0/";

function api_10_root($user) {
	global $api_uri, $base_url;
	if($user != null) {
		$ret['user-ref'] = array(
			'api-ref' => $base_url.$api_uri.$user->getUsername(),
			'href' => $base_url."/notes/".$user->getUsername());
	}
	$oauth_url = $base_url."/".SiteConfig::$oauth_dir;
	$ret["oauth_request_token_url"] = $oauth_url."/request_token";
	$ret["oauth_authorize_url"] = $oauth_url."/authorize";
	$ret["oauth_access_token_url"] = $oauth_url."/access_token";
	$ret["api-version"] = "1.0";
	header('Content-type: text/json');
	echo json_encode($ret);
}

function api_10_user($user) {
	global $api_uri, $base_url;
	if($user == null) {
		Logger::log("Empty user trying to access user API - Problem", LOG_ERR);
		echo "Authntication required\n";
		return false;
	}
	$base_url = SiteConfig::$protocol.$_SERVER['SERVER_NAME']."/".SiteConfig::$url_root_dir;
	$ret['user-name'] = $user->getUsername();
	$ret['first-name'] = $user->getFirstName();
	$ret['last-name'] = $user->getLastName();
	$ret['notes-ref'] = array(	'api-ref' => $base_url.$api_uri.$user->getUsername()."/notes",
					'href' => $base_url."/notes/".$user->getUsername()."/notes");
	$ret['latest-sync-revision'] = $user->getLatestSyncRevision();
	$ret['current-sync-guid'] = $user->getSyncUUID();

	header('Content-type: text/json');
	echo json_encode($ret);
	return true;
}

function api_10_notes($user) {

	require_once 'notestore.php';

	global $api_uri, $base_url;
	$store = new NoteStore($user);
	$since = 0;
	$content = false;
	$ret = array();

	if($user == null) {
		Logger::log("Empty user trying to access notes API - Should have been detected", LOG_ERR);
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

		$changes = json_decode($updates);
		$change_list = (array) $changes;
		if(isset($change_list['latest-sync-revision']) &&
		  $change_list['latest-sync-revision'] != $user->getLatestSyncRevision() + 1) {
			Logger::log("Out of sync - don't know how to recover from this error", LOG_ERR);
			echo "Sync error";
			exit;
		}
		$user->incrementLatestSyncRevision();
		foreach($change_list['note-changes'] as $c) {
			$change = (array) $c;
			$note = $store->findNoteByGUID($change['guid']);
			if(isset($change['command']) && ($change['command'] == "delete")) {
				Logger::log("Deleting note: ".$note->getGUID(), LOG_DEBUG);
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
		Logger::log("Invalid request method on notes API", LOG_WARN);
		echo "Invalid request type";
		return false;
	}
}

?>
