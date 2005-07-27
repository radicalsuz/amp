CREATE TABLE IF NOT EXISTS `scheduleitems` (
  `id` int(11) NOT NULL auto_increment,
  `service` varchar(30) NULL,
  `owner_id` int(11) NULL,
  `start_time` DATETIME NULL,
  `stop_time` DATETIME NULL,
  `title` varchar(70) NULL,
  `description` text NULL,
  `capacity` int(7),
  `status` varchar(20),
  `location` text NULL,
  `timezone` varchar(10),
  `schedule_id` int(11),
  PRIMARY KEY (`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `schedules` (
	`id` int(11) NOT NULL auto_increment,
	`name` varchar (30) NULL,
	`description` text null,
	`userdata_fields_id` int(11) null,
	PRIMARY KEY (`id`)
) TYPE = MyISAM;

CREATE TABLE IF NOT EXISTS `userdata_actions` (
  `id` int(11) NOT NULL auto_increment,
  `service` varchar(30) NULL,
  `userdata_id` int(11) NULL,
  `action_id` int(11) NULL,
  `updated` TIMESTAMP(14),
  `created` TIMESTAMP(14),
  PRIMARY KEY (`id`)
) TYPE= MyISAM;
