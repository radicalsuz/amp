
CREATE TABLE `blast` (
  `blast_ID` int(16) NOT NULL auto_increment,
  `blast_type` enum('Email','Email-Admin','SMS','VOIP') default NULL,
  `created` timestamp(14) NOT NULL,
  `modified` timestamp(14) NOT NULL,
  `requested` timestamp(14) NOT NULL,
  `from_email` varchar(128) default NULL,
  `from_name` varchar(128) default NULL,
  `from_phone` varchar(128) default NULL,
  `reply_to_address` varchar(128) default NULL,
  `subject` varchar(128) default NULL,
  `message_sms` varchar(160) default NULL,
  `message_email_html` text,
  `message_email_text` text,
  `embargo` datetime default NULL,
  `sendformat` enum('HTML and Text','HTML','Text') default NULL,
  `htmlformatted` tinyint(4) default '0',
  `message_template_ID` int(11) default NULL,
  `repeat` int(11) default '0',
  `repeatuntil` datetime default NULL,
  `rsstemplate` varchar(100) default NULL,
  `status` enum('New','Loading','Loaded','Sending Messages','Paused','Draft','Complete') default NULL,
  `processing_thread` varchar(32) default NULL,
  `attempts` int(4) default NULL,
  `publish` int(4) default NULL,
  `max_retries` int(11) NOT NULL default '1',
  `wait_time` int(11) NOT NULL default '30',
  `message` varchar(30) NOT NULL default '',
  `callerid` varchar(20) NOT NULL default '',
  `context` varchar(20) NOT NULL default '',
  `extension` varchar(32) NOT NULL default '1',
  `priority` enum('IMMEDIATE','HIGH','NORMAL','LOW') default 'NORMAL',
  `dialplan` text,
  `send_start_time` timestamp(14) NOT NULL,
  `send_end_time` timestamp(14) NOT NULL,
  `list_ID` int(11) NOT NULL default '0',
  PRIMARY KEY  (`blast_ID`)
) TYPE=MyISAM;



CREATE TABLE `blast_click_links` (
  `click_links_ID` int(16) NOT NULL auto_increment,
  `blast_ID` int(16) default NULL,
  `url` text,
  PRIMARY KEY  (`click_links_ID`)
) TYPE=MyISAM;


CREATE TABLE `blast_clicks` (
  `click_ID` int(16) NOT NULL auto_increment,
  `click_link_ID` int(16) default NULL,
  `message_ID` int(16) default NULL,
  `requested` timestamp(14) NOT NULL,
  PRIMARY KEY  (`click_ID`)
) TYPE=MyISAM;



CREATE TABLE `blast_lists` (
  `id` int(16) NOT NULL auto_increment,
  `name` text,
  `description` text,
  `publish` int(16) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



CREATE TABLE `blast_system_users` (
  `id` int(16) NOT NULL auto_increment,
  `user_ID` int(16) default NULL,
  `blast_ID` int(16) NOT NULL default '0',
  `Email` varchar(128) default NULL,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;



CREATE TABLE `blast_templates` (
  `id` int(16) NOT NULL auto_increment,
  `name` text,
  `description` text,
  `template` text,
  PRIMARY KEY  (`id`)
) TYPE=MyISAM;

CREATE TABLE `messages_to_contacts` (
  `message_ID` int(16) NOT NULL auto_increment,
  `blast_ID` int(16) default NULL,
  `user_ID` int(16) NOT NULL default '0',
  `system_user_ID` int(16) NOT NULL default '0',
  `message_type` enum('Email','Email-Admin','SMS','VOIP','VOIP-Admin') default NULL,
  `requested` timestamp(14) NOT NULL,
  `status` enum('New','Loaded','Paused','Pending','Sending','Done','In-Progress','Bad Address','Bounced','Conected','Answered','Failed','Server Failure','Delayed','Testing') default 'New',
  `asterisk_error_ID` int(11) NOT NULL default '0',
  `asterisk_box_ID` int(11) unsigned NOT NULL default '0',
  `thread_ID` int(8) default NULL,
  `bounce_type` enum('hard','soft') default NULL,
  `call_length` int(11) default '0',
  `unsubscribed` datetime default NULL,
  `viewed` datetime default NULL,
  `uniqueid` int(11) default NULL,
  `billable_seconds` char(32) default NULL,
  `last_data` char(32) default NULL,
  `last_application` char(32) default NULL,
  `ending_context` char(32) default NULL,
  `channel` char(128) default NULL,
  PRIMARY KEY  (`message_ID`)
) TYPE=MyISAM;
        
        