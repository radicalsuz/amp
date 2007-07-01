<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Nav_Location extends AMPSystem_Data_Item {

    var $datatable = 'nav';
    var $_class_name = 'AMP_Content_Nav_Location';

    function AMP_Content_Nav_Location( &$dbcon, $id=null ){
        $this->init( $dbcon, $id );
    }

    function makeCriteriaLayout_id( $layout_id ){
        return 'layout_id=' . $this->dbcon->qstr( $layout_id );
    }

    function _sort_default( &$item_set ){
        $this->sort( $item_set, 'position');
    }

    function getPosition( ){
        return $this->getData( 'position' );
    }

    function getNavid( ){
        return $this->getData( 'navid');
    }

    function getNavName( ){
        if ( !( $navid = $this->getNavId( ))) return false;
        $nav_names = &AMPContent_Lookup::instance( 'navs' );
        if ( isset( $nav_names[ $navid ])) return $nav_names[$navid];
        return false;
    }

    function getLayoutId( ){
        return $this->getData( 'layout_id' );
    }

    function getBadge_id( ) {
        return $this->getBadgeId( );
    }

    function getBadgeId( ) {
        return $this->getData( 'badge_id' );
    }

}

?>
