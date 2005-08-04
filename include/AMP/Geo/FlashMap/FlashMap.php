<?php

/**************
 *  FlashMap
 *
 *  AMP 3.5.1
 *  2005-03-08
 *
 *  Author: david@radicaldesigns.org
 *
 *****/

require_once ( 'AMP/System/Data/Item.inc.php' );


 class AMPSystem_FlashMap extends AMPSystem_Data_Item {


    var $datatable = "maps";
    var $name_field = 'name';

    function AMPSystem_FlashMap ( &$dbcon, $id=null ) {
        $this->init( $dbcon, $id );
    }
    


 }

?>
