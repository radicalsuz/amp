<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Podcast extends AMPSystem_ComponentMap {

	var $heading = "Podcast";
	var $nav_name = "podcast";

	var $paths = array(
		'fields' => 'Modules/Podcast/Fields.xml',
		'list' => 'Modules/Podcast/List.inc.php',
		'form' => 'Modules/Podcast/Form.inc.php',
		'source' => 'Modules/Podcast/Podcast.php' );


	var $components = array(
		'list' => 'Podcast_List',
		'form' => 'Podcast_Form',
		'source' => 'AMPSystem_Podcast' );
}
?>
