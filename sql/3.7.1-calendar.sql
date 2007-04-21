alter table calendar add column rsvp varchar( 25 );
alter table calendar change column `repeat` repeat_event tinyint( 4 ) not null;
