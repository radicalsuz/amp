
/* warning, these suckers block and take a long time if there's lots of rows */

CREATE INDEX `index_userdata_on_modin` ON userdata (`modin`);
CREATE INDEX `index_userdata_on_state` ON userdata (`State`(2));
/* CREATE INDEX `index_userdata_on_publish` ON userdata (`publish`); unnecessary, never better than modin or state */

CREATE INDEX `index_articles_on_publish` ON articles (`publish`);
CREATE INDEX `index_articles_on_class` ON articles (`class`);
/* CREATE INDEX `index_articles_on_type` ON articles (`type`); already done */

/*
too expensive to create for now
CREATE INDEX `index_articles_on_class_and_publish` ON articles (`class`,`publish`);
and / or
CREATE INDEX `index_articles_on_publish_and_class` ON articles (`publish`,`publish`);

CREATE INDEX `index_articles_on_type_and_publish` ON articles (`type`,`publish`);
and / or
CREATE INDEX `index_articles_on_publish_and_type` ON articles (`publish`,`type`);

not sure if its worth it
CREATE INDEX `index_articles_on_date` ON articles (`date`);
CREATE INDEX `index_articles_on_pageorder` ON articles (`pageorder`);
CREATE INDEX `index_articles_on_class_and_type_and_publish_and_date_and_pageorder` ON articles (`class`,`type`,`publish`,`date`,`pageorder`);
*/

CREATE INDEX `index_articlereltype_on_articleid` ON articlereltype (`articleid`);
CREATE INDEX `index_articlereltype_on_typeid` ON articlereltype (`typeid`);
CREATE INDEX `index_articlereltype_on_articleid_and_typeid` ON articlereltype (`articleid`,`typeid`);

CREATE INDEX `index_calendar_on_date` ON calendar (`date`);
/* 
NOTES:

other possible columns for index
userdata:
custom1='Have Housing' | 'Need Housing' = length 4 index
custom6='Need a Ride' | 'Have a Ride to Offer' (same)
*/
