create table if not exists tags (
id 				int 			not null auto_increment,
	human_name 		varchar(255)	not null,
	name			varchar(80)		not null,
	blurb			text			not null,
	status			int				not null,
	image			varchar(80)		not null,
	primary key(id)
);
create table if not exists tags_content (
	id 				int 			not null auto_increment,
	tag_id	 		int 			not null,
	content_type 	enum( 'article', 'section', 'image', 'tag', 'userdata', 'link', 'event', 'action' ) 
									not null,
	content_foreign_key		
					int				not null,
	user_id			int				not null, 
	primary key( id )
);

create table if not exists media (
	id				int 			not null auto_increment,
	owner_id		int(11)			null,
	name			varchar(255) 	null,
	blurb			text			null,
 	url				varchar(255)	null,
	category 		enum('book', 'CD', 'DVD', 'magazine' )
									null,
	foreign_key 	int(11)			null,
	image			varchar(255)	null,
	status			int				null,
	primary key( id )
);
