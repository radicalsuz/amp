ALTER TABLE voterguides add column election_cycle varchar(128);
ALTER TABLE voterguides add column group_name varchar(64);
ALTER TABLE voterguide_styles add column `public` tinyint(1);
alter table voterguide_styles change column url url varchar(128);
alter table voterguide_styles change column fullscreen_url fullscreen_url varchar(128);
