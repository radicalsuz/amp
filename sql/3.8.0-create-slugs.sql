create table if not exists route_slugs (
  id 				int 			not null auto_increment,
	name 		   varchar(255) 	not null,
	owner_type varchar(80)		not null,
	owner_id   int(11)		    not null,
	primary key(id)
  );
