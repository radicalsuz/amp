<?php
//load DB connection
require_once( 'AMP/Base/DB.php');

//server config file
if ( file_exists_incpath( 'AMP/HostConfig.inc.php')) {
    include_once( 'AMP/HostConfig.inc.php');
}

//custom configuration
AMP_config_load( 'site' );

//system wide configuration
require_once( 'AMP/System/Config.inc.php');

//load Language files 
require_once( 'AMP/System/Language/Config.php');

//debug values initialized
require_once( 'AMP/Base/Debug.php');

//cache tokens and settings
//require_once( 'AMP/System/Cache/Config.inc.php');
AMP_config_load( 'cache');
AMP_block_frequent_requesters( );

//db action : load DB system settings ( sysvar table )
require_once( 'AMP/Base/Setup.php');

//for running under fastcgi
if( isset( $_SERVER['AMP_REQUIRE_AUTH']) && $_SERVER['AMP_REQUIRE_AUTH']) {
    require_once( 'AMP/Auth/Require.inc.php');
}

//load user
require_once( 'AMP/System/User/Config.php');

//module setup
AMP_config_load( 'tools' );

//db action : initialize lookups
require_once( 'AMP/Base/Lookups.php');

//content configuration
AMP_config_load( 'content' ); #NOTE: already loaded by the above require_once( 'AMP/Base/Lookups.php'); -seth
require_once( 'AMP/Content/Map.inc.php');

//custom includes
if ( file_exists_incpath( 'custom.layouts.inc.php' )) include_once ('custom.layouts.inc.php' );
if ( file_exists_incpath( 'custom.sources.inc.php' )) include_once ('custom.sources.inc.php' );
if ( file_exists_incpath( 'custom.includes.inc.php' )) include_once ('custom.includes.inc.php' );
if ( file_exists_incpath( 'custom.translations.inc.php' )) include_once ('custom.translations.inc.php' );

//stylesheets
AMP_config_load( 'css', 'amp_content_css');

//old legacy settings
if ( !( isset( $no_legacy ) && $no_legacy )) {
    require_once( 'AMP/LegacyRegistry.inc.php');
}

/*
foreach( $_REQUEST as $rkey => $rvalue ) {
    if( !is_array( $rvalue )) {
        if ( $rkey != 'body' && preg_match( '/union.{1,8}select|insert.{1,8}into|delete.{1,8}from/', $rvalue )) {
            trigger_error( 'SQL injection detected in '. $_SERVER['SCRIPT_NAME'].' '.$rkey.': '. $rvalue );
            exit;
        }

    }
}
*/
?>
