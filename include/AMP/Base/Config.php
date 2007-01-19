<?php
//load DB connection
require_once( 'AMP/Base/DB.php');

//server config file
if ( file_exists_incpath( 'AMP/HostConfig.inc.php')) {
    include( 'AMP/HostConfig.inc.php');
}

//system wide configuration
require_once( 'AMP/System/Config.inc.php');

//load Language files 
require_once( 'AMP/System/Language/Config.php');

//debug values initialized
require_once( 'AMP/Base/Debug.php');

//cache tokens and settings
require_once( 'AMP/System/Cache/Config.inc.php');

//db action : load DB system settings ( sysvar table )
require_once( 'AMP/Base/Setup.php');

//load user
require_once( 'AMP/System/User/Config.php');

//db action : initialize lookups
require_once( 'AMP/Base/Lookups.php');

//content configuration
require_once( 'AMP/Content/Map.inc.php');


//old legacy settings
require_once( 'AMP/LegacyRegistry.inc.php');

?>
