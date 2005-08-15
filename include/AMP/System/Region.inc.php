<?php

require_once ( 'AMP/System/Data/Item.inc.php' );

class AMPSystem_Region extends AMPSystem_Data_Item {

    var $datatable = "region";
    var $name_field = "title";

    var $view_objects = array(
        'files' => array(
            'list' => 'AMP/System/Region/List.inc.php',
            'form' => 'AMP/System/Region/Form.inc.php' ),
        'classes' => array(
            'list' => 'AMPSystem_Region_List',
            'form' => 'AMPSystem_Region_Form')
        );

    function AMPSystem_Region( &$dbcon, $id = null ) {
        $this->init ($dbcon, $id );
    }

    function &getDisplay () {
        require_once( 'AMP/Content/Display/Region.inc.php' );
        return new AMPContentDisplay_Region( $this );
    }
}
?>
