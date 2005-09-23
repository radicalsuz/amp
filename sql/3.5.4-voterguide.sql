CREATE TABLE IF NOT EXISTS `voterguide_positions` (
  `id` int(11) NOT NULL auto_increment,
  `item` varchar(255) NULL,
  `headline` text NULL,
  `position` tinyint(4) NOT NULL default '0',
  `comments` text,
  `voterguide_id` int(11) default NULL,
  `textorder` int(11) default NULL,
  PRIMARY KEY  (`id`)
);


CREATE TABLE IF NOT EXISTS `voterguides` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar( 255 ) NOT NULL,
  `blurb` text NULL,
  `footer` text NULL,
  `city` varchar( 50 ) NULL,
  `state` varchar( 10 ) NULL,
  `owner_id` int(11) NULL,
  `election_date` datetime NULL,    
  `publish` tinyint(4) NULL,
  `redirect_name` varchar( 20 ) NULL,
  `affiliation` varchar( 60 ) NULL,
  `filelink` varchar( 60 ) NULL,
  `picture` varchar( 60 ) NULL,
  `block_id` int( 11 ) NULL,
  PRIMARY KEY (id)
)
