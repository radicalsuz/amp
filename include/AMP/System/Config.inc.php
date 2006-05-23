<?php
if ( !defined( 'AMP_SYSTEM_INCLUDE_PATH' ))     define( 'AMP_SYSTEM_INCLUDE_PATH', 'AMP/System');
if ( !defined( 'AMP_CONTENT_INCLUDE_PATH' ))    define( 'AMP_CONTENT_INCLUDE_PATH', 'AMP/Content');
if ( !defined( 'AMP_MODULE_INCLUDE_PATH' ))     define( 'AMP_MODULE_INCLUDE_PATH', 'Modules');
if ( !defined( 'AMP_COMPONENT_MAP_FILENAME' ))  define( 'AMP_COMPONENT_MAP_FILENAME', 'ComponentMap.inc.php');
if ( !defined( 'AMP_COMPONENT_MAP_CLASSNAME' )) define( 'AMP_COMPONENT_MAP_CLASSNAME', 'ComponentMap');

/**
 * Sort options
 */
if ( !defined( 'AMP_SORT_ASC' )) define( 'AMP_SORT_ASC', ' ASC');
if ( !defined( 'AMP_SORT_DESC' )) define( 'AMP_SORT_DESC', ' DESC');
if ( !defined( 'AMP_SORT_END' )) define( 'AMP_SORT_END', 'zzzzzzzzzzzzzzzz');

/**
 *  NULL values
 */
if ( !defined( 'AMP_NULL_DATE_VALUE')) define('AMP_NULL_DATE_VALUE', '0000-00-00' );
if ( !defined( 'AMP_NULL_DATE_VALUE_FORM')) define('AMP_NULL_DATE_VALUE_FORM', '2001-01-01' );
if ( !defined( 'AMP_NULL_DATE_VALUE_RSS')) define('AMP_NULL_DATE_VALUE_RSS', '1969-12-31' );
if ( !defined( 'AMP_BLANK_DATETIME_VALUE_FORM')) define('AMP_BLANK_DATETIME_VALUE_FORM', '2001-01-01 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE')) define('AMP_NULL_DATETIME_VALUE', '0000-00-00 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_FORM')) define('AMP_NULL_DATETIME_VALUE_FORM', '2001-11-30 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_FORM_2')) define('AMP_NULL_DATETIME_VALUE_FORM_2', '2001-01-01 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_UNIX')) define('AMP_NULL_DATETIME_VALUE_UNIX', '1969-12-31 16:33:25' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_UNIX_2')) define('AMP_NULL_DATETIME_VALUE_UNIX_2', '1969-12-31 16:33:26' );

/**
 * Menu Settings
 */
if ( !defined( 'AMP_CACHE_KEY_SYSTEM_MENU')) define( 'AMP_CACHE_KEY_SYSTEM_MENU', 'AMP_System_Menu_%s.html' );
if ( !defined( 'AMP_CACHE_KEY_SYSTEM_MENU_CSS')) define( 'AMP_CACHE_KEY_SYSTEM_MENU_CSS', 'AMP_System_Menu_%s.css' );
if ( !defined( 'AMP_CACHE_KEY_SYSTEM_MENU_JS')) define( 'AMP_CACHE_KEY_SYSTEM_MENU_JS', 'AMP_System_Menu_%s.js' );


/**
 * Cache Settings
 */
if ( !defined( 'AMP_SYSTEM_CACHE')) define( 'AMP_SYSTEM_CACHE', 'file' );
if ( !defined( 'AMP_SYSTEM_CACHE_TIMEOUT')) define( 'AMP_SYSTEM_CACHE_TIMEOUT', 600 );
if ( !defined( 'AMP_SYSTEM_CACHE_PATH')) define( 'AMP_SYSTEM_CACHE_PATH', AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'custom');

/**
 * Filesystem Settings 
 */
if ( !defined( 'AMP_SYSTEM_FILE_OWNER')) define( 'AMP_SYSTEM_FILE_OWNER', false );

/**
 * Strange legacy settings 
 */
if ( !defined( 'AMP_SYSTEM_SETTING_DB_ID')) define( 'AMP_SYSTEM_SETTING_DB_ID', 1);
if ( !defined( 'PHPLIST_CONFIG_ADMIN_ID')) define( 'PHPLIST_CONFIG_ADMIN_ID', 1);

/**
 * Email Blaster Options  
 */
if ( !defined( 'AMP_MODULE_BLAST')) define ( 'AMP_MODULE_BLAST', 'AMP');
?>
