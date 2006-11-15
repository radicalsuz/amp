<?php

//activate Lookups
require_once('AMP/System/Lookups.inc.php');
require_once('AMP/Registry.php');
$lookup_factory = & AMPSystem_LookupFactory::instance();
$lookup_factory->init( AMP_Registry::getDbcon( ));

//require_once( 'AMP/Content/Map.inc.php');

?>
