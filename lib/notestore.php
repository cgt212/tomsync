<?php

/*
 * Note Collection class for TomSync
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

require_once 'db.php';
require_once 'note.php';

class NoteStore {
	protected $_db;
	protected $_user;
	protected $_notes;

	function __construct($user) {
		$this->_db = MySQLAdapter::getInstance();
		$this->_user = $user;
		$this->_notes = array();
	}

	function loadNotes($content) {
		if($content == true) {
			$query = "SELECT * ";
		} else {
			$query = "SELECT id, guid, title, last_sync_revision ";
		}
		$query .= "FROM notes WHERE user_id=".$this->_user->getUID()." AND deleted=false;";

		if(!$this->_db->query($query)) {
			$this->_db->freeResult();
			Logger::log("Note query failed ".$this->_db->getError()."\n", LOG_ERR);
			return false;
		}

		Logger::log("Query returned ".$this->_db->countRows()." results", LOG_DEBUG);

		while($row = $this->_db->fetch()) {
			$this->_notes[$row['id']] = new Note($this->_user);
			$this->_notes[$row['id']]->loadNoteDataFromDB($row);
		}
		$this->_db->freeResult();
		foreach ($this->_notes as &$note) {
			$note->loadTags();
		}
	}

	function getNoteList($content, $since = -1) {
		$ret = array();
		foreach ($this->_notes as &$note) {
			if($note->getLatestSyncRevision() > $since)
				array_push($ret, $note->getArray($content));
		}
		return $ret;
	}

	function getNote($id) {
		return (isset($this->_notes[$id])) ? $this->_notes[$id] : null;
	}

	function findNoteByGUID($guid) {
		$query = "SELECT * FROM notes WHERE user_id=".$this->_user->getUID()." AND guid='$guid' AND deleted=false;";
		if(!$this->_db->query($query)) {
			Logger::log("DB error for guid: ".$this->_db->getError(), LOG_ERR);
			return false;
		}
		$ret = new Note($this->_user);
		if($this->_db->countRows() == 0) {
			$this->_db->freeResult();
		} else {
			$ret->loadNoteDataFromDB($this->_db->fetch());
			$this->_db->freeResult();
			$ret->loadTags();
		}
		return $ret;
	}
}
