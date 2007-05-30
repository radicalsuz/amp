<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_FlashMap extends AMPSystem_ComponentMap {

	var $heading = "FlashMap";
	var $nav_name = "flashmap";
    var $_action_default = 'list';

	var $paths = array(
		'fields' => 'AMP/Geo/FlashMap/Fields.xml',
		'list' => 'AMP/Geo/FlashMap/List.inc.php',
		'form' => 'AMP/Geo/FlashMap/Form.inc.php',
		'source' => 'AMP/Geo/FlashMap/FlashMap.php' );


	var $components = array(
		'list' => 'FlashMap_List',
		'form' => 'FlashMap_Form',
		'source' => 'AMPSystem_FlashMap' );
}
?>
