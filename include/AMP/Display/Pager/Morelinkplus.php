<?php

require_once( 'AMP/Display/Pager/Content.php');

class AMP_Display_Pager_Morelinkplus extends AMP_Display_Pager_Content {
    function AMP_Display_Pager_Morelinkplus( ) {
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

    function render_top( ){
        if ( $this->_current_offset > 0 ) return parent::render( );
        return false;
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
                                AMP_TEXT_MORE  . $this->render_description( ) . $this->_renderer->space( ) . $this->_renderer->arrow_right( 2 ), 
                                array( 'class' => $this->_css_class_control ));
    }

    function render_description( ) {
        if( !isset( $this->_text_description )) return false;
        return $this->_text_description;
    }

}

?>
