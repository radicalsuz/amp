<?php

class AMP_System_Permission_Item extends AMPSystem_Data_Item {
    var $datatable = 'permission_items';
    var $_class_name = 'AMP_System_Permission_Item';
    var $_exact_value_fields = array( 'action', 'target_type', 'target_id' );

    function AMP_System_Permission_Item( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function create( $action, $item_type, $item_id = null ) {
        $item = &new AMP_System_Permission_Item( AMP_Registry::getDbcon( )) ;
        $item->setDefaults( );
        $item->mergeData( array( 'action' => $action, 'target_type' => $item_type, 'target_id' => $item_id, 'user_id' => AMP_SYSTEM_USER_ID, 'allow' => true  ));
        $result = $item->save( );
        if ( !$result ) return false;
        return $item->id;
    }

    function &create_for_group( $group_id, $action, $item_type, $item_id ) {
        $search_base = new AMP_System_Permission_Item( $this->dbcon );
        $items = $search_base->find( array( 'group' => $group_id, 'action' => $action, 'target_type' => $item_type, 'target_id' => $item_id, 'allow' => 1 ));
        if ( $items && !empty( $items ) ) {
            $found = current( $items );
            return $found;
        }
        $item = new AMP_System_Permission_Item( AMP_Registry::getDbcon( ));
        $item->setDefaults( );
        $item->mergeData( array( 'action' => $action, 'target_type' => $item_type, 'target_id' => $item_id, 'group_id' => $group_id, 'allow' => true  ));
        $result = $item->save( );
        if ( !$result ) return false;
        return $item->id;

    }

    function makeCriteriaAllowed( $status ) {
        return 'allow=' . $status ;
    }

    function makeCriteriaGroup( $group_id ) {
        return 'group_id = ' . $group_id;
    }

    function makeCriteriaUser( $user_id ) {
        return 'user_id = ' . $user_id;
    }

}

?>
