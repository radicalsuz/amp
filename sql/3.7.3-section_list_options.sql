alter table articletype add column list_sort varchar(50) null;
alter table articletype add column list_is_global tinyint(4) null;
alter table articletype add column list_by_class varchar(100) null;
alter table articletype add column list_by_section varchar(100) null;
alter table articletype add column list_by_tag varchar(255) null;
alter table articletype add column search_display tinyint(4) null;
