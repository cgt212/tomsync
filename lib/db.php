<?php

class MySQLAdapter {
	protected static $_instance;
	protected $_dsn = "mysql://tomboy:tomboyIncremental@localhost/tomsync";
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
		if(!$this->_dsn) {
			echo "Bad connection string\n";
			die;
		}
		$this->_creds = parse_url($this->_dsn);
		$this->_result = null;
		$this->_connection = null;
	}

	public function connect() {
		if($this->_connection === null) {
			$host = $this->_creds['host'];
			$user = $this->_creds['user'];
			$pass = $this->_creds['pass'];
			$path = $this->_creds['path'];
			if(!$this->_connection = mysqli_connect($host, $user, $pass, basename($path))) {
				echo "Error on connecting to DB".mysqli_connect_error()."\n";
				return false;
			}
		}
		return true;
	}

	public function query($query) {
		if((!is_string($query)) || (empty($query))) {
			error_log("Query is invalid\n");
			return false;
		}
		if(!$this->connect()) {
			return false;
		}
		if(!$this->_result = mysqli_query($this->_connection, $query)) {
			error_log("Query error: ".mysqli_error($this->_connection)."\n");
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
