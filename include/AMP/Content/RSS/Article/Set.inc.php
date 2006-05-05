<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/RSS_Article/RSS_Article.php');

class RSS_ArticleSet extends AMPSystem_Data_Set {
    var $datatable = 'px_items';
    var $sort = array( "title");

    function RSS_ArticleSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
