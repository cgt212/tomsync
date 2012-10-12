<?php

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
			error_log("Note query failed ".$this->_db->getError()."\n");
			return false;
		}

		error_log("Query returned ".$this->_db->countRows()." results");

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
			if(($note->getLatestSyncRevision() <= $since) && ($since != 0)) {
				continue;
			}
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
			error_log("DB error for guid: ".$this->_db->getError());
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
