<?php

if(!defined('DIR_SEP')) {
	define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if(!defined('DIA_DIR')) {
	define('DIA_DIR', dirname(__FILE__) . DIR_SEP);
}

if(!defined('DIA_API_DIR')) define('DIA_API_DIR', DIA_DIR.'API'.DIR_SEP);

require_once('DIA/dia_config.php');

class DIA_API {

	function DIA_API() {
		$this->init();
	}

	function init() {
	}

	//factory method
    function &create($api = null) {
        if (!isset($api)) $api = DIA_API::getDefaultAPI();
        if(include_once(DIA_API_DIR . $api . '.php')) {
            $classname = 'DIA_API_'.$api;
            return new $classname();
        }

        return false;
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

        // nasty-ass hack. See DIAlist/save.inc.php.
        $GLOBALS['diaSupporter'] = trim( $supporter_id );
        return $supporter_id;

    }

    function linkSupporter ( $list, $supporter ) {

        $data = array();
        
        $data[ 'link' ] = 'groups';
        $data[ 'linkKey' ] = $list;
        $data[ 'key' ] = $supporter;
        $data[ 'updateRowValues' ] = 1;

        return $this->process( "supporter", $data );

    }

	function addGroup( $data ) {

		return $this->process( "groups", $data );

	}

}
?>
