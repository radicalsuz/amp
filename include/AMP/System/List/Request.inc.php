<?php
require_once( 'AMP/System/ComponentLookup.inc.php');

class AMP_System_List_Request {
    var $_actions = array();
    var $_action_args = array( );
    var $_actions_global = array( );

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
        $this->__construct( $source );
    }

    function init( &$source ){
        $this->__construct( $source );
    }

    function __construct( &$source ) {
        $this->_source = &$source;
        if ( $url_vars = AMP_URL_Read( )){
            $this->_request_vars = array_merge( $_POST, AMP_URL_Read( ));
        } else {
            $this->_request_vars = $_POST;
        }
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
        if ( $this->commitActionLocal( $affected_items, $this->_getAction( ), $this->_getArguments( ))) {
            return true;
        }

        foreach( $affected_items as $request_target ) {
            if ( !$this->commitAction(  $request_target , $this->_getAction( ), $this->_getArguments())) continue; 
            ++$this->_committed_qty ;
        }

        AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );

        return true;
    
    }

    function commitAction( &$target, $action, $args = null ){
        if ( !method_exists( $target, $action )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED, get_class( $target ), $action , get_class( $this )));
            return false;
        }
        if ( !$this->allow( $action, $target ))  {
            $flash = &AMP_System_Flash::instance( );
            $flash->add_message( sprintf( AMP_TEXT_ERROR_ACTION_NOT_ALLOWED, $action ) ." " . $target->getShortName( ) );
            return false;
        }

        $local_args = $this->_getSpecificArgs( $target, $action, $args );
        return call_user_func_array( array( $target, $action ), $local_args ) ;
    }

    function commitActionLocal( &$target_set, $action, $args = null ){
        if ( method_exists( $this, $action )) {
            $this->$action( $target_set, $args );
            return true;
        }
        return false;
    }

    function _getSpecificArgs( &$target, $action, $args ){
        if ( !isset( $this->_action_args[$action])) return null; 
        $result_args = array( );
        foreach( $args as $arg_key => $arg_value ){
            if ( !is_array( $arg_value ) || !isset( $arg_value[$target->id])){
                $result_args[$arg_key] = $arg_value;
                continue;
            }
            $result_args[$arg_key] = $arg_value[ $target->id ];
        }
        return $result_args;
    }

    function &_getAffectedItems( $affected_ids ){
        #return array_combine_key( $affected_ids, $this->_source );
        $return = array( );
        if ( !is_array( $affected_ids )) $affected_ids = array( $affected_ids );
        foreach( $this->_source as $sourceItem ) {
            if ( array_search( $sourceItem->id, $affected_ids ) !== FALSE ) {
                $return[$sourceItem->id] = $sourceItem; 
            }
        }
        return $return;

    }

    function allow( $action, $target = false ){
        if ( array_search( $action, $this->_actions) === FALSE ) {
            return false;
        }
        if ( !$target ) return true;
        if ( !isset( $target->id )) return true;

        $map = ComponentLookup::instance( get_class( $target ));
        return $map->isAllowed( $action, $target->id );
        
        
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
        if ( array_search( $this->_getAction( ), $this->_actions_global ) !== FALSE ){
            foreach( $this->_source as $source_object ){
                $return[] = $source_object->id;
            }
            return $return;
        }

        if ( !isset( $this->_request_vars['list_action_id'])) return false;
        return $this->_request_vars['list_action_id'] ;

    }

    function _getArguments( $action = null ) {
        if ( !isset( $action )) $action = $this->_getAction( );
        if ( !isset( $this->_action_args[$action] )) return null;
        return array_combine_key( $this->_action_args[ $action ], $this->_request_vars );
    }

    function addAction( $action_name, $action_args = null ){
        $this->_actions[] = $action_name;
        if ( isset( $action_args )) $this->_action_args[$action_name] = $action_args;
    }
    function setActionGlobal( $action_name ){
        $this->_actions_global[] = $action_name;
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


    function export( &$target_set, $args = null ) {
		$sample = current($target_set);
		$keys = $sample->export_keys();
		$dump = array();
		foreach($keys as $key ) {
			$blank_set[ $key ] = null;
		}

		foreach($target_set as $source) {
			$values = $source->getData();	
			$safe_values = array_combine_key($keys, $values);
			$dump[ $source->id ] = array_merge($blank_set, $safe_values );

            if ( $exp_values = $source->before_export( $safe_values )) {
                $dump[ $source->id ] = array_merge($blank_set, $safe_values, $exp_values );
            }
		}
		require_once('AMP/Renderer/CSV.php');
		$renderer = new AMP_Renderer_CSV();
		$file =  $renderer->format(array( $keys ));
	 	$file .= $renderer->format($dump);	
		$renderer->header( date("Y_m_d__") . AMP_pluralize( get_class($sample)));
		print $file;
		exit;
    }
}

?>
