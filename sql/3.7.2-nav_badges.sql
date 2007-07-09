alter table badges add column include_function_args text null;
alter table nav add column badge_id int( 9 ) null;
replace into badges ( id, name, include, include_function, include_function_args, publish ) values (  30, "Articles", "AMP/Badge/Articles.php", "amp_badge_articles", "", 1 );
replace into badges ( id, name, include, include_function, include_function_args, publish ) values (  29, "Related Forms", "AMP/Badge/RelatedForms.php", "amp_badge_related_forms", "", 1 );
