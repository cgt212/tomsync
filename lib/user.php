<?php

/*
 * User class for TomSync
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

include_once("db.php");

class User {
	
	protected $_user_id;
	protected $_username;
	protected $_first_name;
	protected $_last_name;
	protected $_sync_revision;
	protected $_uuid;
	protected $_db;

	function __construct() {
		$this->db = MySQLAdapter::getInstance();
	}

	protected function _fillUserData() {
		if(!$result = $this->_db->fetch()) {
			Logger::log("User Data Unavailable", LOG_WARN);
			return false;
		}
		$this->_user_id = $result['id'];
		$this->_username = $result['username'];
		$this->_first_name = $result['first_name'];
		$this->_last_name = $result['last_name'];
		$this->_sync_revision = $result['latest_sync_revision'];
		$this->_uuid = $result['current_sync_guid'];
		$this->_db->freeResult();

		settype($this->_sync_revision, "integer");
		$this->_db->freeResult();

		return true;
	}

	public function authenticate($username, $password) {
		if(!$this->_db) {
			$this->_db = MySQLAdapter::getInstance();
		}
		$query = "SELECT * FROM users WHERE username='$username' ";
		$query .= "AND password=SHA1('$password');";
		if(!$this->_db->query($query)) {
			echo "Something failed in the query - no authentication available\n";
			echo $this->_db->getError()."\n";
			$this->_db->freeResult();
			return false;
		}
		return $this->_fillUserData();
	}

	function findByUID($id) {
		if(!$this->_db) {
			$this->_db = MySQLAdapter::getInstance();
		}
		$query = "SELECT * from users WHERE id=$id;";
		if(!$this->_db->query($query)) {
			Logger::log("Query error: ".$this->_db->getError()."\n", LOG_ERR);
			$this->_db->freeResult();
			return false;
		}
		return $this->_fillUserData();
	}

	function getUID() {
		return $this->_user_id;
	}

	function getUsername() {
		return $this->_username;
	}

	function getFirstName() {
		return $this->_first_name;
	}

	function getLastName() {
		return $this->_last_name;
	}

	function getNoteID() {
		return $this->_id;
	}

	function getLatestSyncRevision() {
		return $this->_sync_revision;
	}

	function incrementLatestSyncRevision() {
		$this->_sync_revision++;
		$query = "UPDATE users SET latest_sync_revision=".$this->_sync_revision." WHERE ";
		$query .= "id=".$this->_user_id.";";
		if($this->_db === null) {
			$this->_db = MySQLAdapter::getInstance();
		}
		if(!$this->_db->query($query)) {
			Logger::log("Error incrementing Sync Revision for user: ".$this->_db->getError(), LOG_ERR);
			return false;
		}
		return $this->_sync_revision;
	}

	function getSyncUUID() {
		return $this->_uuid;
	}
}
?>
