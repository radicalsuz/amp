<?php

require_once( 'AMP/Display/List.php');
require_once( 'AMP/System/List/Toolbar.inc.php');
require_once( 'AMP/System/List/Request.inc.php');
require_once( 'AMP/System/List/Observer.inc.php');

class AMP_Display_System_List extends AMP_Display_List {

    var $columns = array( 'select', 'edit', 'name', 'id' );
    var $column_headers = array( 'id' => 'ID' );
    var $item_count = 0;

    var $_toolbar;
    var $_toolbar_class = 'AMP_System_List_Toolbar';
    var $_request;
    var $_request_class = 'AMP_System_List_Request';

    var $_actions = array( 'publish', 'unpublish', 'delete' );
    var $_action_args = array( );
    var $_actions_global = array( );
    var $_suppress_sort_links = false;
    var $_suppress_toolbar = false;

    var $url_create;
    var $link_target_edit = '_top';
    var $link_vars = array( );

    function AMP_Display_System_List( &$source, $criteria = array( )) {
        $this->__construct( $source, $criteria );
    }

    function _renderItem( &$source ) {
        $this->item_count++;
        foreach( $this->columns as $column_name ) {
            $output[ $column_name ] = $this->render_value( $source, $column_name );
        }
        return $this->render_row( $output, $source );

    }

    function _renderItemContainer( $output, $source ) {
        $row_color_class = ( $this->item_count % 2 ) ? ' list_row_odd' : ' list_row_even';
        $hover_class = 'list_row_hover';

        return $this->_renderer->tr( 
            $output, 
            array( 
                'id'            => $this->list_item_id( $source ), 
                'class'         => 'list_row' . $row_color_class,
                'onMouseover'   => 'this.addClassName( "'.$hover_class.'" );',
                'onMouseout'    => 'this.removeClassName( "'.$hover_class.'" );',
                'onClick'       => '$( "select_'.$this->list_item_id( $source ).'" ).checked = !$( "select_'.$this->list_item_id( $source ) .'").checked;',

                ) 
            );
    }

    function _renderFooter( ) {
        return $this->render_create_link( );
    }

    function render_create_link( ) {
        if ( isset( $this->_source_sample )) {
            $edit_url = $this->_source_sample->get_url_edit( );
            $url = AMP_url_update( $edit_url, array( 'action' => 'new'));
        }
        if ( isset( $this->url_create ) && $this->url_create ) {
            $url = $this->url_create;
        }
        $url = AMP_url_update( $url, $this->link_vars );
        return $this->_renderer->link( $url, AMP_TEXT_ADD_ITEM, array( 'target' => $this->link_target_edit ) );
    }

    function render_row( $column_output, $source ) {
        $row_output = '';
        foreach( $column_output as $output_item ) {
            $row_output .= $this->_renderer->td( $output_item );
        }
        return $row_output;
    }

    function render_value( $source, $column_name ) {
        $local_method = 'render_' . $column_name;
        if ( method_exists( $this, $local_method )) {
            return $this->$local_method( $source );
        }

        $source_method = 'get' . AMP_to_camelcase( $column_name );
        if ( method_exists( $source, $source_method )) {
            return $source->$source_method( );
        }

        return $source->getData( $column_name );
        
    }

    function render_edit( $source ) {
        return
            $this->_renderer->link( 
                AMP_url_update( $source->get_url_edit( ), $this->link_vars ),
                $this->_renderer->image( AMP_SYSTEM_ICON_EDIT, array('alt'=>AMP_TEXT_EDIT, 'class'=>'icon' )),
                array( 'title' => AMP_TEXT_EDIT_ITEM, 'target' => $this->link_target_edit )
            );
    }

    function drop_column( $column_name ) {
        $key = array_search( $column_name, $this->columns );
        if ( $key !== FALSE ) {
            unset( $this->columns[$key]);
        }

    }

    function render_select( $source ) {
        return $this->_renderer->input( 
                        "list_action_id[]", 
                        $source->id, 
                        array( 
                            'type' => 'checkbox', 
                            'onclick' => 'this.checked=!this.checked;', 
                            'class' => 'list_select', 
                            'id' => ( 'select_'. $this->list_item_id( $source ) 
                            )
               ));
    }

    function _renderBlock( $output ) {
        return parent::_renderBlock(  
                    $this->_renderer->form( 
                          $this->render_toolbar( )
                        . $this->_renderer->table( 
                                $this->render_column_headers( ) . $output,
                                array( 'class' => 'system', 'id' => $this->list_id  )
                                )
                        . $this->render_toolbar( ),
                        array( 'name' => $this->list_id, 'action' => AMP_url_update( $_SERVER['REQUEST_URI']), 'method' => 'POST' )
                        )
                )
                ;
    }

    function render_column_headers( ) {
        $output = '';
        foreach( $this->columns as $column_name ) {
            $output .= $this->_renderer->td( $this->render_single_column_header( $column_name ));
        }
        return $this->_renderer->tr( $output, array( 'class' => 'list_column_header'));

    }

    function render_single_column_header( $column_name ) {
        $local_method = 'render_header_' . $column_name;
        if ( method_exists( $this, $local_method )) {
            return $this->$local_method( );
        }
        if ( isset( $this->column_headers[$column_name])) {
            return $this->render_sort_link( $this->column_headers[$column_name], $column_name );
        }
        return $this->render_sort_link( ucwords( str_replace( '_', ' ', $column_name )), $column_name );

    }

    function render_sort_link( $column_header_value, $column_name ) {
        if ( $this->_suppress_sort_links ) return $column_header_value;
        if ( !$this->validate_sort_link( $column_name )) return $column_header_value;

        $url_values = $_GET;
        $url_values['sort'] = $column_name;
        unset( $url_values['sort_direction'] ) ;
        if ( isset( $_REQUEST['sort']) 
             && ( $_REQUEST['sort'] == $column_name ) 
             &&   ! isset( $_REQUEST['sort_direction'] )) {
            $url_values['sort_direction'] = AMP_SORT_DESC;
        }
        return $this->_renderer->link( 
                AMP_url_add_vars( $_SERVER['PHP_SELF'], AMP_url_build_query( $url_values )),
                $column_header_value );

    }

    function render_header_select( ) {
        reset( $this->_source );
        $first_item_id = 'select_' . $this->list_item_id( current( $this->_source ));
        return $this->_renderer->a( AMP_TEXT_ALL, array( 
                'onClick' => 
                'new_value = !$( "'.$first_item_id.'").checked; $$( "#'.$this->list_id.' input.list_select").each( function( slbx  ) { slbx.checked=new_value; } );'));
    }

    function render_header_edit( ) {
        return false;
    }

    function render_name( $source ) {
        return $this->_renderer->link( $source->get_url_edit( ), $source->getName( ));
    }

    function render_toolbar( ) {
        if ( isset( $this->_toolbar ) && !$this->_suppress_toolbar ) {
            return $this->_toolbar->execute( );
        }
    }

    function _attach_actions( &$target ){
        $allowed_actions = $this->get_actions_allowed( );
        foreach( $allowed_actions as $action ){
            $args = ( isset( $this->_action_args[$action] )) ?  $this->_action_args[$action] : null;
            $target->addAction( $action, $args ) ;
        }
        foreach( $this->_actions_global as $action ){
            $target->setActionGlobal( $action ) ;
        }
    }

    function get_actions_allowed( ){
        $map = &ComponentLookup::instance( get_class( $this ));
        if ( !$map ) return $this->_actions;

        return array_filter( $this->_actions, array( $map, 'isAllowed'));
    }

    function getName( ) {
        return $this->list_id;
    }

    function _init_tools( ){
        $tools_submit_group = 'submitAction_' . $this->getName( );

        //initialize toolbar display
        if ( isset( $this->_toolbar_class) && $this->_toolbar_class ) {
            $this->_toolbar = &new $this->_toolbar_class( $this );
            $this->_attach_actions( $this->_toolbar );
            $this->_toolbar->setSubmitGroup( $tools_submit_group );
        }

        if ( isset( $this->_request_class) && $this->_request_class ) {
            $this->_request = &new $this->_request_class( $this->_source );
            $this->_attach_actions( $this->_request );
            $this->_request->setSubmitGroup( $tools_submit_group );
            $this->do_request( );
        }

    }

    function do_request( ) {
        if ( !$this->_request->execute( )) return false;
        
        if ( $affected_qty = $this->_request->getAffectedQty( )) {
            $this->message( sprintf( AMP_TEXT_LIST_ACTION_SUCCESS, 
                                ucfirst( AMP_PastParticiple(  $this->_request->getPerformedAction( ))), 
                                $affected_qty ));
        } else {
            $this->message( sprintf( AMP_TEXT_LIST_ACTION_FAIL, 
                                AMP_PastParticiple( $this->_request->getPerformedAction( )))); 
        }

        if ( !AMP_DISPLAYMODE_DEBUG ) {
            ampredirect( AMP_url_update( $_SERVER['REQUEST_URI']));
        }
        AMP_flush_common_cache( );

        $this->_after_request( );
        
    }

    function _after_request( ) {
        //interface
    }

    function add_link_var( $var, $value ) {
        $this->link_vars[ $var ] = $value;
    }

}


?>
