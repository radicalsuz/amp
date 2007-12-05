function AMP_rate_item( value, article_id ) {
    if ( !load_proto( ) ) {
        setTimeout( function( ){
            AMP_rate_item( value, article_id );
        }, 1000 );
        return ;
    }

    new Ajax.Updater( 'rating', "/badge_widget.php?id=10&format=xml", 
                    {   method: 'post', 
                        parameters: { 
                            rating: value, 
                            article_id: article_id 
                            }
                        }
                        );
}


