<?php

class AMP_Display_Cloud {

    var $_items;
    var $_item_qtys;
    var $_item_classes;
    
    var $_display_qty = 50;
    var $_display_offset = 0;

    var $_size_steps = 5;
    var $_css_class_base = 'AMP_cloud_link_';

    function AMP_Display_Cloud( &$source_items, $qty_set ) {
        $this->__construct( $source_items, $qty_set );
    }

    function __construct( &$source_items, $qty_set ) {
        $this->_sort_source( $source_items, $qty_set );
        $this->_renderer = AMP_get_renderer( );
    }

    function _sort_source( &$source_items, $qty_set ) {
        $sorted_order = array( );
        foreach( $source_items as $item_key => $item ) {
            if ( !isset( $qty_set[$item->id] )) continue; 
            $sorted_order[$item_key] = $qty_set[$item->id];
        }
        $tag_names = AMPSystem_Lookup::instance( 'tags');

        arsort( $sorted_order );

        //$display_order = array_keys( array_slice( $sorted_order, $this->_display_offset, $this->_display_qty ));
        if ( count( $sorted_order ) > $this->_display_qty ) {
            $display_order = array( );
            foreach( $sorted_order as $item_id => $item_qty ) {
                $display_order[] = $item_id;
                if ( count( $display_order ) == $this->_display_qty ) {
                    exit;
                }
            }
        } else {
            $display_order = array_keys( $sorted_order );
        }
        

        $this->_items = array_combine_key( $display_order, $source_items );
        $this->_item_qtys = array_combine_key( $display_order, $qty_set );

        $max_qty = max( $this->_item_qtys );
        $min_qty = min( $this->_item_qtys );

        foreach( $this->_items as $item_id => $item ) {
            if ( !isset( $this->_item_qtys[$item->id])){
                unset( $this->_items[ $item->id ]);
                continue;
            }
            $this->_assign_class( $item->id, $this->_item_qtys[ $item->id ], $max_qty, $min_qty );
        }
        $item->sort( $this->_items );

    }

    function _assign_class( $item_id, $item_qty, $max_qty, $min_qty = 0 ) {
        $step_size = ( $max_qty - $min_qty ) / $this->_size_steps;
        for( $n = 0;$n<$this->_size_steps;$n++) {
            if ( $item_qty >= ( $max_qty -( $n * $step_size ))){
                $this->_item_classes[ $item_id ] = $this->_css_class_base . ( $n + 1 );
                return;
            }
        }
        $this->_item_classes[ $item_id ] = $this->_css_class_base . $this->_size_steps;
    }

    function render_item( &$item ) {
        return $this->_renderer->link( 
                        $item->getURL( ),
                        $item->getName( ),
                        array( 'class' => $this->_item_classes[ $item->id ])
                    )
                . $this->_renderer->space_break( )  ;
        
    }

    function render_header( ) {
        return false;
    }

    function render_footer( ) {
        return false;
    }


    function execute( ) {
        $output = '';
        foreach( $this->_items as $item ) {
            $output .= $this->render_item( $item ) ;
        }

        $this->_init_css( );
        return $this->render_header( )
                . $output
                . $this->render_footer( );
    }

    function _init_css( ) {
        $css_source = '';
        for( $n = 0;$n<$this->_size_steps;$n++) {
            $css_source .= 
            '.' . $this->_css_class_base . ( $n + 1) . '{' . "\n"
            . 'font-size: ' . ( 1 + ( .25 * ( $this->_size_steps - $n ))) . 'em;' . "\n"
            . 'color: #FFFFFF;' . "\n"
            . '}' . "\n";
        }
        $header = &AMP_get_header( );
        $header->addStylesheetDynamic( $css_source, 'AMP_cloud'.get_class( $this ));
    }

}


?>