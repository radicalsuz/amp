<?php
require_once( 'AMP/Badge/Articles.php');

function amp_badge_most_commented( $options = array( ) ) {
    $limit = ( isset( $options['limit'])  && is_numeric( $options['limit']))? $options['limit'] : 5;
    $section = ( isset( $options['section'])  && is_numeric( $options['section']))? $options['section'] : 0;

    $commented = AMP_lookup( 'most_commented_articles', $section );
    if ( !$commented ) return false;

    $display = array_chunk( $commented, $limit, $keep_keys = true );
    if ( $display && !empty( $display[0] )) {
        $options['id'] = array_keys( $display[0] );
        unset( $options['section']);
        return amp_badge_articles( $options );
    }
}

?>
