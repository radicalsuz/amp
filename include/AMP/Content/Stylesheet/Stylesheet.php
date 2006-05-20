<?php

require_once( 'AMP/System/File/Text.php');

class AMP_Content_Stylesheet extends AMP_System_File_Text {

    var $_file_name_pattern = "*css";

    function AMP_Content_Stylesheet( $dbcon = null, $file_path = null ){
        if ( isset( $file_path )) $this->setFile( $file_path );
        $this->_base_path = AMP_LOCAL_PATH . '/custom';
    }


}

?>
