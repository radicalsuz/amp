create table if not exists template_archives (
	archive_id int (11) null AUTO_INCREMENT,
	id int (11) null,
	name varchar(100) null,
	header2 text null,
	lnav3 text null,
	lnav4 text null,
	lnav7 text null,
	lnav8 text null,
	lnav9 text null,
	rnav3 text null,
	rnav4 text null,
	rnav7 text null,
	rnav8 text null,
	rnav9 text null,
	css text null,
	imgpath varchar(100) null,
	extra_header text null,	
	archived_at TIMESTAMP,
	PRIMARY KEY (archive_id)

		
) TYPE = MyISAM;
