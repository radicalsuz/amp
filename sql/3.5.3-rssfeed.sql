alter table rssfeed add column section_id int(8) null;
alter table rssfeed add column class_id int(8) null;
alter table rssfeed add column combine_logic varchar(8) null;
alter table rssfeed add column include_full_content tinyint(4) default 0;
