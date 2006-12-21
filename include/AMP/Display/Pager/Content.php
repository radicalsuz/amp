<?php

require_once( 'AMP/Display/Pager.php' );

class AMP_Display_Pager_Content extends AMP_Display_Pager {
    function AMP_Display_Pager_Content ( ) {
        $this->__construct( );
    }

    function render_links( ) {
        return 
            $this->render_first( ) . $this->_renderer->space( )
            . $this->render_last( ) . $this->_renderer->newline( )
            . $this->render_all( );
    }
}

?>
