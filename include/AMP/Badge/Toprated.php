<?php
require_once( 'AMP/Badge/Articles.php');
require_once( 'Modules/Rating/Rating.php');

function amp_badge_top_rated( $options = array( ) ) {
    $limit = ( isset( $options['limit'])  && is_numeric( $options['limit']))? $options['limit'] : 5;
    $section = ( isset( $options['section'])  && is_numeric( $options['section']))? $options['section'] : 0;

    $top_rated = AMP_lookup( 'article_ratings_by_section', $section );
    if ( !$top_rated ) return false;

    $display = array_chunk( $top_rated, $limit, $keep_keys = true );
    if ( $display && !empty( $display[0] )) {
        $options['id'] = array_keys( $display[0] );
        $options['morelink'] = false;
        unset( $options['section']);
        return amp_badge_articles( $options );
    }
}

?>
