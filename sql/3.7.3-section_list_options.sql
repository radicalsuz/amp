alter table articletype add column list_is_global tinyint(4) null;
alter table articletype add column list_by_class varchar(100) null;
alter table articletype add column list_by_section varchar(100) null;
alter table articletype add column list_by_tag varchar(255) null;
