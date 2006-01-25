alter table userdata_fields add column introidlist int(8);
alter table userdata_fields add column introiddetail int(8);
alter table userdata change column `timestamp` `timestamp` timestamp default CURRENT_TIMESTAMP;
alter table userdata add column `created_timestamp` timestamp NULL;
