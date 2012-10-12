<?

//Include web startup functions to start session and load other needed files
//check to ensure the user is logged in

function _path_splitter() {
	$path = explode('/', substr($_SERVER['REQUEST_URI'], 1));
	$ret = array();
	foreach($path as $arg) {
		if($arg != '') {
			array_push($ret, $arg);
		}
	}
	return $ret;
}

print_r(_path_splitter());
