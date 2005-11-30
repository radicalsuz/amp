CREATE TABLE IF NOT EXISTS webactions (
	id 						int(11)		not null	AUTO_INCREMENT,
	title					varchar(250)	null,
	intro_id				int(11)		null,
	response_id 			int(11) 	null,
	message_id  			int(11) 	null,
	tellfriend_message_id 	int(11) 	null,
	target_id				varchar(30)	null,
	enddate		datetime				null,
	modin		int(11)					null,
	PRIMARY KEY (id)
);
