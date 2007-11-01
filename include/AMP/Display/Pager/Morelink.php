<?php

require_once( 'AMP/Display/Pager/Content.php');

class AMP_Display_Pager_Morelink extends AMP_Display_Pager_Content {

    function AMP_Display_Pager_Morelink( ) {
        $this->__construct( );
    }

    function render( ) {
        if ( $this->_current_offset > 0 ) return parent::render( );
        return 
            $this->_renderer->inDiv( 
                $this->render_controls( ),
                array( 'class' => $this->_css_class_container )
            );
    }

    function render_controls( ) {
        if ( $this->_current_offset > 0 ) return parent::render_controls( );
        return $this->render_more( );
    }

    function render_more( ) {
        if ( $this->_qty_total <= ( $this->_current_offset + $this->_qty_page )) {
            return false;
        }
        $target_url = $this->url_offset( $this->_current_offset + $this->_qty_page );
        return $this->_renderer->link( 
                                $target_url, 
                                AMP_TEXT_MORE  . $this->_renderer->space( ) . $this->_renderer->arrow_right( 2 ), 
                                array( 'class' => $this->_css_class_control ));
    }

    function _init_request( ) {
        $this->_request = array( );
    }

    function url_offset( $new_offset = 0 ) {
        return $this->_url_target;
    }

}

?>
