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
    var $_cache_allowed = array( );

    var $_url_system = false;
    var $_observers = array( );
    var $_gacl_obj = false;

    function getComponents() {
        return $this->components;
    }

    function getComponentClass( $component_type ) {
        if ( !isset( $this->components[ $component_type ])) return false;
        if ( ( !class_exists( $this->components[ $component_type ] ))
                && isset( $this->paths[ $component_type ])) {
            require_once( $this->paths[ $component_type ]);
        }
        return $this->components[$component_type];
    }

    function &getCachedComponent( $component_type, $id=null, $passthru_value = null ){
        if ( AMP_DEBUG_MODE_COMPONENT_CACHE_INACTIVE 
             || ( isset( $this->_cache_allowed[ $component_type ]) && !$this->_cache_allowed[$component_type])) {
            return $this->getComponent( $component_type, $passthru_value );
        }
        $empty_value = false;
        $cache = &AMP_get_cache( );
        $cache_key = $this->getCacheKey( $component_type, $id );
        if ( !$cache_key ) return $empty_value;

        $component_class = $this->getComponentClass( $component_type );
        if ( !$component_class ) return $empty_value;
        if ( ( !class_exists( $component_class )) &&  isset( $this->paths[ $component_type ])) {
            require_once( $this->paths[ $component_type ]);
        }

        if ( $component = AMP_cache_get( $cache_key )) {
            return $component;
        }

        $component = $this->getComponent( $component_type, $passthru_value );
        if ( !$component ) return $empty_value;
        $result = AMP_cache_set( $cache_key, $component );
        //$cache->add( $component, $cache_key );
        return $component;
    }

    function getCacheKey( $component, $id = null ) {
        static $cache = false;
        if ( !$cache ) $cache = AMP_get_cache( );
        $component_class = is_object( $component ) ? get_class( $component ) : $this->getComponentClass( $component ); 
        $cache_key_base = false;

        foreach( $this->components as $key => $class_name ) {
            if ( strtolower( $class_name ) != strtolower( $component_class )) continue;
            $cache_key_base = sprintf( AMP_CACHE_TOKEN_COMPONENT, $class_name );
            break;
        }

        if ( !$cache_key_base ) return false;
        if ( isset( $id )) {
            return $cache->identify( $cache_key_base, $id );
        }
        return $cache_key_base;


    }

    function clearCached( $component ) {
        if ( AMP_DEBUG_MODE_COMPONENT_CACHE_INACTIVE ) return true;
        $id = isset( $component->id ) ? $component->id : null;
        $cache_key = $this->getCacheKey( $component, $id );
        return AMP_cache_delete( $cache_key ) ;
    }

    function cacheComponent( &$component ) {
        if ( AMP_DEBUG_MODE_COMPONENT_CACHE_INACTIVE ) return true;
        $id = isset( $component->id ) ? $component->id : null;
        $cache_key = $this->getCacheKey( $component, $id );
        if ( !$cache_key ) return false;

        return AMP_cache_set( $cache_key, $component );


    }

    function &getComponent( $component_type, $passthru_value = null ){
        $empty_value = false;

        if ( !isset( $this->components[ $component_type ])) return $empty_value;
        $component_class = $this->components[ $component_type ];

        if ( ( !class_exists( $component_class )) &&  isset( $this->paths[ $component_type ])) {
            require_once( $this->paths[ $component_type ]);
        }
        if ( !isset( $passthru_value )) $passthru_value = &AMP_Registry::getDbcon( );
        $result = &new $component_class( $passthru_value );
        return $result;
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
			if (strtolower( $component_name ) == strtolower( $component_class )) return $key;
		}
		return false;
	}

    function readRequest( &$controller ){
        require_once ( $this->_path_request_handler );
        $handler = &new $this->_component_request_handler( $controller );
        return $handler->execute( );
    }

    function isAllowed( $action, $id = false ){

        if ( $this->_gacl_obj && $id ) {
            if ( !AMP_allow( $action, $this->_gacl_obj, $id )) return false;
        }

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
        if ( !empty( $this->_observers )) {
            foreach( $this->_observers as $observer_class ) {
                $observer = new $observer_class( );
                $controller->add_observer( $observer );
                unset( $observer );
            }
        }
        return $controller;
    }

    function get_action_display_type( $action ) {
        if ( !isset( $this->_action_displays[$action] )) {
            return $this->_default_display ;
        }
        return $this->_action_displays[ $action ];
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
        $empty_value = false;
        if ( !( $id = $this->getPublicPageId( $action ))) return $empty_value;
        require_once( 'AMP/System/IntroText.inc.php');
        $page = &new AMPSystem_IntroText( AMP_Registry::getDbcon( ), $id );
        if ( !$page->hasData( )) return false;
        return $page;
    }

    function get_url_system( ) {
        return $this->_url_system;
    }

}

?>
