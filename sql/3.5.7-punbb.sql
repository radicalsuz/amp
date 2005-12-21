
CREATE TABLE IF NOT EXISTS `punbb_bans` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `username` varchar(200) default NULL,
  `ip` varchar(255) default NULL,
  `email` varchar(50) default NULL,
  `message` varchar(255) default NULL,
  `expire` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `punbb_categories` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `cat_name` varchar(80) NOT NULL default 'New Category',
  `disp_position` int(10) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;



REPLACE INTO `punbb_categories` VALUES (1, 'Test category', 1);



CREATE TABLE IF NOT EXISTS `punbb_censoring` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `search_for` varchar(60) NOT NULL default '',
  `replace_with` varchar(60) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `punbb_config` (
  `conf_name` varchar(255) NOT NULL default '',
  `conf_value` text,
  PRIMARY KEY  (`conf_name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



REPLACE INTO `punbb_config` VALUES ('o_cur_version', '1.2.10');
REPLACE INTO `punbb_config` VALUES ('o_board_title', 'Fourm');
REPLACE INTO `punbb_config` VALUES ('o_board_desc', NULL);
REPLACE INTO `punbb_config` VALUES ('o_server_timezone', '0');
REPLACE INTO `punbb_config` VALUES ('o_time_format', 'H:i:s');
REPLACE INTO `punbb_config` VALUES ('o_date_format', 'Y-m-d');
REPLACE INTO `punbb_config` VALUES ('o_timeout_visit', '600');
REPLACE INTO `punbb_config` VALUES ('o_timeout_online', '300');
REPLACE INTO `punbb_config` VALUES ('o_redirect_delay', '1');
REPLACE INTO `punbb_config` VALUES ('o_show_version', '0');
REPLACE INTO `punbb_config` VALUES ('o_show_user_info', '1');
REPLACE INTO `punbb_config` VALUES ('o_show_post_count', '1');
REPLACE INTO `punbb_config` VALUES ('o_smilies', '1');
REPLACE INTO `punbb_config` VALUES ('o_smilies_sig', '1');
REPLACE INTO `punbb_config` VALUES ('o_make_links', '1');
REPLACE INTO `punbb_config` VALUES ('o_default_lang', 'English');
REPLACE INTO `punbb_config` VALUES ('o_default_style', 'Oxygen');
REPLACE INTO `punbb_config` VALUES ('o_default_user_group', '4');
REPLACE INTO `punbb_config` VALUES ('o_topic_review', '15');
REPLACE INTO `punbb_config` VALUES ('o_disp_topics_default', '30');
REPLACE INTO `punbb_config` VALUES ('o_disp_posts_default', '25');
REPLACE INTO `punbb_config` VALUES ('o_indent_num_spaces', '4');
REPLACE INTO `punbb_config` VALUES ('o_quickpost', '1');
REPLACE INTO `punbb_config` VALUES ('o_users_online', '0');
REPLACE INTO `punbb_config` VALUES ('o_censoring', '0');
REPLACE INTO `punbb_config` VALUES ('o_ranks', '0');
REPLACE INTO `punbb_config` VALUES ('o_show_dot', '0');
REPLACE INTO `punbb_config` VALUES ('o_quickjump', '1');
REPLACE INTO `punbb_config` VALUES ('o_gzip', '0');
REPLACE INTO `punbb_config` VALUES ('o_additional_navlinks', '');
REPLACE INTO `punbb_config` VALUES ('o_report_method', '0');
REPLACE INTO `punbb_config` VALUES ('o_regs_report', '0');
REPLACE INTO `punbb_config` VALUES ('o_mailing_list', '');
REPLACE INTO `punbb_config` VALUES ('o_avatars', '0');
REPLACE INTO `punbb_config` VALUES ('o_avatars_dir', 'img/avatars');
REPLACE INTO `punbb_config` VALUES ('o_avatars_width', '60');
REPLACE INTO `punbb_config` VALUES ('o_avatars_height', '60');
REPLACE INTO `punbb_config` VALUES ('o_avatars_size', '10240');
REPLACE INTO `punbb_config` VALUES ('o_search_all_forums', '1');
REPLACE INTO `punbb_config` VALUES ('o_base_url', '');
REPLACE INTO `punbb_config` VALUES ('o_admin_email', '');
REPLACE INTO `punbb_config` VALUES ('o_webmaster_email', '');
REPLACE INTO `punbb_config` VALUES ('o_subscriptions', '1');
REPLACE INTO `punbb_config` VALUES ('o_smtp_host', NULL);
REPLACE INTO `punbb_config` VALUES ('o_smtp_user', NULL);
REPLACE INTO `punbb_config` VALUES ('o_smtp_pass', NULL);
REPLACE INTO `punbb_config` VALUES ('o_regs_allow', '1');
REPLACE INTO `punbb_config` VALUES ('o_regs_verify', '0');
REPLACE INTO `punbb_config` VALUES ('o_announcement', '0');
REPLACE INTO `punbb_config` VALUES ('o_announcement_message', 'Enter your announcement here.');
REPLACE INTO `punbb_config` VALUES ('o_rules', '0');
REPLACE INTO `punbb_config` VALUES ('o_rules_message', 'Enter your rules here.');
REPLACE INTO `punbb_config` VALUES ('o_maintenance', '0');
REPLACE INTO `punbb_config` VALUES ('o_maintenance_message', 'The forums are temporarily down for maintenance. Please try again in a few minutes.<br />\n<br />\n/Administrator');
REPLACE INTO `punbb_config` VALUES ('p_mod_edit_users', '1');
REPLACE INTO `punbb_config` VALUES ('p_mod_rename_users', '0');
REPLACE INTO `punbb_config` VALUES ('p_mod_change_passwords', '0');
REPLACE INTO `punbb_config` VALUES ('p_mod_ban_users', '0');
REPLACE INTO `punbb_config` VALUES ('p_message_bbcode', '1');
REPLACE INTO `punbb_config` VALUES ('p_message_img_tag', '1');
REPLACE INTO `punbb_config` VALUES ('p_message_all_caps', '1');
REPLACE INTO `punbb_config` VALUES ('p_subject_all_caps', '1');
REPLACE INTO `punbb_config` VALUES ('p_sig_all_caps', '1');
REPLACE INTO `punbb_config` VALUES ('p_sig_bbcode', '1');
REPLACE INTO `punbb_config` VALUES ('p_sig_img_tag', '0');
REPLACE INTO `punbb_config` VALUES ('p_sig_length', '400');
REPLACE INTO `punbb_config` VALUES ('p_sig_lines', '4');
REPLACE INTO `punbb_config` VALUES ('p_allow_banned_email', '1');
REPLACE INTO `punbb_config` VALUES ('p_allow_dupe_email', '0');
REPLACE INTO `punbb_config` VALUES ('p_force_guest_email', '1');



CREATE TABLE IF NOT EXISTS `punbb_forum_perms` (
  `group_id` int(10) NOT NULL default '0',
  `forum_id` int(10) NOT NULL default '0',
  `read_forum` tinyint(1) NOT NULL default '1',
  `post_replies` tinyint(1) NOT NULL default '1',
  `post_topics` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`group_id`,`forum_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `punbb_forums` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `forum_name` varchar(80) NOT NULL default 'New forum',
  `forum_desc` text,
  `redirect_url` varchar(100) default NULL,
  `moderators` text,
  `num_topics` mediumint(8) unsigned NOT NULL default '0',
  `num_posts` mediumint(8) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned default NULL,
  `last_post_id` int(10) unsigned default NULL,
  `last_poster` varchar(200) default NULL,
  `sort_by` tinyint(1) NOT NULL default '0',
  `disp_position` int(10) NOT NULL default '0',
  `cat_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;



REPLACE INTO `punbb_forums` VALUES (1, 'Test forum', 'This is just a test forum', NULL, NULL, 1, 1, 1133820143, 1, 'admin', 0, 1, 1);



CREATE TABLE IF NOT EXISTS `punbb_groups` (
  `g_id` int(10) unsigned NOT NULL auto_increment,
  `g_title` varchar(50) NOT NULL default '',
  `g_user_title` varchar(50) default NULL,
  `g_read_board` tinyint(1) NOT NULL default '1',
  `g_post_replies` tinyint(1) NOT NULL default '1',
  `g_post_topics` tinyint(1) NOT NULL default '1',
  `g_post_polls` tinyint(1) NOT NULL default '1',
  `g_edit_posts` tinyint(1) NOT NULL default '1',
  `g_delete_posts` tinyint(1) NOT NULL default '1',
  `g_delete_topics` tinyint(1) NOT NULL default '1',
  `g_set_title` tinyint(1) NOT NULL default '1',
  `g_search` tinyint(1) NOT NULL default '1',
  `g_search_users` tinyint(1) NOT NULL default '1',
  `g_edit_subjects_interval` smallint(6) NOT NULL default '300',
  `g_post_flood` smallint(6) NOT NULL default '30',
  `g_search_flood` smallint(6) NOT NULL default '30',
  PRIMARY KEY  (`g_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;



REPLACE INTO `punbb_groups` VALUES (1, 'Administrators', 'Administrator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0);
REPLACE INTO `punbb_groups` VALUES (2, 'Moderators', 'Moderator', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0);
REPLACE INTO `punbb_groups` VALUES (3, 'Guest', NULL, 1, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 0);
REPLACE INTO `punbb_groups` VALUES (4, 'Members', NULL, 1, 1, 1, 1, 1, 1, 1, 0, 1, 1, 300, 60, 30);



CREATE TABLE IF NOT EXISTS `punbb_online` (
  `user_id` int(10) unsigned NOT NULL default '1',
  `ident` varchar(200) NOT NULL default '',
  `logged` int(10) unsigned NOT NULL default '0',
  `idle` tinyint(1) NOT NULL default '0',
  KEY `punbb_online_user_id_idx` (`user_id`)
) ENGINE=HEAP DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `punbb_posts` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `poster` varchar(200) NOT NULL default '',
  `poster_id` int(10) unsigned NOT NULL default '1',
  `poster_ip` varchar(15) default NULL,
  `poster_email` varchar(50) default NULL,
  `message` text NOT NULL,
  `hide_smilies` tinyint(1) NOT NULL default '0',
  `posted` int(10) unsigned NOT NULL default '0',
  `edited` int(10) unsigned default NULL,
  `edited_by` varchar(200) default NULL,
  `topic_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `punbb_posts_topic_id_idx` (`topic_id`),
  KEY `punbb_posts_multi_idx` (`poster_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;



REPLACE INTO `punbb_posts` VALUES (1, 'admin', 2, '127.0.0.1', NULL, 'If you are looking at this (which I guess you are), the install of PunBB appears to have worked! Now log in and head over to the administration control panel to configure your forum.', 0, 1133820143, NULL, NULL, 1);



CREATE TABLE IF NOT EXISTS `punbb_ranks` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `rank` varchar(50) NOT NULL default '',
  `min_posts` mediumint(8) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;



REPLACE INTO `punbb_ranks` VALUES (1, 'New member', 0);
REPLACE INTO `punbb_ranks` VALUES (2, 'Member', 10);



CREATE TABLE IF NOT EXISTS `punbb_reports` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `post_id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  `forum_id` int(10) unsigned NOT NULL default '0',
  `reported_by` int(10) unsigned NOT NULL default '0',
  `created` int(10) unsigned NOT NULL default '0',
  `message` text NOT NULL,
  `zapped` int(10) unsigned default NULL,
  `zapped_by` int(10) unsigned default NULL,
  PRIMARY KEY  (`id`),
  KEY `punbb_reports_zapped_idx` (`zapped`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `punbb_search_cache` (
  `id` int(10) unsigned NOT NULL default '0',
  `ident` varchar(200) NOT NULL default '',
  `search_data` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `punbb_search_cache_ident_idx` (`ident`(8))
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `punbb_search_matches` (
  `post_id` int(10) unsigned NOT NULL default '0',
  `word_id` mediumint(8) unsigned NOT NULL default '0',
  `subject_match` tinyint(1) NOT NULL default '0',
  KEY `punbb_search_matches_word_id_idx` (`word_id`),
  KEY `punbb_search_matches_post_id_idx` (`post_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `punbb_search_words` (
  `id` mediumint(8) unsigned NOT NULL auto_increment,
  `word` varchar(20) character set latin1 collate latin1_bin NOT NULL default '',
  PRIMARY KEY  (`word`),
  KEY `punbb_search_words_id_idx` (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;



CREATE TABLE IF NOT EXISTS `punbb_subscriptions` (
  `user_id` int(10) unsigned NOT NULL default '0',
  `topic_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`user_id`,`topic_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



CREATE TABLE IF NOT EXISTS `punbb_topics` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `poster` varchar(200) NOT NULL default '',
  `subject` varchar(255) NOT NULL default '',
  `posted` int(10) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned NOT NULL default '0',
  `last_post_id` int(10) unsigned NOT NULL default '0',
  `last_poster` varchar(200) default NULL,
  `num_views` mediumint(8) unsigned NOT NULL default '0',
  `num_replies` mediumint(8) unsigned NOT NULL default '0',
  `closed` tinyint(1) NOT NULL default '0',
  `sticky` tinyint(1) NOT NULL default '0',
  `moved_to` int(10) unsigned default NULL,
  `forum_id` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `punbb_topics_forum_id_idx` (`forum_id`),
  KEY `punbb_topics_moved_to_idx` (`moved_to`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;



REPLACE INTO `punbb_topics` VALUES (1, 'admin', 'Test post', 1133820143, 1133820143, 1, 'admin', 0, 0, 0, 0, NULL, 1);



CREATE TABLE IF NOT EXISTS `punbb_users` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `group_id` int(10) unsigned NOT NULL default '4',
  `username` varchar(200) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `email` varchar(50) NOT NULL default '',
  `title` varchar(50) default NULL,
  `realname` varchar(40) default NULL,
  `url` varchar(100) default NULL,
  `jabber` varchar(75) default NULL,
  `icq` varchar(12) default NULL,
  `msn` varchar(50) default NULL,
  `aim` varchar(30) default NULL,
  `yahoo` varchar(30) default NULL,
  `location` varchar(30) default NULL,
  `use_avatar` tinyint(1) NOT NULL default '0',
  `signature` text,
  `disp_topics` tinyint(3) unsigned default NULL,
  `disp_posts` tinyint(3) unsigned default NULL,
  `email_setting` tinyint(1) NOT NULL default '1',
  `save_pass` tinyint(1) NOT NULL default '1',
  `notify_with_post` tinyint(1) NOT NULL default '0',
  `show_smilies` tinyint(1) NOT NULL default '1',
  `show_img` tinyint(1) NOT NULL default '1',
  `show_img_sig` tinyint(1) NOT NULL default '1',
  `show_avatars` tinyint(1) NOT NULL default '1',
  `show_sig` tinyint(1) NOT NULL default '1',
  `timezone` float NOT NULL default '0',
  `language` varchar(25) NOT NULL default 'English',
  `style` varchar(25) NOT NULL default 'Oxygen',
  `num_posts` int(10) unsigned NOT NULL default '0',
  `last_post` int(10) unsigned default NULL,
  `registered` int(10) unsigned NOT NULL default '0',
  `registration_ip` varchar(15) NOT NULL default '0.0.0.0',
  `last_visit` int(10) unsigned NOT NULL default '0',
  `admin_note` varchar(30) default NULL,
  `activate_string` varchar(50) default NULL,
  `activate_key` varchar(8) default NULL,
  PRIMARY KEY  (`id`),
  KEY `punbb_users_registered_idx` (`registered`),
  KEY `punbb_users_username_idx` (`username`(8))
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;


REPLACE INTO `punbb_users` VALUES (1, 3, 'Guest', 'Guest', 'Guest', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 1, 0, 1, 1, 1, 1, 1, 0, 'English', 'Oxygen', 0, NULL, 0, '0.0.0.0', 0, NULL, NULL, NULL);
REPLACE INTO `punbb_users` VALUES (2, 1, 'admin', 'fa9beb99e4029ad5a6615399e7bbae21356086b3', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 1, 1, 0, 1, 1, 1, 1, 1, 0, 'English', 'Oxygen', 1, 1133820143, 1133820143, '127.0.0.1', 1133820143, NULL, NULL, NULL);
        
