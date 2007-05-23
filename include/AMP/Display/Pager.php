<?php

class AMP_Display_Pager {

    var $_renderer;

    var $_qty_total;
    var $_qty_page;

    var $_request;

    var $_current_page = 1;
    var $_current_offset;

    var $_css_class_link      = "pager_link";
    var $_css_class_container = "list_pager";
    var $_css_class_control = 'standout';
    var $_css_class_jump = 'page_jump_link';
    var $_css_class_jump_highlight = 'current_page_jump_link';

    var $_text_next = AMP_TEXT_PAGER_NEXT;
    var $_text_previous = AMP_TEXT_PAGER_PREVIOUS;
    var $_text_first = AMP_TEXT_PAGER_FIRST;
    var $_text_last  = AMP_TEXT_PAGER_LAST;
    var $_text_all   = AMP_TEXT_PAGER_ALL;

    var $_enable_all  = true;

    var $_url_target;

    function AMP_Display_Pager( ) {
        $this->__construct( );
    }

    function __construct( ) {
        $this->_renderer = AMP_get_renderer( );
        $this->_init_request( );

    }

    function _init_request( ) {
        $this->_request = AMP_url_read( );
        if ( !$this->_request) {
            $this->_request = array( );
            return;
        }

        if ( ( $limit = $this->assert_var( 'limit')) || ( $limit = $this->assert_var( 'qty'))) {
            $this->set_limit( $limit );
        }
        if ( $offset = $this->assert_var( 'offset')) {
            $this->set_offset( $offset );
        }

        $this->set_target( $_SERVER['PHP_SELF']);
        //
        //init view all settings
        //

    }

    function set_page( $page ) {
        $this->_current_page = $page;
    }

    function set_total( $total ) {
        $this->_qty_total = $total;
    }

    function set_limit( $limit ) {
        $this->_qty_page = $limit;
        $this->_current_page = ( $this->_current_offset / $this->_qty_page ) + 1;
    }

    function set_offset( $offset ) {
        $this->_current_offset = $offset;
        if ( $this->_qty_page < 1 ) return ( $this->_current_page = 1 );

        $this->_current_page = ( $this->_current_offset / $this->_qty_page ) + 1;
    }

    function set_target( $url ) {
        $this->_url_target = $url;
        if ( strpos( $url, '//') === 0 ) 
            $this->_url_target = substr( $url, 2 );
    }

    function get_offset( ) {
        return $this->_current_offset;
    }

    function get_limit( ) {
        return $this->_qty_page;
    }

    function get_page( ) {
        return $this->_qty_page;
    }

    function assert_var( $var_name ) {
        if ( !isset( $this->_request[ $var_name ])) {
            return false;
        }
        return $this->_request[ $var_name ];
    }

    function url_offset( $new_offset = 0 ) {
        $target_request = array_merge( $this->_request, array( 'offset' => $new_offset, 'qty' => $this->_qty_page ));
        if ( !$new_offset || $new_offset < 0 ) {
            unset( $target_request['offset']);
        }
		unset( $target_request['all']);
        foreach( $target_request as $var => $value ) {
            if ( strip_tags( $value ) != $value ) continue;
            $target_request_vars[$var] = urlencode_array( $value, $var ); //$var . '=' . ( is_array( $value ) ? urlencode_array( $value ) : $value ) ;
        }
        return AMP_url_add_vars( $this->_url_target, $target_request_vars );
    }

    function execute( ){
        return $this->render( );
    }

    function render( ) {
        return 
            $this->_renderer->inDiv( 
                          $this->render_position( )
                        . $this->_renderer->newline( )
                        . $this->render_controls( )
                        . $this->_renderer->newline( )
                        . $this->render_links( ),
                    array( 'class' => $this->_css_class_container )
                    );
    }

    function render_top( ) {
        if ( $this->_qty_page == 1 || ( $this->_qty_total - $this->_current_offset ) == 1 ) return false;
        return
            $this->_renderer->inDiv( 
                          $this->render_position( )
                        . $this->_renderer->newline( )
                        . $this->render_controls( ),
                    array( 'class' => $this->_css_class_container )
                    );
            //. $this->_renderer->newline( 1, array( 'clear' => 'all'));
    }

    function render_position( ) {
        if ( !$this->_qty_total ) return false;
        if ( 1 == $this->_qty_page ) {
            //for single items, do not display a range
            $current_page_range = $this->_current_page;
        } else {
            $range_start = 1;
            $range_end = $this->_qty_page + $this->_current_offset ;
            if ( $this->_current_offset ) {
                $range_start = $this->_current_offset;
            }
            if ( $this->_qty_page + $this->_current_offset > $this->_qty_total ) {
                $range_end = $this->_qty_total;
            }
            $current_page_range = $range_start . '-' . $range_end;
        }

        return sprintf( AMP_TEXT_PAGER_POSITION, $current_page_range, $this->_qty_total );
    }

    function render_controls( ) {
        if ($this->_qty_total <= $this->_qty_page ) return false;
        return  $this->render_previous( ) 
                . $this->_renderer->space( 2 )
                . $this->render_next( );
    }

    function render_previous( ) {
        if ( !$this->_current_offset ) return false;
        $target_url = $this->url_offset( $this->_current_offset - $this->_qty_page );
        return $this->_renderer->link( 
                                $target_url, 
                                $this->_renderer->arrow_left( ) . $this->_renderer->space( ) . $this->_text_previous, 
                                array( 'class' => $this->_css_class_control ));
    }

    function render_next ( ) {
        if ( $this->_qty_total <= ( $this->_current_offset + $this->_qty_page )) {
            return false;
        }
        $target_url = $this->url_offset( $this->_current_offset + $this->_qty_page );
        return $this->_renderer->link( 
                                $target_url, 
                                $this->_text_next  . $this->_renderer->space( ) . $this->_renderer->arrow_right( 2 ), 
                                array( 'class' => $this->_css_class_control ));
    }

    function render_first( ) {
        if ( !$this->_current_offset ) return false;
        $url = $this->url_offset( );
        return $this->_renderer->link( 
                            $url,   
                            $this->_renderer->double_arrow_left( ) . $this->_renderer->space( ) . $this->_text_first ,
                            array( 'class' => $this->_css_class_link ));

    }

    function render_last( ) {
        if ( $this->_qty_total <= ( $this->_current_offset + $this->_qty_page )) {
            return false;
        }
        $url = $this->url_offset( $this->_qty_total - $this->_qty_page );
        return $this->_renderer->link( 
                            $url,   
                            $this->_text_last . $this->_renderer->space( ) . $this->_renderer->double_arrow_right( ),
                            array( 'class' => $this->_css_class_link ));

    }

    function render_all( ) {
        if ( $this->_qty_page >= $this->_qty_total ) {
            return false;
        }
        $url = AMP_url_add_vars( $this->url_offset( ), 'all=1');
        return $this->_renderer->link( 
                            $url,
                            $this->_renderer->double_arrow_left( ) . $this->_renderer->space( )
                            . $this->_text_all
                            . $this->_renderer->space( ) . $this->_renderer->double_arrow_right( ),
                            array( 'class' => $this->_css_class_link ));

    }

    function render_links( ) {
        if ( !$this->_enable_all ) return $this->render_jump_set( );
        return 
            $this->render_all( ) . $this->_renderer->newline( )
            . $this->render_jump_set( );
    }

    function render_jump_set( ) {
        if ( $this->_qty_total < $this->_qty_page ) return false;
        $output = '';
        for( $n=0; ( $n * $this->_qty_page ) < $this->_qty_total; $n++ ) {
            $target_offset = $n * $this->_qty_page;
            $output .= $this->render_jump( $target_offset ) . ' ';
        }
        return $output;
    }

    function render_jump( $target_offset ) {
        $target_page = ( $target_offset / $this->_qty_page) + 1; 
        $jump_class = $this->_css_class_jump;
        if ( $target_page == $this->_current_page ) {
            $jump_class = $this->_css_class_jump_highlight;
        }
        $target_url = $this->url_offset( $target_offset );
        return $this->_renderer->link( $target_url, $target_page, array( 'class' => $jump_class ));
    }

    function total( &$source ) {
        if ( is_array( $source )) {
            return count( $source );
        } elseif ( is_object( $source )) {
            return $source->NoLimitRecordCount( );
        } else {
            return false;
        }
    }

    function trim( &$source ) {
        if ( is_array( $source )) {
            $source = array_slice( $source, $this->get_offset( ), $this->get_limit( ) );
        } elseif ( is_object( $source )) {
            $source->setLimit( $this->_qty_page );
            $source->setOffset( $this->_current_offset );
        }
    }

    function view_all( ) {
        return $this->assert_var( 'all');
    }

    function set_text ( $text, $whichlink ){
        $linktext = '_text_'.$whichlink;
        if ( !isset( $this->$linktext)) return false;
        $this->$linktext = $text;
    }

    function enable_all( $value = true ) {
        $this->_enable_all = $value;
    }

}
?>
