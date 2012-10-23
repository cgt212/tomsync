<?php

/*
 * Class for storing and retrieving nore URLS (Currently unused)
 *
 * Copyright 2012 Chris Tsonis <cgt212@whatbroke.com>
 *
 * This file is part of TomSync.
 *
 * TomSync is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TomSync is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with TomSync.  If not, see <http://www.gnu.org/licenses/>.
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
			Logger::log("URL Data Unavailable", LOG_ERR);
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
