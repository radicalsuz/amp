<?php

ob_start();

require_once('AMP/BaseDB.php');
require_once('AMP/System/Lookups.inc.php');
$lookup_factory = & AMPSystem_LookupFactory::instance();
$lookup_factory->init( $dbcon );

setBrowser();

require_once("AMP/System/Permission/Manager.inc.php");

$userLevel = $_SERVER['REMOTE_GROUP'];
$AMP_Permission = & AMPSystem_PermissionManager::instance();
$AMP_Permission->readLevel( $dbcon, $userLevel );
if (!AMP_Authorized( AMP_PERMISSION_CONTENT_ACCESS)) header ("Location: index.php");

//ENSURE THAT THE current user is allowed to see this page
$AMP_Permission->readUser( $dbcon, $_SERVER['REMOTE_USER'] );
if (!$AMP_Permission->authorizedPage()) {
    ampredirect( $AMP_Permission->userHome() );
}

/* This $userper var is deprecated, modules should now check the AMPSystem_Permissions
 * singleton like so:
 *
 * if (AMP_Authorized( CONSTANT_PERMISSION ));
 *
 */
 
$userper = $AMP_Permission->permission_array;

$gettop = $dbcon->Execute("SELECT subsite FROM per_group WHERE id = $userLevel")
            or die( "Couldn't find sub-site authentication information: " . $dbcon->ErrorMsg());
$MX_top = $gettop->Fields("subsite"); 

require_once('AMP/Content/Map.inc.php');
$system_map = & AMPContent_Map::instance();
$system_map->init( $dbcon, $MX_top );

require_once("AMP/System/Help.inc.php");
require_once("AMP/DBfunctions.inc.php");
?>
