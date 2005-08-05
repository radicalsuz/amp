<?php

class Article_Version extends Article {

    var $datatable = "articles_version";
    var $id_field = "vid";

    function Article_Version ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

}
?>
