alter table navtbl add column badge_id int( 9 ) null;
alter table navtbl add column include_function_args text null;
alter table navtbl change column include_file include_file varchar(100);
alter table navtbl change column include_function include_function varchar(100);
