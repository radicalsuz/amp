<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Blog extends AMPSystem_ComponentMap {

	var $heading = "Blog";
	var $nav_name = "blog";

	var $paths = array(
		'fields' => 'AMP/Content/Blog/Fields.xml',
		'list' => 'AMP/Content/Blog/List.inc.php',
		'form' => 'AMP/Content/Blog/Form.inc.php',
		'source' => 'AMP/Content/Blog/Blog.php' );


	var $components = array(
		'list' => 'Blog_List',
		'form' => 'Blog_Form',
		'source' => 'Blog' );
}
?>
