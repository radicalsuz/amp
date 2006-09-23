drop table tags;

create table tags ( 
	id int(11) not null AUTO_INCREMENT,
	name varchar(50) not null,
	description text null,
	image	varchar(60) null,
	publish int(6) null,
	PRIMARY KEY (id)
);

insert into moduletext (id, name, title ) values ( 28, 'Tag Listing', 'Tags' );
replace into tags ( id, name, publish ) values ( 1, 'New', 0 );
replace into tags ( id, name, publish ) values ( 2, 'Front Page', 0 );
replace into tags ( id, name, publish ) values ( 20, 'User Added', 0 );
