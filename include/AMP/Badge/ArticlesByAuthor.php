<?php
require_once( 'AMP/Badge/Articles.php');

function amp_badge_articles_by_author( $options ) {
    $author = ( isset( $options['author']) && $options['author'] ) ? $options['author'] : false;
    if( !$author ) {
        $page = AMPContent_Page::instance( );
        $article = $page->getArticle( );
        if( !$article ) return false;
        $options['not_id'] = $article->id;

        $author = $article->getAuthor( );
        if( !$author ) return false;
    }
    unset( $options['section']);
    unset( $options['class']);
    $options['author'] = $author;
    return amp_badge_articles( $options );
}
?>
