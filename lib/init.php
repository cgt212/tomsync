<?php

/*
 * Bootstrap file for the API portion of TomSync
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

require_once 'user.php';
require_once 'logger.php';
require_once __DIR__.'/../config/siteconfig.php';

session_start();
$user = null;

function _check_authentication() {
	global $user;
	$user = false;

	if(isset($_SESSION['authenticated']) && ($_SESSION['authenticated'] == true)) {
		$user = new User();
		if(!$user->findByUID($_SESSION['user_id'])) {
			header("Location: ".SiteConfig::$url_root_dir."/login?goto=".urlencode($_SERVER['REQUEST_URI']));
			die;
		}
	} else if(!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] == false) {
		header("Location: ".SiteConfig::$url_root_dir."/login?goto=".urlencode($_SERVER['REQUEST_URI']));
		die;
	}
	return $user;
}

$db_conn = mysql_connect(SiteConfig::$db_host, SiteConfig::$db_user, SiteConfig::$db_pass);
if(!$db_conn) {
	Logger::log("DB error: ".mysql_error());
	die;
}
mysql_select_db(SiteConfig::$db_name, $db_conn);

require_once 'oauth/OAuthStore.php';
$store = OAuthStore::instance('MySQL', array('conn' => $db_conn));

require_once 'oauth/OAuthServer.php';
$server = new OAuthServer();

?>
