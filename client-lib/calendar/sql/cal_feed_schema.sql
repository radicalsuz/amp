CREATE TABLE IF NOT EXISTS `calendar_feeds` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(250) NOT NULL default '',
  `title` varchar(250) NOT NULL default '',
  `link` varchar(250) default NULL,
  `description` varchar(250) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
