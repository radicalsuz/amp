<?php

require_once( 'AMP/System/Data/Item.inc.php');

class PermissionGroup extends AMPSystem_Data_Item {

    var $datatable = "per_group";
    var $name_field = "name";

    function PermissionGroup ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( 'settings');
    }
    function _afterSave( ){
        $this->_renewPermissions( );
    }

    function _afterRead( ){
        $this->_loadPermissions( );
    }
    function _loadPermissions( ){
        require_once( 'AMP/System/Permission/Set.inc.php');
        $settingSet = &new PermissionSet( $this->dbcon );
        $settingSet->addCriteriaPermissionGroup( $this->id );
        $settingSet->readData( );
        if ( !$settingSet->makeReady( )) return false;
        $activeItems= array( );
        while( $current = $settingSet->getData( )) {
            $activeItems[] = $current['perid'];
        }
        return $this->setSettings( $activeItems );
    }

    function _renewPermissions() {
        if ( !( $setting_values = $this->getSettings( ))) return false;
        require_once( 'AMP/System/Permission/Set.inc.php');
        $settingSet = &new PermissionSet( $this->dbcon );
        $this->dbcon->StartTrans( );
        $settingSet->deleteData( 'groupid='.$this->id );
        $this->_savePermissions( $setting_values );
        $this->dbcon->CompleteTrans( );
    }

    function _savePermissions( $setting_values ){
        $newGroup = &new Permission( $this->dbcon );
        foreach( $setting_values as $perid ){
            $newGroup->dropID( ) ;
            $newGroup->setData(  array ( 
                    'perid'   => $perid,
                    'groupid' => $this->id ));
            $newGroup->save( );
        }

    }

    function getSettings( ){
        if ( !( $settings = $this->getData( 'settings'))) return false;
        if ( !is_array( $settings )) return split( ',', $settings );
        return $settings ;
    }
    function setSettings( $settings_values ) {
        $this->mergeData( array( 'settings' => $settings_values ));
    }

}

?>
