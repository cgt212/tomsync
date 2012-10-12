<?php

class MySQLAdapter {
	protected static $_instance;
	protected $_result;
	protected $_creds;
	protected $_connection;
	protected $_last_query;

	public static function getInstance() {
		if(self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected function __construct() {
		$this->_result = null;
		$this->_connection = null;
	}

	public function connect() {
		if($this->_connection === null) {
			if(!$this->_connection = mysqli_connect(SiteConfig::$db_host,
								SiteConfig::$user,
								SiteConfig::$pass,
								SiteConfig::$db_name)) {
				Logger::log("Error on connecting to DB".mysqli_connect_error()."\n", LOG_ERR);
				echo "Error Connecting to DB");
				return false;
			}
		}
		return true;
	}

	public function query($query) {
		if((!is_string($query)) || (empty($query))) {
			Logger::log("Query is invalid\n", LOG_ERR);
			return false;
		}
		if(!$this->connect()) {
			return false;
		}
		if(!$this->_result = mysqli_query($this->_connection, $query)) {
			Logger::log("Query error: ".mysqli_error($this->_connection)."\n", LOG_ERR);
			$this->_result = null;
			return false;
		}
		$this->_last_query = $query;
		return true;
	}

	public function fetch() {
		$row = null;
		if($this->_result !== null) {
			if(!$row = mysqli_fetch_array($this->_result, MYSQLI_ASSOC)) {
				$this->freeResult();
				return false;
			}
		}
		return $row;
	}

	public function escapeString($str) {
		return mysqli_real_escape_string($this->_connection, $str);
	}

	public function countRows() {
		if($this->_result !== null) {
			return mysqli_num_rows($this->_result);
		}
		return 0;
	}

	public function getAffectedRows() {
		return $this->_connection !== null ? mysqli_affected_rows($this->_connection) : 0;
	}
	
	public function freeResult() {
		if ($this->_result !== null) {
			mysqli_free_result($this->_result); 
			$this->_result = null;
		}
		$this->_last_query = null;
		$this->_result = null;
	}                                   

	public function disconnect() {
		if($this->_connection !== null) {
			mysqli_close($this->_connection);
		}       
		$this->_connection = null;
	}

	public function getError() {
		return mysqli_error($this->_connection);
	}

	public function __destruct() {
		$this->disconnect();
	}
}
?>
