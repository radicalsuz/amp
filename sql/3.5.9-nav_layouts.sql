create table if not exists nav_layouts (
	id 		int(8) 	not null AUTO_INCREMENT,
	name	varchar(250) null,
	introtext_id 	int(11) null,
	class_id		int(11) null, 
	section_id		int(11) null,
	section_id_list int(11) null,
	PRIMARY KEY (id )
);
alter table nav add column layout_id int(11) null;
