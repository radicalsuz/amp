<?php

//load DB
require_once( 'AMP/BaseDB.php');
require_once('AMP/System/Config.inc.php');

//activate the memcache class
if ( AMP_SITE_MEMCACHE_ON ) require_once('AMP/System/Memcache.inc.php');

//add the dbcon to the Registry
$registry =& AMP_Registry::instance();
$registry->setDbcon($dbcon);

//activate Lookups
require_once('AMP/System/Lookups.inc.php');
$lookup_factory = & AMPSystem_LookupFactory::instance();
$lookup_factory->init( $dbcon );

//load Language files 
require_once( 'AMP/System/Language/Config.php');

//load sysvar table data
require_once('AMP/LegacyRegistry.inc.php');

//content configuration
require_once('AMP/Content/Config.inc.php');


// turn on APD debugger when set by config file
if ( AMP_DEBUG_MODE_APD ) apd_set_pprof_trace(); 

?>
