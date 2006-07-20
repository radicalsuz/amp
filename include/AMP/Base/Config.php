<?php
//load DB
require_once( 'AMP/Base/DB.php');

//system wide configuration
require_once( 'AMP/System/Config.inc.php');

//load Language files 
require_once( 'AMP/System/Language/Config.php');

//debug values initialized
require_once( 'AMP/Base/Debug.php');

//db action : initialize lookups
require_once( 'AMP/Base/Lookups.php');

//db action : load DB system settings ( sysvar table )
require_once( 'AMP/Base/Setup.php');

//content configuration
require_once( 'AMP/Content/Config.inc.php');

//load user
require_once( 'AMP/System/User/Config.php');

//activate the memcache class
if ( AMP_SITE_MEMCACHE_ON ) require_once('AMP/System/Memcache.inc.php');

//old legacy settings
require_once( 'AMP/LegacyRegistry.inc.php');

?>
