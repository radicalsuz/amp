<?php
require_once( 'AMP/Badge/Articles.php');

function amp_badge_most_emailed( $options = array( ) ) {
    $limit = ( isset( $options['limit'])  && is_numeric( $options['limit']))? $options['limit'] : 5;
    $section = ( isset( $options['section'])  && is_numeric( $options['section']))? $options['section'] : 0;

    $emailed = AMP_lookup( 'most_emailed_articles', $section );
    if ( !$emailed ) return false;

    $display = array_chunk( $emailed, $limit, $keep_keys = true );
    if ( $display && !empty( $display[0] )) {
        $options['id'] = array_keys( $display[0] );
        unset( $options['section']);
        return amp_badge_articles( $options );
    }
}

?>
