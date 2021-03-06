<?php

require_once( 'AMP/Display/Pager.php' );

class AMP_Display_Pager_Content extends AMP_Display_Pager {
    function AMP_Display_Pager_Content ( ) {
        $this->__construct( );
    }

    function render_links( ) {
        $links =  
            $this->render_first( ) . $this->_renderer->space( )
            . $this->render_last( ) . $this->_renderer->newline( );

        if ( !$this->_enable_all || ( $this->_qty_total > AMP_CONTENT_LIST_DISPLAY_MAX )) {
            return $links;
        }

        return $links.$this->render_all( );
    }
    function render_top( ) {
        if ( $this->is_first_page( )) return false;
        return parent::render_top( );
    }

    function is_first_page( ) {
        return ( !( $this->_current_offset > 1 ));
    }
}

?>
