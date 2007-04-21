alter table userdata_fields change column lookup_State lookup_State varchar( 255 ) null DEFAULT 'AMPSystemLookup_Regions_US';
alter table userdata_fields change column lookup_Country lookup_Country varchar( 255 ) null DEFAULT 'AMPSystemLookup_Regions_World';
