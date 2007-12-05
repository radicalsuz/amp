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

