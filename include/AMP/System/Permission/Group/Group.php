<?php

require_once( 'AMP/System/Data/Item.inc.php');

class PermissionGroup extends AMPSystem_Data_Item {

    var $datatable = "per_group";
    var $name_field = "name";
    var $_class_name = 'PermissionGroup';

    function PermissionGroup ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( 'settings');
        $this->_addAllowedKey( 'sections');
    }
    function _afterSave( ){
        $this->_renewPermissions( );
        $this->_updateSections( );
    }

    function _afterRead( ){
        $this->_loadPermissions( );
        $this->_loadSections( );
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

    function _updateSections( ) {
        $allowed_sections = $this->getData( 'sections');
        if ( !$allowed_sections ) $allowed_sections = array( );

        $new_sections  = array_diff( $allowed_sections, $this->_readSections( ));
        $deleted_sections  = array_diff( $this->_readSections( ), $allowed_sections );

        if ( empty( $new_sections ) && empty( $deleted_sections ) ) {
            return true;
        }
        $visible_sections = AMP_lookup( 'sectionMap');
        require_once( 'AMP/System/Permission/Item/Item.php');
        foreach ( $new_sections as $section_id => $section_name ) {
            $item = AMP_System_Permission_Item::create_for_group( $this->id, 'access', 'section', $section_id );
            unset( $item );
        }

        $permissions_lookup = AMP_lookup( 'SectionPermissionItemsByGroup', $this->id );
        foreach( $deleted_sections as $section_id => $section_name ) {
            if ( !isset( $permissions_lookup[$section_id ])) continue;
            $item = new AMP_System_Permission_Item( $this->dbcon, $permissions_lookup[ $section_id ]);
            $item->delete( );
            unset( $item );
        }

        AMP_permission_update( );
        
    }

    function _readSections( ) {
        $allowed_sections = AMP_lookup( 'sectionsByGroup', $this->id );
        if ( !$allowed_sections ) return array( );

        $section_list = AMP_lookup( 'sectionMap' );

        $result = array_combine_key( array_keys( AMP_lookup( 'sectionMap')), $allowed_sections );
        trigger_error( 'got settings ' . count( $result ));
        return $result;

    }

    function _loadSections( ) {
        $this->mergeData( array( 'sections' => $this->_readSections( )));

    }

}

?>
