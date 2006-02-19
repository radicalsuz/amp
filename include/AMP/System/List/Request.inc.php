<?php
define( 'AMP_ERROR_METHOD_NOT_SUPPORTED', '%s does not support method %s attempted by %s');

class AMP_System_List_Request {
    var $_actions = array();
    var $_action_args = array( );

    /* sample code
    var $_action_args = array( 
        'move' => array( 'move_destination' ),
        'regionize' => array( 'region_destination' )
    );
    */

    var $_source;
    var $_request_vars;

    var $_committed_action;
    var $_committed_qty = 0;

    var $_submitGroup = 'submitAction';

    function AMP_System_List_Request( &$source ){
        $this->init( $source );
    }

    function init( &$source ){
        $this->_source = &$source;
        $this->_request_vars = array_merge( $_POST, AMP_URL_Read( ));
    }


    function execute( ){
        if ( !$this->isActive( )) return false;

        $this->_committed_action = $this->_getAction( );

        // simple code for AMPSystem_Data_Sets 
        if ( !is_array( $this->_source )) {
            return $this->commitAction( $this->_source, $this->_getAction( ), $this->_getArguments());
        }
        
        // for Object Array lists
        $affected_items = &$this->_getAffectedItems( $this->_getAffectedIds( ));

        foreach( $affected_items as $request_target ) {
            if ( !$this->commitAction(  $request_target , $this->_getAction( ), $this->_getArguments())) continue; 
            ++$this->_committed_qty ;
        }

        return true;
    
    }

    function commitAction( &$target, $action, $args = null ){
        if ( !method_exists( $target, $action )) {
            trigger_error( sprintf( AMP_ERROR_METHOD_NOT_SUPPORTED, get_class( $target ), $action , get_class( $this )));
            return false;
        }
        return call_user_func_array( array( $target, $action ), $args ) ;
    }

    function &_getAffectedItems( $affected_ids ){
        $return = array( );
        foreach( $this->_source as $sourceItem ) {
            if ( array_search( $sourceItem->id, $affected_ids ) !== FALSE ) {
                $return[$sourceItem->id] = $sourceItem; 
            }
        }
        return $return;

    }

    function allow( $action ){
        return ( array_search( $action, $this->_actions) !== FALSE );
    }

    function isActive( ){
        if ( !( $action = $this->_getAction( ) && $this->_getAffectedIds( ))) return false;
        if ( !$this->allow( $action )) return false;
        return true;
    }

    function _getAction( ){
        if ( !isset( $this->_request_vars[$this->_submitGroup])) return false;
        return key( $this->_request_vars[$this->_submitGroup] );
    }

    function _getAffectedIds( ){
        if ( !isset( $this->_request_vars['id'])) return false;
        return $this->_request_vars['id'] ;

    }

    function _getArguments( $action = null ) {
        if ( !isset( $action )) $action = $this->_getAction( );
        if ( !isset( $this->_action_args[$action] )) return null;
        return array_combine_key( $this->_action_args[ $action ], $this->_request_vars );
    }

    function addAction( $action_name, $action_args = null ){
        $this->_actions[] = $action_name;
        if ( isset( $action_args )) $this->_action_args[$action] = $action_args;
    }

    function getPerformedAction( ){
        return $this->_committed_action;
    }

    function getAffectedQty( ){
        return $this->_committed_qty;
    }

    function setSubmitGroup( $name ){
        $this->_submitGroup = $name;
    }


}

?>
