<?php

function amp_articles_tagged( $tagname = false ) {

    if ( !$tagname ) return false;

    $tag_ids = AMP_lookup( 'tagsSimple' );
    $found_id = array_keys( $tag_ids, strtolower( $tagname ));
    if ( !$found_id ) return;
    $tag_id = $found_id[0];

    require_once( 'AMP/Content/Article.inc.php');
    $source = new Article( AMP_Registry::getDbcon( ));
    $article_set = $source->find( array( 'tag' => $tag_id ));
    $source->sort( $article_set, 'itemDate', AMP_SORT_DESC );
    $renderer = AMP_get_renderer( );

    foreach( $article_set as $item ) {
        $output .= $renderer->link( $item->getURL( ), AMP_trimText( $item->getName( ), 50, false), array( 'class' => 'sidelist'))
                    . $renderer->newline( );

    }

    return $output;

}


?>
