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

    function get_url_edit( ) {
        $badge = $this->getBadgeId( );
        if ( $badge ) {
            return AMP_url_update( AMP_SYSTEM_URL_BADGE, array( 'id' => $badge));
        }

        $nav = $this->getNavId( );
        if ( $nav ) {
            return AMP_url_update( AMP_SYSTEM_URL_NAV, array( 'id' => $nav ));
        }
        return false;
    }

    function getName( ) {
        $badge = $this->getBadgeId( );
        if ( $badge ) {
            $badge_names = AMP_lookup( 'badges');
            return ucwords( AMP_TEXT_BADGE ) . ': ' . $badge_names[ $badge ];
        }

        $nav = $this->getNavId( );
        if ( $nav ) {
            $nav_names = AMP_lookup( 'navs');
            return ucwords( AMP_TEXT_NAV ). ': ' . $nav_names[ $nav ];
        }
        return false;
    }

    function getBlock( ) {
        $blocks = AMP_lookup( 'navBlocks');
        $position = $this->getPosition( );
        foreach( $blocks as $block_header => $token ) {
            if ( strpos( strtolower( $position), strtolower( $token )) === 0 ) {
                return $block_header;
            }
        }

    }

}

?>
