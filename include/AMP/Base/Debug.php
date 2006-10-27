<?php

/**
 * Debug Modes 
 */
define('AMP_DISPLAYMODE_DEBUG',         (isset($_GET['debug'])          && $_GET['debug']));
define('AMP_DISPLAYMODE_DEBUG_CACHE',   (isset($_GET['debug_cache'])    && $_GET['debug_cache']));
define('AMP_DISPLAYMODE_DEBUG_PLUGINS', (isset($_GET['debug_plugins'])  && $_GET['debug_plugins']));
define('AMP_DISPLAYMODE_DEBUG_LOOKUPS', (isset($_GET['debug_lookups'])  && $_GET['debug_lookups']));
define('AMP_DISPLAYMODE_DEBUG_NAVS',    (isset($_GET['debug_navs'])     && $_GET['debug_navs']));
define('AMP_DISPLAYMODE_DEBUG_DIA',     (isset($_GET['debug_dia'])      && $_GET['debug_dia']));

$display_cache_setting = ( AMP_DISPLAYMODE_DEBUG_DIA 
                        || AMP_DISPLAYMODE_DEBUG_NAVS
                        || AMP_DISPLAYMODE_DEBUG_LOOKUPS
                        || AMP_DISPLAYMODE_DEBUG_PLUGINS
                        || AMP_DISPLAYMODE_DEBUG_CACHE
                        || AMP_DISPLAYMODE_DEBUG 
                        || ( isset( $_GET['cache']) && $_GET['cache'] === '0'));

define( 'AMP_DISPLAYMODE_CACHE_OFF', $display_cache_setting );

if ( !defined( 'AMP_SYSTEM_CUSTOM_ERROR_HANDLER' ))
    define('AMP_SYSTEM_CUSTOM_ERROR_HANDLER',     false );

if ( AMP_SYSTEM_CUSTOM_ERROR_HANDLER ) set_error_handler( AMP_SYSTEM_CUSTOM_ERROR_HANDLER );

// turn on APD debugger when set by config file
if ( !defined( 'AMP_DEBUG_MODE_APD')) define ('AMP_DEBUG_MODE_APD', false );
if ( AMP_DEBUG_MODE_APD ) apd_set_pprof_trace(); 

if ( !defined( 'AMP_DEBUG_MODE_COMPONENT_CACHE_INACTIVE')) define( 'AMP_DEBUG_MODE_COMPONENT_CACHE_INACTIVE', false );

?>
