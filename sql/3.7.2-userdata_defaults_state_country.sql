alter table userdata_fields change column lookup_State lookup_State varchar( 255 ) null DEFAULT 'ampsystemlookup_regions_us';
alter table userdata_fields change column lookup_Country lookup_Country varchar( 255 ) null DEFAULT 'ampsystemlookup_regions_world';
