<?php

require_once('AMP/BaseDB.php');

//Browser discovery
setBrowser();

//Initialize System Permissions
require_once("AMP/System/Permission/Manager.inc.php");

$AMP_Permission = & AMPSystem_PermissionManager::instance();
$AMP_CurrentUser = &$AMP_Permission->readUser( $_SERVER['REMOTE_USER'] );

if (!AMP_Authorized( AMP_PERMISSION_CONTENT_ACCESS)) {
    trigger_error( 'content access not authorized for user '.$AMP_CurrentUser->getName( ) );
    ampredirect ( AMP_SITE_URL );
}

//ENSURE THAT THE current user is allowed to see this page
if (!$AMP_Permission->authorizedPage()) {
    trigger_error( 'unauthorized page access attempt by '. $AMP_CurrentUser->getName( ) );
    ampredirect( $AMP_Permission->userHome() );
}

//initialize Content Map
require_once('AMP/Content/Map.inc.php');
$content_map = & AMPContent_Map::instance();

//standard system pages
require_once( 'AMP/System/Page/Urls.inc.php');

//These items are deprecated 
//used in the older form-processing engine
require_once("AMP/System/Help.inc.php");
require_once("AMP/DBfunctions.inc.php");

?>
