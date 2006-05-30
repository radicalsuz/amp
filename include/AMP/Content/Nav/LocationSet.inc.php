<?php

require_once ('AMP/System/Data/Set.inc.php' );


class NavigationLocationSet extends AMPSystem_Data_Set {

    var $datatable = 'nav';
    var $sort = array('position');
    var $_debug_constant = "AMP_DISPLAYMODE_DEBUG_NAVS";

    function NavigationLocationSet( &$dbcon ) {
        $this->init( $dbcon );
    }

    function addCriteriaPositionPrefix( $position_prefix ){
        $this->addCriteria( 'position like ' . $this->dbcon->qstr( $position_prefix .'%' ));
    }

    function addCriteriaLayout( $layout_id ){
        $this->addCriteria( 'layout_id='. $this->dbcon->qstr( $layout_id ));
    }

}
?>
