<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('AMP/Geo/FlashMap/FlashMap.php' );
require_once ('AMP/Geo/FlashMap/ComponentMap.inc.php' );


class FlashMap_Form extends AMPSystem_Form_XML {

	var $inital_form_links = array();

	function FlashMap_Form() {
		$name = "FlashMap";
		$this->init( $name );
	}


}

?> 
