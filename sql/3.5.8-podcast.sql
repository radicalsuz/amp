
CREATE TABLE `podcast` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `subtitle` varchar(255) NOT NULL default '',
  `author` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL default '',
  `copyright` varchar(255) NOT NULL default '',
  `language` varchar(255) NOT NULL default '',
  `category` varchar(255) NOT NULL default '',
  `ttl` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;


CREATE TABLE `podcast_item` (
  `id` int(11) NOT NULL auto_increment,
  `publish` tinyint(4) NOT NULL default '0',
  `podcast` int(11) NOT NULL default '0',
  `file` varchar(255) NOT NULL default '',
  `length` varchar(255) NOT NULL default '',
  `title` varchar(255) NOT NULL default '',
  `subtitle` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL default '',
  `date` date NOT NULL default '0000-00-00',
  `keywords` text NOT NULL,
  `copyright` varchar(255) NOT NULL default '',
  `category` varchar(255) NOT NULL default '',
  `explicit` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;
