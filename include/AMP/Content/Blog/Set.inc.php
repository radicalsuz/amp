<?php

require_once ('AMP/System/Data/Set.inc.php');

class BlogSet extends AMPSystem_Data_Set {

    var $datatable = "articles";

    function BlogSet ( &$dbcon ) {
        $this->init ( $dbcon );
		$this->setCriteria('class= '.AMP_CONTENT_CLASS_BLOG);
    }

}
?>
