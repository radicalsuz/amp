CREATE TABLE IF NOT EXISTS webactions (
	id 						int(11)		not null	AUTO_INCREMENT,
	name 					varchar(250)	null,
	blurb					text			null,
	intro_id				int(11)		null,
	response_id 			int(11) 	null,
	message_id  			int(11) 	null,
	tellfriend_message_id 	int(11) 	null,
	target_id				varchar(30)	null,
    target_method           varchar(30) null,
	status 		varchar(10)				null,
	enddate		datetime				null,
	modin		int(11)					null,
	PRIMARY KEY (id)
);
