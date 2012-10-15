<?php

class Config {
	/* Protocol that the server will use for incoming connections */
	public static $protocol		=	'http://';
	/* The server name to use when building URLs for responses */
	// public $server_name	=	$_SERVER['SERVER_NAME'];
	/* directory under web root that tomsync is located in
	 * with leading slash */
	public static $url_root_dir	=	'/tomsync';
	public static $log_level	=	LOG_DEBUG;
	/* Database information */
	public static $db_host		=	'localhost';
	public static $db_name		=	'tomsync';
	public static $db_user		=	'tom';
	public static $db_pass		=	'sync';

	/* OAuth location - fixed for now, but could change in the future */
	public static $oauth_dir	=	'oauth';
}

?>
