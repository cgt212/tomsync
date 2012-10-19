CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` varchar(255) default NULL,
  `password` char(40) NOT NULL,
  `first_name` varchar(64) default NULL,
  `last_name` varchar(64) default NULL,
  `latest_sync_revision` int(11) NOT NULL default '-1',
  `current_sync_guid` varchar(36) NOT NULL,
  `active` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`),
  KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `notes` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `guid` varchar(36) NOT NULL,
  `title` text,
  `note_content` text,
  `note_content_version` double NOT NULL,
  `last_change_date` datetime NOT NULL,
  `last_metadata_change_date` datetime NOT NULL,
  `create_date` datetime NOT NULL,
  `last_sync_revision` int(11) NOT NULL default '-1',
  `open_on_startup` tinyint(1) NOT NULL default '0',
  `pinned` tinyint(1) NOT NULL default '0',
  `url` varchar(512) default NULL,
  `deleted` tinyint(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oauth_consumer_registry` (
  `ocr_id` int(11) NOT NULL auto_increment,
  `ocr_usa_id_ref` int(11) default NULL,
  `ocr_consumer_key` varchar(128) character set utf8 collate utf8_bin NOT NULL,
  `ocr_consumer_secret` varchar(128) character set utf8 collate utf8_bin NOT NULL,
  `ocr_signature_methods` varchar(255) NOT NULL default 'HMAC-SHA1,PLAINTEXT',
  `ocr_server_uri` varchar(255) NOT NULL,
  `ocr_server_uri_host` varchar(128) NOT NULL,
  `ocr_server_uri_path` varchar(128) character set utf8 collate utf8_bin NOT NULL,
  `ocr_request_token_uri` varchar(255) NOT NULL,
  `ocr_authorize_uri` varchar(255) NOT NULL,
  `ocr_access_token_uri` varchar(255) NOT NULL,
  `ocr_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`ocr_id`),
  UNIQUE KEY `ocr_consumer_key` (`ocr_consumer_key`,`ocr_usa_id_ref`,`ocr_server_uri`),
  KEY `ocr_server_uri` (`ocr_server_uri`),
  KEY `ocr_server_uri_host` (`ocr_server_uri_host`,`ocr_server_uri_path`),
  KEY `ocr_usa_id_ref` (`ocr_usa_id_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oauth_consumer_token` (
  `oct_id` int(11) NOT NULL auto_increment,
  `oct_ocr_id_ref` int(11) NOT NULL,
  `oct_usa_id_ref` int(11) NOT NULL,
  `oct_name` varchar(64) character set utf8 collate utf8_bin NOT NULL default '',
  `oct_token` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `oct_token_secret` varchar(255) character set utf8 collate utf8_bin NOT NULL,
  `oct_token_type` enum('request','authorized','access') default NULL,
  `oct_token_ttl` datetime NOT NULL default '9999-12-31 00:00:00',
  `oct_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`oct_id`),
  UNIQUE KEY `oct_ocr_id_ref` (`oct_ocr_id_ref`,`oct_token`),
  UNIQUE KEY `oct_usa_id_ref` (`oct_usa_id_ref`,`oct_ocr_id_ref`,`oct_token_type`,`oct_name`),
  KEY `oct_token_ttl` (`oct_token_ttl`),
  CONSTRAINT `oauth_consumer_token_ibfk_1` FOREIGN KEY (`oct_ocr_id_ref`) REFERENCES `oauth_consumer_registry` (`ocr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oauth_log` (
  `olg_id` int(11) NOT NULL auto_increment,
  `olg_osr_consumer_key` varchar(64) character set utf8 collate utf8_bin default NULL,
  `olg_ost_token` varchar(64) character set utf8 collate utf8_bin default NULL,
  `olg_ocr_consumer_key` varchar(64) character set utf8 collate utf8_bin default NULL,
  `olg_oct_token` varchar(64) character set utf8 collate utf8_bin default NULL,
  `olg_usa_id_ref` int(11) default NULL,
  `olg_received` text NOT NULL,
  `olg_sent` text NOT NULL,
  `olg_base_string` text NOT NULL,
  `olg_notes` text NOT NULL,
  `olg_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `olg_remote_ip` bigint(20) NOT NULL,
  PRIMARY KEY  (`olg_id`),
  KEY `olg_osr_consumer_key` (`olg_osr_consumer_key`,`olg_id`),
  KEY `olg_ost_token` (`olg_ost_token`,`olg_id`),
  KEY `olg_ocr_consumer_key` (`olg_ocr_consumer_key`,`olg_id`),
  KEY `olg_oct_token` (`olg_oct_token`,`olg_id`),
  KEY `olg_usa_id_ref` (`olg_usa_id_ref`,`olg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oauth_server_nonce` (
  `osn_id` int(11) NOT NULL auto_increment,
  `osn_consumer_key` varchar(64) character set utf8 collate utf8_bin NOT NULL,
  `osn_token` varchar(64) character set utf8 collate utf8_bin NOT NULL,
  `osn_timestamp` bigint(20) NOT NULL,
  `osn_nonce` varchar(80) character set utf8 collate utf8_bin NOT NULL,
  PRIMARY KEY  (`osn_id`),
  UNIQUE KEY `osn_consumer_key` (`osn_consumer_key`,`osn_token`,`osn_timestamp`,`osn_nonce`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oauth_server_registry` (
  `osr_id` int(11) NOT NULL auto_increment,
  `osr_usa_id_ref` int(11) default NULL,
  `osr_consumer_key` varchar(64) character set utf8 collate utf8_bin NOT NULL,
  `osr_consumer_secret` varchar(64) character set utf8 collate utf8_bin NOT NULL,
  `osr_enabled` tinyint(1) NOT NULL default '1',
  `osr_status` varchar(16) NOT NULL,
  `osr_requester_name` varchar(64) NOT NULL,
  `osr_requester_email` varchar(64) NOT NULL,
  `osr_callback_uri` varchar(255) NOT NULL,
  `osr_application_uri` varchar(255) NOT NULL,
  `osr_application_title` varchar(80) NOT NULL,
  `osr_application_descr` text NOT NULL,
  `osr_application_notes` text NOT NULL,
  `osr_application_type` varchar(20) NOT NULL,
  `osr_application_commercial` tinyint(1) NOT NULL default '0',
  `osr_issue_date` datetime NOT NULL,
  `osr_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`osr_id`),
  UNIQUE KEY `osr_consumer_key` (`osr_consumer_key`),
  KEY `osr_usa_id_ref` (`osr_usa_id_ref`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oauth_server_token_description` (
  `id` int(11) NOT NULL auto_increment,
  `ost_id` int(11) NOT NULL,
  `ost_description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ost_id_ref` (`ost_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `oauth_server_token` (
  `ost_id` int(11) NOT NULL auto_increment,
  `ost_osr_id_ref` int(11) NOT NULL,
  `ost_usa_id_ref` int(11) NOT NULL,
  `ost_token` varchar(64) character set utf8 collate utf8_bin NOT NULL,
  `ost_token_secret` varchar(64) character set utf8 collate utf8_bin NOT NULL,
  `ost_token_type` enum('request','access') default NULL,
  `ost_authorized` tinyint(1) NOT NULL default '0',
  `ost_referrer_host` varchar(128) NOT NULL default '',
  `ost_token_ttl` datetime NOT NULL default '9999-12-31 00:00:00',
  `ost_timestamp` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `ost_verifier` char(10) default NULL,
  `ost_callback_url` varchar(512) default NULL,
  PRIMARY KEY  (`ost_id`),
  UNIQUE KEY `ost_token` (`ost_token`),
  KEY `ost_osr_id_ref` (`ost_osr_id_ref`),
  KEY `ost_token_ttl` (`ost_token_ttl`),
  CONSTRAINT `oauth_server_token_ibfk_1` FOREIGN KEY (`ost_osr_id_ref`) REFERENCES `oauth_server_registry` (`osr_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `tags` (
  `id` int(11) NOT NULL auto_increment,
  `note_id` int(11) default NULL,
  `tag_name` varchar(255) default NULL,
  PRIMARY KEY  (`id`),
  KEY `note_id` (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
