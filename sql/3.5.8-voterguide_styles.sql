ALTER TABLE voterguides add column style int(11) null;
create table if not exists voterguide_styles (
	id				int				not null auto_increment,
	name			varchar(64)		not null,
	author			varchar(64)		not null,
	url				varchar(64)		not null,
	fullscreen_url	varchar(64)		not null,
	primary key(id)
);
