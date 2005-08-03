<?php

require_once ('AMP/Content/Blog/Set.inc.php' );
require_once ('AMP/System/List.inc.php');

class Blog_List extends AMPSystem_List {

	var $col_headers = array( "ID" => "id", "Title" => "title" );
	var $editlink = "blog.php";
	var $name = "Blog";

	function Blog_List ( &$dbcon ) {
		$source = & new BlogSet( $dbcon );
		$this->init ($source );
	}

}
?>
