<?php

class Article_Display_Status {
    var $_renderer;

    function Article_Display_Status( ) {
        $this->__construct( );
    }

    function __construct( ){
        $this->_renderer = AMP_get_renderer( );
    }

    function execute( ) {
        $revised = AMP_lookup( 'articles_in_revision' );
        $pending = AMP_lookup( 'articles_pending' );
        if ( !( $revised || $pending )) return false;
        $link = "<a href='/system/article.php?publish=%s&AMPSearch=Search'>%s items</a> currently %s";

        $results = array( );
        if ( $revised ) {
            $results[] = $this->_renderer->div( sprintf( $link, AMP_CONTENT_STATUS_REVISION, count( $revised), AMP_TEXT_CONTENT_STATUS_REVISION ), array( 'class' => 'list_item' ));
        }
        if ( $pending ) {
            $results[] = $this->_renderer->div( sprintf( $link, AMP_CONTENT_STATUS_PENDING, count( $pending), AMP_TEXT_CONTENT_STATUS_PENDING ), array( 'class' => 'list_item' ));
        }
        return    $this->_renderer->div( AMP_TEXT_CONTENT_STATUS_DISPLAY_HEADING, array( 'class' => 'system_heading'))
                . $this->_renderer->div( join( '', $results ), array( 'class' => 'status_display' ));
    }

}


?>
