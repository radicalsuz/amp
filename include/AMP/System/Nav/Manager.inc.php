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

class AMPSystem_NavManager {

    var $nav_set;
    var $per_manager;

    function AMPSystem_NavManager() {
        $this->per_manager =  & AMPSystem_PermissionManager::instance();
        $this->loadNavs();
    }

    function loadNavs() {
        if (!($xmlEngine = &new AMPSystem_XMLEngine('Nav'))) return false;;
        $nav_set = $xmlEngine->readData();
        $this->convertPermissions( $nav_set );

        foreach ($nav_set as $name => $item ) {
            $new_nav = &$this->addNav( $name, $item );
        }
    }

    function &addNav ( $nav_name, $desc=array() ) {
        $desc['id'] = $nav_name;
        $this->nav_set[$nav_name] = &new AMPSystem_Nav( $desc, $this );
        return $this->nav_set[$nav_name];
    }


    function render( $nav_name ) {
        if (!($nav = &$this->getNav($nav_name))) return false;
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
        return isset($this->nav_set[$nav_name]);
    }

    function getNav( $nav_name ) {
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

        $nav->addToolOptions( $tool_lookup[$form_id] );

        return $nav_name;
    }


}
?>