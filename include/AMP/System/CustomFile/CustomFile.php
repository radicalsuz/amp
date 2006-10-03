<?php

require_once( 'AMP/System/File/Text.php');

class AMP_System_CustomFile extends AMP_System_File_Text {

    function AMP_System_CustomFile ( $dbcon = null, $file_path = null ){
        $this->__construct( $file_path );
        $this->_base_path = AMP_LOCAL_PATH . '/custom';
    }

    function get_url_edit( ) {
        return AMP_url_add_vars( AMP_SYSTEM_URL_CUSTOM_FILE, array( 'id=' . $this->id ));
    }


}
?>
