<?php

class Logger {
	static function log($msgi, $priority = LOG_ERR) {
		if($priority <= SiteConfig::$log_level)
			error_log("TomSync: ".$msg);
	}
}

?>
