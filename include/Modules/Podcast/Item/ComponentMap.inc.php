<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_PodcastItem extends AMPSystem_ComponentMap {

	var $heading = "Podcast Item";
	var $nav_name = "podcast";

	var $paths = array(
		'fields' => 'Modules/Podcast/Item/Fields.xml',
		'list' => 'Modules/Podcast/Item/List.inc.php',
		'form' => 'Modules/Podcast/Item/Form.inc.php',
		'source' => 'Modules/Podcast/PodcastItem.php' );


	var $components = array(
		'list' => 'PodcastItem_List',
		'form' => 'PodcastItem_Form',
		'source' => 'AMPSystem_PodcastItem' );
}
?>
