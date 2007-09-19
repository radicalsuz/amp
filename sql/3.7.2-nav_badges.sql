alter table navtbl add column badge_id int( 9 ) null;
replace into badges ( id, name, include, include_function, publish ) values (  30, "Articles", "AMP/Badge/Articles.php", "amp_badge_articles", 1 );
replace into badges ( id, name, include, include_function, publish ) values (  29, "Related Forms", "AMP/Badge/RelatedForms.php", "amp_badge_related_form", 1 );
alter table badges add column include_function_args text null;
