<?php

/* * * * * * * * * * * * *
 * 
 *  AMPSystem_NavManager
 *
 *  class for system-side navigation display 
 *
 *  AMP 3.5.0
 *
 *  2005-07-08
 *  Author: austin@radicaldesigns.org
 *
 * * * * * * * **/

 require_once ('AMP/System/XMLEngine.inc.php');
 require_once ('AMP/System/Permission/Manager.inc.php');
 require_once ('AMP/System/Nav.inc.php');
 require_once ('AMP/System/Cache/Config.inc.php');

class AMPSystem_NavManager {

    var $nav_set;
    var $per_manager;
    var $_tool_id;
    var $_buffer;
    var $_default_nav = 'content';
    var $_filename_navs = 'AMP/System/Nav.xml';

    function AMPSystem_NavManager() {
        $this->per_manager =  & AMPSystem_PermissionManager::instance();
        $this->loadNavs();
    }

    function loadNavs() {
        //check for cached version
        $cache_key = AMP_CACHE_TOKEN_XML_DATA . $this->_filename_navs;
        $nav_set = &AMP_cache_get( $cache_key );
        if ( !$nav_set ){
            if (!($xmlEngine = &new AMPSystem_XMLEngine('Nav'))) return false;;
            $nav_set = $xmlEngine->readData();
            $this->convertPermissions( $nav_set );
            AMP_cache_set( $cache_key, $nav_set );
        }

        foreach ($nav_set as $name => $item ) {
            $new_nav = &$this->addNav( $name, $item );
        }
    }

    function &addNav ( $nav_name, $desc=array() ) {
		$nav_name = strtolower($nav_name);
        $desc['id'] = $nav_name;
        $this->nav_set[$nav_name] = &new AMPSystem_Nav( $desc, $this );
        return $this->nav_set[$nav_name];
    }

    function setToolId( $modid ) {
        $this->_tool_id = $modid;
    }


    function render( $nav_name ) {
        if (!($nav = &$this->getNav($nav_name))) return false;
        if (isset($this->_tool_id)) $nav->addToolOptions( $this->_tool_id );
        return $nav->output();
    }


    function getNavNames() {
        return $this->_permittedNavs();
    }


    function convertPermissions( &$set ) {
        if (empty($set)) return false;
        foreach ($set as $key => $nav) {
            if (!is_array($nav)) continue;
            if (isset($nav['per'])) $set[$key]['per'] = $this->per_manager->convertDescriptor( $nav['per'] );
            $this->convertPermissions( $set[$key] );
        }
    }

    function isNav( $nav_name ) {
		$nav_name = strtolower($nav_name);
        return isset($this->nav_set[$nav_name]);
    }

    function getNav( $nav_name ) {
		$nav_name = strtolower($nav_name);
        if (!$this->isNav( $nav_name )) return false;
        return $this->nav_set[$nav_name];
    }

    function _permittedNavs() {
        $allowed_set = array();
        foreach ($this->nav_set as $name => $item ) {
            if (!$this->nav_set[$name]->checkPermission()) continue;
            $allowed_set[] = $name;
        }
        return $allowed_set;
    }


    function buildFormNav( $form_id ) {
        $form_names = AMPSystem_Lookup::instance('Forms');
        if (!isset($form_names[$form_id])) return false;
        $nav_name = str_replace( " ", "_", $form_names[$form_id] );

        $nav = &$this->addNav( $nav_name );
        $nav->addTitle( $form_names[$form_id] );
        $nav->addItem ( 'userdata_list.php?modin='.$form_id    , 'View Data',    'view' );
        $nav->addItem ( 'modinput4_view.php?modin='.$form_id   , 'Add Data',     'add' );
        $nav->addItem ( 'modinput4_search.php?modin='.$form_id , 'Search Data',  'search' );
        $nav->addItem ( 'modinput4_edit.php?modin='.$form_id   , 'Form Settings','form', AMP_PERMISSION_FORM_ADMIN );
        $nav->addItem ( 'modinput4_copy.php?modin='.$form_id   , 'Copy Form',    'add',  AMP_PERMISSION_FORM_ADMIN );

        $tool_lookup = AMPSystem_Lookup::instance('ToolsbyForm');
        if (!isset($tool_lookup[$form_id])) return $nav_name;

        $this->setToolId( $tool_lookup[ $form_id ] );
        #$nav->addToolOptions( $tool_lookup[$form_id] );

        return $nav_name;
    }

    function &get_buffer( ){
        if ( isset( $this->_buffer )) return $this->_buffer;
        require_once( 'AMP/Content/Buffer.php');
        $this->_buffer = &new AMP_Content_Buffer( );
        return $this->_buffer;
    }

    function request( $nav_name, $form_id = false ){
        if ( $form_id ) $nav_name = $this->buildFormNav( $form_id );
        if ( !$output = $this->render( $nav_name)) return false;
        $buffer = &$this->get_buffer( );
        $buffer->add( $output );
        return true;

    }

    function execute( ){
        $buffer = &$this->get_buffer( );
        $output = $buffer->execute( );
        if ( $output ) return $output;
        
        $this->request( $this->_default_nav );
        return $this->execute( );
        
    }

}
?>
