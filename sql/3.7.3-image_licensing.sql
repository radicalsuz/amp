create table if not exists `images` (
	`id`		int(11) null AUTO_INCREMENT,
	`name`		varchar( 100 ) not null,
	`caption`   text null,
	`alt`	    varchar( 250 ) not null,
	`author`    varchar( 100 ) not null,
	`author_url`    varchar( 250 ) not null,
	`date`		date not null,
	`map_lon`	float not null,
	`map_lat`	float not null,	
	`width`		int(8) not null,
 	`height`	int(8) not null,	
	`publish`	int(4) not null,
	`license`	text not null,
	`license_url` varchar(150) not null,
	`foreign_key` varchar(100) not null,
	`created_by` int(8) not null,
	`updated_at` timestamp(14) default CURRENT_TIMESTAMP,
	`created_at` timestamp(14),
	PRIMARY KEY ( id )
) TYPE = MyISAM;
