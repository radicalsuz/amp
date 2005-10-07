<?php

require_once('AMP/BaseDB.php');

//Browser discovery
setBrowser();


//Initialize System Permissions
require_once("AMP/System/Permission/Manager.inc.php");

$userLevel = $_SERVER['REMOTE_GROUP'];
$AMP_Permission = & AMPSystem_PermissionManager::instance();
$AMP_Permission->readLevel( $dbcon, $userLevel );
if (!AMP_Authorized( AMP_PERMISSION_CONTENT_ACCESS)) header ("Location: index.php");
if ( $dbcon->ErrorMsg() ) trigger_error( 'CURRENT MESSAGE:'.$dbcon->ErrorMsg( ));

//ENSURE THAT THE current user is allowed to see this page
$AMP_Permission->readUser( $dbcon, $_SERVER['REMOTE_USER'] );
if (!$AMP_Permission->authorizedPage()) {
    ampredirect( $AMP_Permission->userHome() );
}

// Discover top of content hierarchy */
// No one is using this, removing for 3.5.4 -ap
/*
$gettop = $dbcon->Execute("SELECT subsite FROM per_group WHERE id = $userLevel")
            or die( "Couldn't find sub-site authentication information: " . $dbcon->ErrorMsg());
$MX_top = $gettop->Fields("subsite"); 
*/

//initialize Content Map
require_once('AMP/Content/Map.inc.php');
$content_map = & AMPContent_Map::instance();

//These items are deprecated 
//used in the older form-processing engine
require_once("AMP/System/Help.inc.php");
require_once("AMP/DBfunctions.inc.php");

?>
