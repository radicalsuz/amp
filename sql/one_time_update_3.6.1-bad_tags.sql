drop table tags;

create table tags ( 
	id int(11) not null AUTO_INCREMENT,
	name varchar(50) not null,
	description text null,
	image	varchar(60) null,
	publish int(6) null,
	PRIMARY KEY (id)
);

insert into moduletext (id, name, title ) values ( 66, 'Tag Listing', 'Tags' );
