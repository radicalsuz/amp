<?php

require_once( 'AMP/System/Data/Item.inc.php');

class ArticleComment extends AMPSystem_Data_Item {

    var $datatable = "comments";
    var $name_field = "title";

    function ArticleComment ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }
}

?>
