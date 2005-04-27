ALTER TABLE sysvar ADD COLUMN database_version VARCHAR(16);
UPDATE sysvar SET database_version = '3.4.5';
