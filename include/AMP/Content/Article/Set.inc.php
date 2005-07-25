<?php

require_once ('AMP/System/Data/Set.inc.php');

class ArticleSet extends AMPSystem_Data_Set {

    var $datatable = "articles";

    function ArticleSet ( &$dbcon ) {
        $this->init ( $dbcon );
    }

}
?>
