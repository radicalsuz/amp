create table if not exists ratings (
    id int( 11 ) null AUTO_INCREMENT,
    user_id int( 11 ) not null,
    session varchar( 200 ) not null,
    item_type varchar( 50 ) not null,
    item_id int( 11 ) not null,
    rating int( 8 ) not null,
    updated_at TIMESTAMP,
    PRIMARY KEY ( id ) 

)  TYPE = MyISAM;

replace into badges ( id, publish, name, include, include_function ) values ( 10, 1, "Rating Update", "AMP/Badge/Rating.php", "amp_badge_rating" );
replace into badges ( id, publish, name, include, include_function ) values ( 11, 1, "Rate this Article", "AMP/Badge/Rating.php", "amp_badge_rating_block" );
