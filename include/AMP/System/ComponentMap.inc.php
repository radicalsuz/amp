<?php
require_once( 'AMP/System/Observer.php');

class AMPSystem_ComponentMap extends AMP_System_Observer {
    
    var $paths;
    var $components;
    var $heading = 'Item';
    var $nav_name;

    var $_allow_inline_update = false;
    var $_allow_search = false; 

    var $_path_request_handler = 'AMP/System/ComponentRequest.php';
    var $_component_request_handler = 'AMPSystem_ComponentRequest';

    var $_path_controller = 'AMP/System/Component/Controller.php';
    var $_component_controller = 'AMP_System_Component_Controller_Standard';

    function getComponents() {
        return $this->components;
    }

    function &getComponent( $component_type ){
        if ( !isset( $this->components[ $component_type ])) return false;
        if ( isset( $this->paths[ $component_type ])) {
            require_once( $this->paths[ $component_type ]);
        }
        $component_class = $this->components[ $component_type ];
        return new $component_class( AMP_Registry::getDbcon( ) );
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

    function &get_controller( ){
        require_once( $this->_path_controller );
        $controller = &new $this->_component_controller( );
        $controller->set_map( $this );
        return $controller;
    }

}

?>
