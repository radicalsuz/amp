ALTER TABLE payment add column auth_code varchar(8) null;
ALTER TABLE payment add column transaction_id varchar(25) null;
ALTER TABLE payment add column requesting_ip varchar(20) null;
