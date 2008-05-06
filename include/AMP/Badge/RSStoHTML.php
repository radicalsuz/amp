<?php
if (!defined( 'MAGPIE_DIR' )) define('MAGPIE_DIR', 'magpierss'.DIRECTORY_SEPARATOR);
require_once( MAGPIE_DIR.'rss_fetch.inc' );
function amp_badge_rss_to_html( $options ) {
    $feed_url = ( isset( $options['url']) && $options['url'] ) ? $options['url'] : false;
    $error_level_tmp = error_reporting();
    error_reporting( E_ERROR );
    $rss = fetch_rss( $feed_url );
    error_reporting( $error_level_tmp );

    
    $renderer = AMP_get_renderer( );
    $output = '';
    $header_output = '';
    foreach( $rss as $result ) {
        if( is_array( $result ) && isset( $result['title']) && $result['title']) {
            $header_output .= $renderer->tag( 'h3', 
                      $renderer->link( $result['link'], $result['title'] ),
                      array( 'class' => 'feed title'));
            continue;

        }
        foreach( $result as $key => $value ) {
            if( is_array( $value )) {
                $output .= $renderer->link( $value['link'], $value['title'], array( 'class' => 'feed link' )) . "<br />\n";
            } 
        }
        $output .= "<br />\n<br />\n";
    }

    return $header_output . $output;

}
?>
