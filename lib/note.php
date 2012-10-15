<?php

require_once 'tag.php';

class Note {

	protected $_user;
	protected $_id;
	protected $_db;
	protected $_guid;
	protected $_title;
	protected $_content;
	protected $_content_version;
	protected $_last_change;
	protected $_last_metadata_chenge;
	protected $_created;
	protected $_last_sync_revision;
	protected $_open_on_startup;
	protected $_pinned;
	protected $_tags;
	protected $_dirty;
	protected $_note;
	protected $_url;

	function __construct(&$user) {
		//$this->_db = MySQLAdapter::getInstance();
		$this->_id = -1;
		$this->_note = array();
		$this->_dirty = false;
		$this->_user = $user;
	}

	function _createURL() {
		$url = "/notes/".$this->_user->getUsername()."/";
		$url .= strtolower($this->_note['title']);
		$this->_url = $url;
	}

	function loadTags() {
		if($this->_tags != null)
			return;
		$this->_tags = new Tags($this);
		$this->_tags->loadTags();
	}

	function loadNoteDataFromDB($note) {
		foreach($note as $k => $v) {
			if($k == 'tags')
				continue;
			$key = str_replace('_', '-', $k);
			$this->_note[$key] = $v;
		}
		$this->_id = $note['id'];
		if(isset($this->_note['content-version'])) {
			settype($this->_note['content-version'], "double");
		}
		if(isset($this->_note['note-content-version'])) {
			settype($this->_note['note-content-version'], "double");
		}
		if(isset($this->_note['pinned'])) {
			settype($this->_note['pinned'], "bool");
		}
		if(isset($this->_note['open-on-startup'])) {
			settype($this->_note['open-on-startup'], "bool");
		}
		if(isset($this->_note['last-sync-revision'])) {
			settype($this->_note['last-sync-revision'], "integer");
		}
		/*
		$this->_tags = new Tags($this);
		$this->_tags->loadTags();
		 */

		$url_host = SiteConfig::$protocol.$_SERVER['SERVER_NAME']."/".SiteConfig::$url_root_dir;
		//TODO: Fix the API reference here - it should not be set to 1.0
		$this->_note['ref'] = array(
			'api-ref' => $url_host."/api/1.0/".$this->_user->getUsername()."/notes/".$this->_id,
			'href' => $url_host."/user/".$this->_user->getUsername()."/notes/".$this->_id."/".strtolower(str_replace(' ', '-', $this->_note['title'])));
	}

	function getNoteID() {
		return $this->_id;
	}

	function getGUID() {
		return $this->_note['guid'];
	}

	function setGUID($guid) {
		$this->_note['guid'] = $guid;
		$this->_dirty = true;
	}

	function getLatestSyncRevision() {
		return $this->_note['last-sync-revision'];
	}

	function getURL() {
		if(!isset($this->_url) || $this->_url == '') {
			$this->_createURL();
		}
		return $this->_url;
	}

	function getArray($content) {
		$ret = array();
		if(!$content) {
			$ret['guid'] = $this->_note['guid'];
			$ret['ref'] = $this->_note['ref'];
			$ret['title'] = $this->_note['title'];
		} else {
			$ret['guid'] = $this->_note['guid'];
			$ret['title'] = $this->_note['title'];
			$ret['note-content'] = $this->_note['note-content'];
			$ret['note-content-version'] = $this->_note['note-content-version'];
			$ret['last-change-date'] = date('Y-m-d\Th:i:s.uP', 
							strtotime($this->_note['last-change-date']."UTC"));
			$ret['last-metadata-change-date'] = date('Y-m-d\Th:i:s.uP', 
							strtotime($this->_note['last-metadata-change-date']."UTC"));
			$ret['create-date'] = date('Y-m-d\Th:i:s.uP', strtotime($this->_note['create-date']."UTC"));
			$ret['last-sync-revision'] = $this->_note['last-sync-revision'];
			if($this->_note['open-on-startup'] != null) {
				$ret['open-on-startup'] = ($this->_note['open-on-startup']) ? "true" : "false";
			} else {
				$ret['open-on-startup'] = false;
			}
			if($this->_note['pinned'] != null) {
				$ret['pinned'] = ($this->_note['pinned']) ? "true" : "false";
			} else {
				$ret['pinned'] = false;
			}
			if(isset($this->_tags)) {
				$ret['tags'] = $this->_tags->getTagList();
			}
			/*
			$ret['tags'] = (count(json_decode($this->_note['tags'])) == 0) ? null
							: json_decode($this->_note['tags']);
			 */

		}

		return $ret;
	}

	function updateNote($change) {
		foreach ($change as $k => $v) {
			if($k == "tags") {
				if(!isset($this->_tags))
					$this->_tags = new Tags($this);
				else
					$this->_tags->clearTags();
				foreach($v as $tag)
					$this->_tags->addTag($tag);
			}
			$this->_note[$k] = $v;
		}
		$this->_note['last-sync-revision'] = $this->_user->getLatestSyncRevision();
		$this->_dirty = true;
		$this->save();
	}

	function delete() {
		$this->_db = MySQLAdapter::getInstance();
		if(!$this->_db->connect()) {
			Logger::log("Database connection error when tring to delete note\n", LOG_ERR);
			return false;
		}
		$query = "UPDATE notes SET deleted=true WHERE guid='".$this->_note['guid']."';";
		if(!$this->_db->query($query)) {
			Logger::log("Error deleting note: ".$this->_db->getError()."\n", LOG_ERR);
			return false;
		}
		return true;
	}

	function save() {
		if(!$this->_dirty) {
			return true;
		}
		$this->_db = MySQLAdapter::getInstance();
		if(!$this->_db->connect()) {
			Logger::log("Database connection error when tring to save note\n", LOG_ERR);
			return false;
		}
		$fields = array();
		$values = array();
		array_push($fields, "last_sync_revision");
		array_push($values, $this->_user->getLatestSyncRevision());
		if(isset($this->_note['guid'])) {
			array_push($fields, "guid");
			array_push($values, "'".$this->_db->escapeString($this->_note['guid'])."'");
		}
		if(isset($this->_note['note-content'])) {
			array_push($fields, "note_content");
			array_push($values, "'".$this->_db->escapeString($this->_note['note-content'])."'");
		}
		if(isset($this->_note['title'])) {
			array_push($fields, "title");
			array_push($values, "'".$this->_db->escapeString($this->_note['title'])."'");
		}
		if(isset($this->_note['note-content-version'])) {
			array_push($fields, "note_content_version");
			array_push($values, $this->_note['note-content-version']);
		}
		if(isset($this->_note['last-change-date'])) {
			array_push($fields, "last_change_date");
			array_push($values, "'".gmdate('Y-m-d G:i:s', 
				strtotime($this->_note['last-change-date']))."'");
		}
		if(isset($this->_note['last-metadata-change-date'])) {
			array_push($fields, "last_metadata_change_date");
			array_push($values, "'".gmdate('Y-m-d G:i:s',
			                                strtotime($this->_note['last-metadata-change-date']))."'");
		} else {
			array_push($fields, "last_metadata_change_date");
			array_push($values, "NOW()");
		}
		if(isset($this->_note['create-date'])) {
			array_push($fields, "create_date");
			array_push($values, "'".gmdate('Y-m-d G:i:s', 
				strtotime($this->_note['create-date']))."'");
		} else if($this->_id < 0) {
			array_push($fields, "create_date");
			if(isset($this->_note['last-change-date'])) {
				array_push($values, "'".gmdate('Y-m-d G:i:s',
					strtotime($this->_note['last-change-date']))."'");
			} else {
				array_push($values, "NOW()");
			}
		}
		if(isset($this->_note['open-on-startup']) && $this->_note['open-on-startup']) {
			array_push($fields, "open_on_startup");
			array_push($values, "1");
		}
		if(isset($this->_note['pinned']) && $this->_note['pinned']) {
			array_push($fields, "pinned");
			array_push($values, "1");
		}
		/*if(isset($this->_note['tags'])) {
			$this->_tags = new Tags($this);
			foreach ($this->_note['tags'] as $tag)
				$this->_tags->addTag($tag);
		}*/

		if($this->_id > 0) {
			$query = "UPDATE notes SET ";
			for($cnt = 0; $cnt < count($fields); $cnt++) {
				$query .= $fields[$cnt]."=".$values[$cnt];
				if($cnt+1 != count($fields)) {
					$query .= ", ";
				}
			}
			$query .= " WHERE guid='".$this->_db->escapeString($this->_note['guid'])."';";
		} else {
			array_push($fields, "user_id");
			array_push($values, $this->_user->getUID());
			$query = "INSERT INTO notes (".implode(", ", $fields).") VALUES(".implode(", ", $values).");";
		}

		if(!$this->_db->query($query)) {
			Logger::log("Database error on note insert/update: ".$this->_db->getError()."\n", LOG_ERR);
			return false;
		}
		if($this->_id < 0) {
			$query = "SELECT id FROM notes WHERE guid='".$this->_note['guid']."';";
			if(!$this->_db->query($query)) {
				Logger::log("Database error on getting note id: ".$this->_db->getError()."\n", LOG_ERR);
				$this->_db->freeResult();
				return false;
			}
			//There must be at least 1 row
			if($this->_db->countRows() < 1) {
				Logger::log("Database error on getting note id: There was nothing returned\n", LOG_ERR);
				$this->_db->freeResult();
				return false;
			}
			$row = $this->_db->fetch();
			$this->_id = $row['id'];
			$this->_db->freeResult();
		}
		if($this->_tags != null)
			$this->_tags->save();
	}
}
?>

