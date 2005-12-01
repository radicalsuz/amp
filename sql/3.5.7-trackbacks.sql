ALTER TABLE comments add column author_IP varchar(100) null;
ALTER TABLE comments add column author_url varchar(100) null;
ALTER TABLE comments add column karma int(11) null;
ALTER TABLE comments add column agent varchar(255) null;
ALTER TABLE comments add column `type` varchar(20) null;
ALTER TABLE comments add column parent bigint(20) null;
ALTER TABLE comments add column user_id bigint(20) null;
