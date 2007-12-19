alter table sysvar add column akismet_key varchar( 50 ) null;
alter table userdata add column spam tinyint( 4 ) DEFAULT 0;
alter table comments add column spam tinyint( 4 ) DEFAULT 0;
