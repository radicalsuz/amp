<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('AMP/Content/Map/Select.inc.php');
require_once ('Modules/Podcast/Podcast.php' );
require_once ('Modules/Podcast/ComponentMap.inc.php' );

class Podcast_Form extends AMPSystem_Form_XML {

	var $inital_form_links = array();

	function Podcast_Form() {
		$name = "Podcast";

		$this->init( $name );
	}

}

?> 
