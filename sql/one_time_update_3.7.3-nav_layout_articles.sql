replace into moduletext ( id, name, title, modid, type ) values ( 3, 'Article', 'Article', 19, 1 );
insert into nav_layouts (  name, introtext_id, class_id, section_id, section_id_list ) select name, introtext_id, class_id, section_id, section_id_list from nav_layouts where id=3;
update nav set layout_id=LAST_INSERT_ID( ) where layout_id=3;
replace into nav_layouts values (  3, 'Articles', 3, NULL, NULL, NULL );
insert into nav ( name, moduleid, navid, position, typelist, typeid, sublist, subid, catlist, catid, classlist, classid, layout_id ) select name, moduleid, navid, position, typelist, typeid, sublist, subid, catlist, catid, classlist, classid, 3 as new_layout_id from nav where layout_id=1;
