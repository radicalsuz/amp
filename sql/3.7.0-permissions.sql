create table if not exists permission_items (
	id 		int(11)		not null AUTO_INCREMENT,
	action	varchar(30) null,
	target_type varchar(30) not null,
	target_id	int(11) not null,
	group_id	int(11) not null,
	user_id		int(11) not null,
	allow		int(6) default 1,
	PRIMARY KEY (id)
) ;
