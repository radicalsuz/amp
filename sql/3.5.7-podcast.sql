CREATE TABLE if not exists `podcast` (
  `id` int(11) NOT NULL auto_increment,
  `title` text NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `author` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `copyright` varchar(255) NOT NULL,
  `language` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `ttl` varchar(50) NOT NULL,
  PRIMARY KEY  (id)
); 


CREATE TABLE if not exists `podcast_item` (
  `id` int(11) NOT NULL auto_increment,
  `publish` tinyint(4) NOT NULL default '0',
  `podcast` int(11) NOT NULL,
  `file` varchar(255) NOT NULL,
  `length` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `subtitle` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `author` varchar(255) NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `keywords` text NOT NULL,
  `copyright` varchar(255) NOT NULL,
  `category` varchar(255) NOT NULL,
  `explicit` varchar(255) NOT NULL,
  `last_modified` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (id)
) ;
