<?php
require_once( 'Modules/Rating/Rating.php');
require_once( 'Modules/Rating/Public/Display.php');
require_once( 'AMP/Content/Article.inc.php');

function amp_badge_rating_block( $options ) {
    $page = &AMPContent_Page::instance( );
    $article = $page->getArticle( );
    if ( !$article ) return false; 

    $header = &AMP_get_header( ) ;
    $header->addJavaScript( '/scripts/rating.js', 'ratings');
    $header->addJavaScript( '/scripts/ajax/prototype.js', 'prototype');
    
    $header->addJavascriptOnload( 
        "new Ajax.Updater( 'rating', '/badge_widget.php?id=10&cache=0&format=xml&article_id=" . $article->id . "' );", 'ratings_loader' );
    
    $display = new Rating_Public_Display( $article );
    return $display->render_block( $article );
    
}

function amp_badge_rating( $options ) {
    $queryd_item = ( isset( $_GET['article_id']) && $_GET['article_id'] )? intval( $_GET['article_id'] ) : 0;
    if( $queryd_item ) {
        $article = new Article( AMP_Registry::getDbcon( ), $queryd_item );
        $display = new Rating_Public_Display( $article );
        return $display->execute( );
    }
    $rating = ( isset( $_POST['rating']) && $_POST['rating'] )? intval( $_POST['rating'] ) : 1;
    $posted_item = ( isset( $_POST['article_id']) && $_POST['article_id'] )? intval( $_POST['article_id'] ) : 0;
    $article = new Article( AMP_Registry::getDbcon( ), $posted_item );
    $display = new Rating_Public_Display( $article );
    if( $rating && $posted_item && AMP_SYSTEM_UNIQUE_VISITOR_ID && is_numeric( $rating ) && is_numeric( $posted_item )) {
        //actually record the rating somewhere....
        ArticleRating::create( $posted_item, $rating );
    }
    return $display->execute( );
}

?>
