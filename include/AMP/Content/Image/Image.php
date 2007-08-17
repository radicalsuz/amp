<?php

class AMP_Content_Image extends AMPSystem_Data_Item {
    var $datatable = 'images';
    var $_image;

    function AMP_Content_Image( &$dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

}


?>
