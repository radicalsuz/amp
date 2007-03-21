create table if not exists badges (
	id int(11) not null AUTO_INCREMENT,
	name varchar(50) not null,
	html text not null,
	publish int(6) not null,
	gallery int(11) not null,
	include varchar(100) not null,
	include_function varchar(100) not null,
    PRIMARY KEY ( id )
) TYPE = MyISAM;
