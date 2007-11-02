<?php

require_once( 'AMP/Display/Pager.php' );

class AMP_Display_System_Pager extends AMP_Display_Pager {

    var $_jump_values = array( );
    var $current_jumps = false;

    function AMP_Display_System_Pager( ) {
        $this->__construct( );
    }

    function render_jump_set( ) {
        if ( $this->_qty_total < $this->_qty_page ) return false;
        if ( empty( $this->_jump_values )) return parent::render_jump_set( );
        return $this->_renderer->label( 'offset', AMP_TEXT_GO_TO_PAGE, array( 'class' => 'searchform_label' )) 
             . $this->_renderer->select( 'offset', $this->url_offset( $this->get_offset( )), $this->_jump_values, array( 'onChange' => 'window.location.href=this.value', 'class' => 'searchform_element') );
    }

    function trim( &$source ) {
        if ( !$this->get_limit( )) return;

        if ( is_array( $source )) {
            $source = array_slice( $source, $this->get_offset( ), $this->get_limit( ) );
        } elseif ( is_object( $source )) {
            $source->setLimit( $this->_qty_page );
            $source->setOffset( $this->_current_offset );
        }
    }

    function pull_jumps( $source, $index_property = 'name' ) {
        if( $this->current_jumps == ($index_property.'/'.$this->get_limit( ))) return;
        $this->current_jumps = $index_property.'/'.$this->get_limit( );
//--------------------------------------------------
// 
//         static $called = false;
//         if ( $called ) return;
//         if ( !$called ) $called = true;
//-------------------------------------------------- 
        if ( is_array( $source )) {
            $source_values = array_values( $source );
            for( $index = 0; $index < count( $source_values ); $index += $this->get_limit( )) {
                $jump_item = $source_values[ $index ];
                $this->_jump_values[ $index ] = $jump_item->getName( );
            }
            return;
        } 
        
        if ( method_exists( $source, 'getLookup')){
            $jump_index = $source->getLookup( $index_property, $sort=true );
        } elseif ( method_exists( $source, 'get_index')) {
            $jump_index= $source->get_index( $index_property, $this->get_limit( ));
        } else {
            $this->_jump_values = array( );
            $this->current_jumps = false;
            return false;
        }

        $jump_values = array_values( $jump_index );
        for( $index = 0; $index < count( $jump_index ); $index = $index + $this->get_limit( )) {
            $this->_jump_values[ $this->url_offset( $index ) ] = $jump_values[ $index ];
        }

    }

    function render_top( ) {
        if (( $this->_qty_total - $this->_current_offset ) == 1 ) return false;
        return
            $this->_renderer->inDiv( 
                        ( empty( $this->_jump_values ) ? '' :  $this->render_jump_set( ) . $this->_renderer->newline( ) )
                        . $this->render_position( )
                        . $this->_renderer->newline( )
                        . $this->render_controls( ),
                    array( 'class' => $this->_css_class_container )
                    );
            //. $this->_renderer->newline( 1, array( 'clear' => 'all'));
    }
}


?>
