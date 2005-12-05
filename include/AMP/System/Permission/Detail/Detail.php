<?php

require_once( 'AMP/System/Data/Item.inc.php');

class PermissionDetail extends AMPSystem_Data_Item {

    var $datatable = "per_description";
    var $name_field = "name";

    function PermissionDetail ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( 'groups');
    }

    function _afterSave( ){
        $this->_renewPermissions( );
    }

    function _afterRead( ){
        $this->_loadPermissions( );
    }

    function _loadPermissions( ){
        require_once( 'AMP/System/Permission/Set.inc.php');
        $groupSet = &new PermissionSet( $this->dbcon );
        $groupSet->addCriteriaPermission( $this->id );
        $groupSet->readData( );
        if ( !$groupSet->makeReady( )) return false;
        $activeGroups= array( );
        while( $current = $groupSet->getData( )) {
            $activeGroups[] = $current['groupid'];
        }
        return $this->setGroups( $activeGroups );
    }

    function _renewPermissions() {
        if ( !( $group_values = $this->getGroups( ))) return false;
        
        require_once( 'AMP/System/Permission/Set.inc.php');
        $groupSet = &new PermissionSet( $this->dbcon );
        $this->dbcon->StartTrans( );
        $groupSet->deleteData( 'perid='.$this->id );
        $this->_savePermissions( $group_values );
        $this->dbcon->CompleteTrans( );
    }

    function _savePermissions( $group_values ){
        $newGroup = &new Permission( $this->dbcon );
        foreach( $group_values as $groupid ){
            $newGroup->dropID( ) ;
            $newGroup->setData(  array ( 
                    'perid'   => $this->id,
                    'groupid' => $groupid ));
            $newGroup->save( );
        }

    }

    function getGroups( ){
        if ( !( $groups = $this->getData( 'groups'))) return false;
        if ( !is_array( $groups )) return split( ',', $groups );
        return $groups;
    }

    function setGroups( $group_values ) {
        $this->mergeData( array( 'groups' => $group_values ));
    }
}

?>
