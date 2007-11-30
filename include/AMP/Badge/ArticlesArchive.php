<?php

/**
 * amp_badge_articles archive
 * class: default( blank ) set to a value to limit the archive to one class ( accepts comma-separated set )
 * limit: default( 24 ) set a max # of links to archived months. 2 years is the max and the default.
 */
function amp_badge_articles_archive( $options ) {
    $renderer = AMP_get_renderer( );
    $class = ( isset( $options['class']) && $options['class']) ? $options['class'] :false;
    $limit = ( isset( $options['limit']) && $options['limit']) ? $options['limit'] : 24;
    $articles_archive = $class ?    AMP_lookup( 'article_archives_by_month_by_class', $class )
                                :   AMP_lookup( 'article_archives_by_month' );
    if( !$articles_archive ) return false;
    $articles_archive = array_slice( array_keys( $articles_archive ), 0, $limit );
    $links = array( );
    $url_vars = array( );
    if( $class ) $url_vars['class'] = $class;
    foreach( $articles_archive as $date ) {
        $datetime = strtotime( $date );
        $url_vars['date[M]'] = date( 'm', $datetime );
        $url_vars['date[Y]'] = date( 'Y', $datetime );
        $links[] = $renderer->link( AMP_url_update( 'list.php', $url_vars ), $date);
    }
    return $renderer->UL( $links, array( 'class' => 'article-archives') );

}

?>
