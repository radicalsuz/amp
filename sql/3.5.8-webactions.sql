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

CREATE TABLE IF NOT EXISTS webaction_targets (
	id 						int(11) 	not null	AUTO_INCREMENT,
	First_Name				varchar(250),
	Last_Name				varchar(250),
	Title 					varchar(60),
	occupation				varchar(100),
	Email					varchar(120),
	Work_Fax				varchar(40),
	District				varchar(40),
	Office					varchar(50),
	region					varchar(50),
	publish					int(4),
	PRIMARY KEY (id)
);
