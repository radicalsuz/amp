alter table articles add column `media_filename` varchar(128) null;
alter table articles add column `media_html` text null;
alter table articles add column `media_list_display` tinyint(6);
alter table moduletext add column `media_filename` varchar(128) null;
alter table moduletext add column `media_html` text null;
alter table moduletext add column `media_list_display` tinyint(6);
