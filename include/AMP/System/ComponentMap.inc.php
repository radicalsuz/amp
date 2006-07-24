<?php
require_once( 'AMP/System/Observer.php');
require_once( 'AMP/System/Permission/Config.inc.php');

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
    var $_component_controller_public = 'AMP_System_Component_Controller_Public';
    var $_controller;

    var $_action_displays = array( );
    var $_default_display = 'list';
    var $_action_default  = 'add' ;

    var $_public_page_id_input;
    var $_public_page_id_response;
    var $_public_page_id_list;

    var $_permissions = array( );

    function getComponents() {
        return $this->components;
    }

    function getComponentClass( $component_type ) {
        if ( !isset( $this->components[ $component_type ])) return false;
        if ( isset( $this->paths[ $component_type ])) {
            require_once( $this->paths[ $component_type ]);
        }
        return $this->components[$component_type];
    }

    function &getCachedComponent( $component_type, $id=null, $passthru_value = null ){
        $cache = &AMP_get_cache( );
        if ( !(( $component_class = $this->getComponentClass( $component_type )) && ( $cache ))) return false;

        $cache_key = $component_class;
        if ( isset( $id )) $cache_key = $cache->identify( $component_class, $id );

        if ( isset( $this->paths[ $component_type ])) {
            require_once( $this->paths[ $component_type ]);
        }

        if ( $component = $cache->retrieve( $cache_key )) {
            return $component;
        }

        $component = $this->getComponent( $component_type, $passthru_value );
        if ( !$component ) return false;
        $cache->add( $component, $cache_key );
        return $component;
    }

    function &getComponent( $component_type, $passthru_value = null ){
        if ( !isset( $this->components[ $component_type ])) return false;
        if ( isset( $this->paths[ $component_type ])) {
            require_once( $this->paths[ $component_type ]);
        }
        $component_class = $this->components[ $component_type ];
        if ( !isset( $passthru_value )) $passthru_value = &AMP_Registry::getDbcon( );
        return new $component_class( $passthru_value );
    }

    function getFilePaths() {
        return $this->paths;
    }

    function getDefaultDisplay( ){
        return $this->_action_default;
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
        //if edit is not allowed -- allow nothing
        $allow_any_action = 'edit';
        if ( $action != $allow_any_action && $action != 'search' ){
            if ( !$this->isAllowed( $allow_any_action )) return false;
        }

        $allow_var = '_allow_' . $action;
        if ( !isset( $this->$allow_var )) return true;
        if ( !$this->$allow_var ) return false;
        if ( $this->$allow_var === true ) return true;
        return AMP_Authorized( $this->$allow_var );

    }

    function &get_controller( ){
        if ( isset( $this->_controller )) return $this->_controller;
        require_once( $this->_path_controller );
        $controller = &new $this->_component_controller( );
        $controller->set_map( $this );
        $this->_controller = &$controller;
        return $controller;
    }

    function &get_action_display( $action ){
        if ( !isset( $this->_action_displays[$action] )) {
            return $this->getComponent( $this->_default_display );
        }
        return $this->getComponent( $this->_action_displays[ $action ]);
    }

    function getPublicPageId( $action = 'input' ){
        $page_var = '_public_page_id_' . $action;
        if ( !isset( $this->$page_var )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $this->_map, $page_var )));
            return false;
        }
        return $this->$page_var;
    }

    function &getPublicPage( $action = 'input' ) {
        if ( !( $id = $this->getPublicPageId( $action ))) return false;
        require_once( 'AMP/System/IntroText.inc.php');
        $page = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ), $id );
        if ( !$page->hasData( )) return false;
        return $page;
    }

}

?>
