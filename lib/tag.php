<?php

class Tags {
	protected $_note;
	protected $_tags;
	protected $_db;

	function __construct($note) {
		$this->_note = $note;
		$this->_tags = null;
		$this->_db = MySQLAdapter::getInstance();
	}

	function loadTags() {
		if($this->_tags == null) {
			$query = "SELECT * FROM tags WHERE note_id=".$this->_note->getNoteID().";";
			if(!$this->_db->query($query)) {
				error_log("Tag Query failed".$this->_db->getError());
				return false;
			}
			$this->_tags = array();
			while($row = $this->_db->fetch()) {
				array_push($this->_tags, $row['tag_name']);
			}
			$this->_db->freeResult();
		}
	}

	function getTagList() {
		return $this->_tags;
	}

	function clearTags() {
		$this->_tags = null;
	}

	function addTag($tag) {
		if($this->_tags == null)
			$this->_tags = array();
		array_push($this->_tags, $tag);
	}
	function save() {
		if($this->_tags == null)
			return true;
		$args = array();
		$query = "DELETE FROM tags WHERE note_id=".$this->_note->getNoteID().";";
		//TODO: Check the result
		$this->_db->query($query);
		$query = "INSERT INTO tags (note_id, tag_name) VALUES ";
		foreach ($this->_tags as $tag) {
			array_push($args, "(".$this->_note->getNoteID().", '$tag')");
		}
		$query .= implode(", ", $args).";";
		if(!$this->_db->query($query)) {
			error_log("Failed to save tags".$this->_db->getError());
			return false;
		}
		return true;
	}
}

?>
