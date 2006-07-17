<?php
require_once("AMP/System/Permission/Manager.inc.php");
//permission config file
require_once( 'AMP/System/Permission/Config.inc.php' );
require_once( 'AMP/System/Flash.php');

$AMP_Permission = & AMPSystem_PermissionManager::instance();

//legacy setup from < 3.5.9 
if ( isset( $_SERVER['REMOTE_USER']))  {

    $AMP_CurrentUser = false;
    $AMP_CurrentUser = &$AMP_Permission->readUser( $_SERVER['REMOTE_USER'] );
    if ( $AMP_CurrentUser ) {
        if ( !defined( 'AMP_SYSTEM_USER_ID' )) define( 'AMP_SYSTEM_USER_ID', $AMP_CurrentUser->id);

        if ( !AMP_Authorized( AMP_PERMISSION_CONTENT_ACCESS)) {
            trigger_error( 'content access not authorized for user '.$AMP_CurrentUser->getName( ) );
            ampredirect ( AMP_SITE_URL );
        }

        //ENSURE THAT THE current user is allowed to see this page
        if ( !$AMP_Permission->authorizedPage()) {
            trigger_error( 'unauthorized page access attempt by '. $AMP_CurrentUser->getName( ) );
            ampredirect( $AMP_Permission->userHome() );
        }

    }
}

// confirm whether WYSIWYG editor should be allowed for this machine
// this should probably eventually become a user setting instead of a cookie
if ( !defined( 'AMP_USER_CONFIG_USE_WYSIWYG') ){
    if ( isset( $_COOKIE['AMPWYSIWYG'] )) {
        $wysiwyg_setting =  !( $_COOKIE['AMPWYSIWYG'] == 'none' || $_COOKIE['AMPWYSIWYG'] == false );
        define( 'AMP_USER_CONFIG_USE_WYSIWYG', $wysiwyg_setting);
    } else {
        define( 'AMP_USER_CONFIG_USE_WYSIWYG', true );
    }
}

if ( !defined( 'AMP_USER_CONFIG_CONTENT_MODE_TRANSFER')){
    $tranfer_mode = false;
    if ( isset( $_COOKIE['AMPTransferMode'])){
        $tranfer_mode = $_COOKIE['AMPTransferMode'];
    }
    define( 'AMP_USER_CONFIG_CONTENT_MODE_TRANSFER', $tranfer_mode );
}


//define Unique Visitor ID
//$unique_visitor_cookie_name = 'AMP_SYSTEM_UNIQUE_VISITOR_ID';
$unique_visitor_cookie_name = 'AMPSystemGuest';

if ( defined( 'AMP_SYSTEM_USER_ID' ) && AMP_SYSTEM_USER_ID )  {
    define( 'AMP_SYSTEM_UNIQUE_VISITOR_ID', AMP_SYSTEM_USER_ID );
} elseif ( isset( $_COOKIE[ $unique_visitor_cookie_name ])) {
    define( 'AMP_SYSTEM_UNIQUE_VISITOR_ID', $_COOKIE[ $unique_visitor_cookie_name ]);
//  trigger_error( $_SERVER['REQUEST_URI'] .' found cookie unique id: ' . AMP_SYSTEM_UNIQUE_VISITOR_ID );
} elseif ( isset( $_POST['AMP_SYSTEM_UNIQUE_VISITOR_ID'])) {
    define( 'AMP_SYSTEM_UNIQUE_VISITOR_ID', $_POST['AMP_SYSTEM_UNIQUE_VISITOR_ID']);
    setcookie( $unique_visitor_cookie_name, AMP_SYSTEM_UNIQUE_VISITOR_ID );
//  trigger_error( $_SERVER['REQUEST_URI'] . ' found POST unique id: ' . AMP_SYSTEM_UNIQUE_VISITOR_ID );
} else {
    define( 'AMP_SYSTEM_UNIQUE_VISITOR_ID',  sha1( $_SERVER['REMOTE_ADDR'] . date( "Y-m-d h:i:s")));
    setcookie( $unique_visitor_cookie_name, AMP_SYSTEM_UNIQUE_VISITOR_ID );
//  trigger_error( $_SERVER['REQUEST_URI'] .' created unique id: ' . AMP_SYSTEM_UNIQUE_VISITOR_ID );
}

?>
