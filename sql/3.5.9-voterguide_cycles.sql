ALTER TABLE voterguides add column election_cycle varchar(128);
ALTER TABLE voterguides add column group_name varchar(64);
ALTER TABLE voterguide_styles add column `public` tinyint(1);
