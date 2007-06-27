<?php

function nav_articles_by_date( ) {
    $page = &AMPContent_Page::instance( );
    if ( $class = $page->getClassId( )) {
        $date_values = AMP_lookup( 'classArticlesByDate', $class );
    } else {
        $date_values = AMP_lookup( 'articlesByDate');
    }
    if ( !$date_values ) {
        return false;
    }

    $output = array( );
    $renderer = AMP_get_renderer( );

    foreach( $date_values as $pretty_date => $qty ) {
        $real_date = strtotime( $pretty_date );
        $url['year']= date( 'Y' , $real_date );
        $url['month'] = date( 'm' , $real_date );
        $url['offset'] = false;
        $url['qty'] = false;
        $new_url = AMP_url_update( $_SERVER['REQUEST_URI'], $url );
        $output[] = $renderer->link( $new_url, $pretty_date, array( 'class' => AMP_CONTENT_CSS_CLASS_NAV_LINK ) );
        if ( count( $output ) == AMP_CONTENT_NAV_ARCHIVE_LIMIT ) {
            break;
        }
    }
    return join( $renderer->newline( ), $output );
}


?>
