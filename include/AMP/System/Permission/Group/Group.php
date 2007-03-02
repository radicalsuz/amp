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

        $actual_sections = $this->_readSections( );

        $new_section_ids  = array_diff( array_keys( $allowed_sections), array_keys( $actual_sections ));
        $new_sections = array_combine_key( $new_section_ids, AMP_lookup( 'sectionMap'));

        $deleted_section_ids  = array_diff( array_keys( $actual_sections ), array_keys( $allowed_sections ));
        $deleted_sections = array_combine_key( $deleted_section_ids, AMP_lookup( 'sectionMap'));

        if ( empty( $new_sections ) && empty( $deleted_sections ) ) {
            return true;
        }
        //$visible_sections = AMP_lookup( 'sectionMap');
        require_once( 'AMP/System/Permission/Item/Item.php');
        foreach ( $new_sections as $section_id => $section_name ) {
            $item = AMP_System_Permission_Item::create_for_group( $this->id, 'access', 'section', $section_id );
            unset( $item );
        }

        $permissions_lookup = AMP_lookup( 'SectionPermissionItemsByGroup', $this->id );
        foreach( $deleted_sections as $section_id => $section_name ) {
            if ( !$permissions_lookup ) continue;
            if ( !( $permission_item_id = array_search( $section_id, $permissions_lookup ))) continue;
            $item = new AMP_System_Permission_Item( $this->dbcon, $permission_item_id );
            $item->delete( );
            unset( $item );
        }

        /*
        AMP_cacheFlush( AMP_CACHE_TOKEN_ADODB );
        $current_cookie = $this->dbcon->Execute( 'Select * from users_sessions where user = ' . AMP_SYSTEM_USER_ID );
        require_once( 'HTTP/Request.php');
        $request = new HTTP_Request( 'http://local_ufpj.org/system/permission.php?action=update');
        foreach( $_COOKIE as $cookie_name => $cookie_value ) {
            $request->addCookie( $cookie_name, $cookie_value );
        }
        $request->sendRequest( );
        */
        
    }

    function _readSections( ) {
        $allowed_sections = AMP_lookup( 'sectionsByGroup', $this->id );
        if ( !$allowed_sections ) return array( );

        $section_list = AMP_lookup( 'sectionMap' );

        $result = array_combine_key( array_keys( AMP_lookup( 'sectionMap')), $allowed_sections );
        return $result;

    }

    function _loadSections( ) {
        $this->mergeData( array( 'sections' => $this->_readSections( )));

    }

}

?>
