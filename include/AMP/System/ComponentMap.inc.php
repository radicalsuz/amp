<?php

class AMPSystem_ComponentMap {
    
    var $paths;
    var $components;
    var $heading;
    var $nav_name;

    var $_allow_inline_update = false;
    var $_path_request_handler = 'AMP/System/ComponentRequest.php';
    var $_component_request_handler = 'AMPSystem_ComponentRequest';

    function getComponents() {
        return $this->components;
    }

    function getFilePaths() {
        return $this->paths;
    }

    function getNavName() {
        return $this->nav_name;
    }

    function getHeading() {
        return $this->heading;
    }
	
	function getPath( $component_name ) {
		if (!isset( $this->paths[ $component_name ] ) ) return false;
		return $this->paths[ $component_name ];
	}
	
	function findComponent( $component_class ) {
		foreach( $this->components as $key => $component_name ) {
			if (strtolower( $component_name ) == $component_class) return $key;
		}
		return false;
	}

    function readRequest( &$controller ){
        require_once ( $this->_path_request_handler );
        $handler = &new $this->_component_request_handler( $controller );
        return $handler->execute( );
    }

    function isAllowed( $action ){
        $allow_var = '_allow_'.$action;
        if ( !isset( $this->$allow_var )) return true;
        if ( !$this->$allow_var ) return false;
        if ( $this->$allow_var === true ) return true;
        return AMP_Authorized( $this->$allow_var );

    }

}

?>
