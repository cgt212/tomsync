<?php

/*
 * Dashboard display of OAuth information for TomSync
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

require_once 'lib/init.php';
require_once 'lib/oauth-web-utils.php';

_check_authentication();

global $store;

?>

<html>
  <head>
    <title>User OAuth Dashboard</title>
    <link rel="stylesheet" type="text/css" href="<?= SiteConfig::$url_root_dir ?>/css/tabs.css">
    <link rel="stylesheet" type="text/css" href="<?= SiteConfig::$url_root_dir ?>/css/style.css">
    <script type="text/javascript" src="<?= SiteConfig::$url_root_dir ?>/js/jquery-1.8.2.min.js"></script>
    <script type="text/javascript" src="<?= SiteConfig::$url_root_dir ?>/js/script.js"></script>
  </head>
  <body>
  <div class="commands">
    <a href="<?= SiteConfig::$url_root_dir ?>/logout">Logout</a>
  </div>
  <div id="tabs">
    <ul class="tablist">
      <li><a href="#token">My Consumer Tokens</a></li>
      <li><a href="#all-consumers">All Consumers</a></li>
      <li><a href="#my-consumers">My Consumers</a></li>
    </ul>
  </div>
  <div class="tab-wrapper">
    <div class="tabs" id="token">
<?php
$ctokens = $store->listConsumerTokens($user->getUID());
if(count($ctokens) < 1) {
	echo "<h2>You have no active consumer tokens</h2>\n";
} else {
	arrayofarrays2table($ctokens, 'token');
}
?>
    </div>
    <div class="tabs" id="all-consumers">
<?php
  $aconsumers = $store->listConsumerApplications();
  if(count($aconsumers) < 1) {
	  echo "<h2>There are currently no consumers registered on this system</h2>\n";
  } else {
	  arrayofarrays2table($aconsumers, 'all-consumers');
  }
?>
    </div>
    <div class="tabs" id="my-consumers">
<?php
$consumers = $store->listConsumers($user->getUID());
if(count($consumers) < 1) {
	echo "<h2>You have no registered consumers</h2>\n";
} else {
	arrayofarrays2table($consumers, 'my-consumers');
}
?>
    </div>
    <div class="tabs" id="formtab">
      <form name="form_consumer" method="post">
        <div class="input-label">Consumer Key:</div>
        <div class="input-text">
    	  <input type="text" name="consumer-key">
        </div>
        <div class="input-label">Consumer Secret:</div>
        <div class="input-text"><input type="text" name="consumer-secret"></div>
        <div class="input-label">Server URI:</div>
        <div class="input-text"><input type="text" name="server-uri"></div>
        <div class="input-text">Signature Method</div>
        <input type="radio" name="sig-method" value="HMAC-SHA1" checked>HMAC-SHA1<br />
        <input type="radio" name="sig-method" value="PLAINTEXT">PLAINTEXT<br />
        <div class="input-label">Request URI:</div>
        <div class="input-text"><input type="text" name="request-uri"></div>
        <div class="input-label">Authorize URI:</div>
        <div class="input-text"><input type="text" name="authorize-uri"></div>
        <div class="input-label">Access URI:</div>
        <div class="input-text"><input type="text" name="access-uri"></div>
      </form>
    </div>
  </div>
  </body>
</html>
