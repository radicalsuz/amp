<?php

require_once( 'AMP/Display/Pager.php' );

class AMP_Display_System_Pager extends AMP_Display_Pager {

    var $_jump_values = array( );

    function AMP_Display_System_Pager( ) {
        $this->__construct( );
    }

    function render_jump_set( ) {
        if ( $this->_qty_total < $this->_qty_page ) return false;
        if ( empty( $this->_jump_values )) return parent::render_jump_set( );
        return $this->_renderer->select( 'offset', $this->url_offset( $this->get_offset( )), $this->_jump_values, array( 'onChange' => 'window.location.href=this.value', ) );
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

    function pull_jumps( $source, $index = 'name' ) {
        static $called = false;
        if ( $called ) return;
        if ( !$called ) $called = true;
        if ( is_array( $source )) {
            $source_values = array_values( $source );
            for( $index = 0; $index < count( $source_values ); $index += $this->get_limit( )) {
                $jump_item = $source_values[ $index ];
                print get_class( $jump_item );
                $this->_jump_values[ $index ] = $jump_item->getName( );
            }
        } else {
            $jump_index = $source->getLookup( $index, $sort=true );
            $jump_values = array_values( $jump_index );
            for( $index = 0; $index < count( $jump_index ); $index = $index + $this->get_limit( )) {
                $this->_jump_values[ $this->url_offset( $index ) ] = $jump_values[ $index ];
            }
        }

    }

}


?>
