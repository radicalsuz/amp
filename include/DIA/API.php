<?php

if(!defined('DIR_SEP')) {
	define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if(!defined('DIA_DIR')) {
	define('DIA_DIR', dirname(__FILE__) . DIR_SEP);
}

if(!defined('DIA_API_DIR')) define('DIA_API_DIR', DIA_DIR.'API'.DIR_SEP);

class DIA_API {

	var $ERROR = "";
	var $WARNING = "";

	function DIA_API() {
		$this->init();
	}

	function init() {
		if( !defined('DIA_API_DEBUG') ) {
			define('DIA_API_DEBUG', false);
		}

		if( !defined('DIA_MESSAGE_DATA_NOT_FOUND') ) {
			define('DIA_MESSAGE_DATA_NOT_FOUND', 'No data found');
		}
	}

	//factory method
    function &create($api = null) {
        if (!isset($api)) $api = DIA_API::getDefaultAPI();
        if(include_once(DIA_API_DIR . $api . '.php')) {
            $classname = 'DIA_API_'.$api;
            return new $classname();
        }

		return $this->error('Could not create new DIA API');
    }

	function getDefaultAPI() {
		return 'HTTP_Request';
	}

	//options are key, column, order, limit, where, desc
	function get( $table, $options ) {
		trigger_error( "get must be overwritten" );
	}

	//options are key, debug
	function process( $table, $data, $options = null ) {
		trigger_error( "process must be overwritten" );
	}

	//thank you magpie
	function error ($errormsg, $lvl=E_USER_WARNING) {
        // append PHP's error message if track_errors enabled
        if ( $php_errormsg ) {
            $errormsg .= " ($php_errormsg)";
        }
        if ( defined('DIA_API_DEBUG') && DIA_API_DEBUG ) {
            trigger_error( $errormsg, $lvl);        
        }
        else {
            error_log( $errormsg, 0);
        }

        $notices = E_USER_NOTICE|E_NOTICE;
        if ( $lvl&$notices ) {
            $this->WARNING = $errormsg;
        } else {
            $this->ERROR = $errormsg;
        }
    }

	//object support methods
	function readObject(&$object, $type=null) {
		if(!is_object($object)) {
			$key = $object;
			$object = DIA_Object::create($type, $key);
		}
		$options = array('key' => $object->getKey());
		$result = $this->get($object->getTable(), $options);
		return $result;
	}

	function saveObject(&$object) {
		$result = $this->process($object->getTable(), array('key' => $object->getKey()));
		return $result;
	}

	//convenience
	function describe( $table ) {
		return $this->get( $table, array('desc'=>true));
	}

    function addSupporter ( $email, $info = array() ) {

        $info[ 'Email' ] = $email;

        $supporter_id = $this->process( "supporter", $info );

        return $supporter_id;

    }

    function linkSupporter ( $list, $supporter ) {

		$this->process('supporter_groups', array('groups_KEY' => $list,
												 'supporter_KEY' => $supporter));
/*
        $data = array();
        
        $data[ 'link' ] = 'groups';
        $data[ 'linkKey' ] = $list;
        $data[ 'key' ] = $supporter;
        $data[ 'updateRowValues' ] = 1;

        return $this->process( "supporter", $data );
*/

    }

	function addGroup( $data ) {

		return $this->process( "groups", $data );

	}

}

function dia_api_get($table, $options) {
	$api =& new DIA_API();
	return $api->get($table, $options);
}

function dia_api_process($table, $data) {
	$api =& new DIA_API();
	return $api->process($table, $data);
}
?>
