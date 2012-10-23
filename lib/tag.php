<?php

/*
 * Class representing note tags for TomSync
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
				Logger::log("Tag Query failed".$this->_db->getError(), LOG_ERR);
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
			Logger::log("Failed to save tags".$this->_db->getError(), LOG_ERR);
			return false;
		}
		return true;
	}
}

?>
