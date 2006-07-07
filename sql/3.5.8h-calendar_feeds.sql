create table if not exists calendar_feeds (
	id			int(11)			not null auto_increment,
	name		varchar(255)	null,
	publish		boolean			null,
	title		varchar(255)	null,
	url			varchar(255)	null,
	link		varchar(255)	null,
	description	text			null,
	last_update timestamp		null,
	primary key(id)
);

alter table calendar add column feed_id int(11) null;
