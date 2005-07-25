<?php

require_once ( 'AMP/System/Data/Item.inc.php' );

class Article extends AMPSystem_Data_Item {

    var $datatable = "articles";

    function Article( &$dbcon, $id = null ) {
        $this->init ($dbcon, $id);
    }

}
?>
