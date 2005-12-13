<?php

class AMPSystemPage_Controller {
	var $_page;
	var $_dbcon;
	var $_udm;

	var $_action_method = null;
	var $_action_id = null;
	var $_action_object = null;

    var $_action_object_class = null;
    var $_list_class;

	var $_protected_methods;

	var $_intro_id;

    var $_identifiers = array( 'id' => 'setActionID' );
    var $_display;

    var $_errors = array( );
    var $_results = array( );

    function AMPSystemPage_Controller( ){
        $this->init();
    }

	function init( ) {
		$this->_page = &AMPContent_Page::instance( );
		$this->_dbcon = &AMP_Registry::getDbcon(); 
		$this->setProtectedMethods();
        $this->readRequest( );
    }

	function execute() {
		return $this->doAction();
	}

	function allowed($action) {
		return method_exists($this, $action) && !$this->_protected_methods[$action];
	}

    function &instance( $controller_type = "AMPSystemPage") {
        static $page_controller = false;
        if ( !$page_controller ) {
            $controller_type .= "_Controller";
            $page_controller = new $controller_type( );
        }
        return $page_controller;
    }

	function doAction() {
		$action = $this->_action_method;
		if( !( isset($action) && $action && $this->allowed($action) )) return false; 
	    return $this->$action();
	}

	function setProtectedMethods() {
        //interface
    }

    function readRequest( ){
        foreach( $this->_identifiers as $var_name => $id_method ){
            if ( isset( $_GET[ $var_name ])) $this->$id_method( $_GET[ $var_name ]);
        }

		$this->_action_method = (isset($_GET['action']) && $_GET['action'] && !is_numeric( $_GET['action']))?
							$_GET['action']: $this->getDefaultAction( );
		if('new' == $this->_action_method) {
			$this->_action_method = 'post';
		}
    }

    function setActionID( $id ){
        $this->_action_id = $id;
    }

    function getDefaultAction( ){
        if ( $this->_action_id ) return 'view';
        return 'showlist';
    }


	function authorized() {
		if(isset($this->_authorized_user) && $this->_authorized_user) {
			return $this->_authorized_user;
		}
        return $this->doAuthorization( );
    }
    function doAuthorization( ){
        //interface
    }
	function &getActionObject() {
		if (isset($this->_action_object)) return $this->_action_object;
        #if (!isset($this->_action_id)) return false;

        $action_class = $this->_action_object_class;
        $this->_action_object = &new $action_class($this->_dbcon, $this->_action_id);
        return $this->_action_object;
    }
	function addError($message, $level = E_USER_WARNING) {
		$this->_errors[] = $message;
	}
    function addResult( $message ){
        $this->_results[] = $message;
    }

	function view() {
		if ( !$item = &$this->getActionObject()) return false;

        $this->_beforeView( );
        $this->_display = &$item->getDisplay( );
		$this->_page->contentManager->addDisplay( $this->_display );
        $this->_afterView( );
        return true;
    }

    function showlist( ){
		if ( !$item = &$this->getActionObject()) return false;

        $this->_beforeList( );
        $this->_display = &$item->getListDisplay( );
		$this->_page->contentManager->addDisplay( $this->_display );
        $this->_afterList( );
    }

    function _beforeList( ){
        //interface
    }

    function _afterList( ){
        //interface
    }
    function _beforeView( ){
        //interface
    }

    function _afterView( ){
        //interface
    }


}

?>
