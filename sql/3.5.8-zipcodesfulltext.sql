ALTER TABLE zipcodes DROP INDEX city;
ALTER TABLE zipcodes ADD FULLTEXT (`city`);
