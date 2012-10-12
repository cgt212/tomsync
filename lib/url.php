<?php

/*
 * Database Schema
 * id INT Unique ID of the url
 * note_id INT key to the referenced note
 * url VARCHAR(255) request_uri portion of the url
 */

require_once "db.php";

class URLizer {
	protected static $_CONFIG_url_base = "notes";
	protected $_db;

	function __construct() {
		$this->_db = MySQLAdapter::getInstance();
	}

	function _createURLTitle($title) {
		$t = strtolower($title);
		$uri = str_replace(' ', '-', $t);
		return $uri;
	}

	function createURLForNote($user, $note) {
		$url = "/".$this->_CONFIG_url_base."/".$user->getUsername()."/";
		$url .= $this->_createURLTitle($note->getTitle());

		$append = 1;
		while($ans = $this->_getNoteIDFromURL(i$url) && $ans != $note->getNoteID()) {
			$url .= $url."=".$append;
		}
		$query = "INSERT INTO urls (note_id, url) VALUES(".$note->getNoteID().", '";
		$query .= mysqli_excape_string($url)."');";
		$this->_db->query($query);
	}

	function getNoteIDFromURL($url) {
		$query = "SELECT note_id FROM urls WHERE url='".mysqli_escape_string($url)."';";
		if(!$this->_db->query($query)) {
			$this->_db->freeResult();
			echo "Something wrong with finding the URL\n";
			return false;
		}
		if(!$result = $this->_db->fetch()) {
			error_log("URL Data Unavailable");
			return false;
		}
		if($this->_db->countRows() > 0) {
			$ret = $result['id'];
			$this->_db->freeResult();
		} else {
			$ret = false;
		}

		return $ret;
	}
}

?>
