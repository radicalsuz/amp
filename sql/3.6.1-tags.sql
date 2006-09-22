create table if not exists tags (
	id int(11) not null AUTO_INCREMENT,
	name varchar(50) not null,
	description text null,
	image	varchar(60) null,
	publish int(6) null,
	PRIMARY KEY (id)
);

create table if not exists tags_items (
	id 			int(11) not null AUTO_INCREMENT,
	item_type	enum( 'article', 'gallery_image', 'link', 'form', 'event', 'file', 'gallery' ) not null,
	item_id		varchar(60) not null,
	tag_id		int(11) not null,
    user_id     int(11) not null,
    created_at  timestamp,
	PRIMARY KEY (id)
);

replace into tags ( id, name, publish ) values ( 1, 'New', 0 );
replace into tags ( id, name, publish ) values ( 2, 'Front Page', 0 );
replace into tags ( id, name, publish ) values ( 20, 'User Added', 0 );
