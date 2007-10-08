<?php

class AMP_Content_Image extends AMPSystem_Data_Item {
    var $datatable = 'images';
    var $_image;
    var $_exact_value_fields = array( 'name' );
    var $_class_name = 'AMP_Content_Image';

    function AMP_Content_Image( &$dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

}


?>
