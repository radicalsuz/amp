<?php

//standard system pages
require_once( 'AMP/System/Page/Urls.inc.php');

if ( !defined( 'AMP_CONTENT_MAP_ROOT_SECTION' )) define( 'AMP_CONTENT_MAP_ROOT_SECTION' , 1 );
if ( !defined( 'AMP_SYSTEM_UNIQUE_ID')) define ( 'AMP_SYSTEM_UNIQUE_ID', AMP_DB_NAME );
if ( !defined( 'AMP_SITE_MEMCACHE_ON' ))       define ('AMP_SITE_MEMCACHE_ON', true);
if ( !defined( 'AMP_SYSTEM_MEMCACHE_SERVER' ))      define ('AMP_SYSTEM_MEMCACHE_SERVER', 'localhost' );
if ( !defined( 'AMP_SYSTEM_MEMCACHE_PORT' ))        define ('AMP_SYSTEM_MEMCACHE_PORT',   '11211' );
if ( !defined( 'AMP_SITE_MEMCACHE_TIMEOUT')) define ( 'AMP_SITE_MEMCACHE_TIMEOUT', 180 );

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
if ( !defined( 'AMP_NULL_DATE_VALUE_DB')) define('AMP_NULL_DATE_VALUE_DB', '0000-00-00' );
if ( !defined( 'AMP_NULL_DATE_VALUE_FORM')) define('AMP_NULL_DATE_VALUE_FORM', '2001-01-01' );
if ( !defined( 'AMP_NULL_DATE_VALUE_RSS')) define('AMP_NULL_DATE_VALUE_RSS', '1969-12-31' );
if ( !defined( 'AMP_BLANK_DATETIME_VALUE_FORM')) define('AMP_BLANK_DATETIME_VALUE_FORM', '2001-01-01 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE')) define('AMP_NULL_DATETIME_VALUE', '0000-00-00 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_FORM')) define('AMP_NULL_DATETIME_VALUE_FORM', '2001-11-30 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_FORM_2')) define('AMP_NULL_DATETIME_VALUE_FORM_2', '2001-01-01 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_UNIX')) define('AMP_NULL_DATETIME_VALUE_UNIX', '1969-12-31 16:33:25' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_UNIX_2')) define('AMP_NULL_DATETIME_VALUE_UNIX_2', '1969-12-31 16:33:26' );

if ( !defined( 'AMP_FUTURE_DATETIME_VALUE')) define('AMP_FUTURE_DATETIME_VALUE', '2100-12-31 16:33:26' );

/**
 * Cache Settings
 */
if ( !defined( 'AMP_SYSTEM_CACHE')) define( 'AMP_SYSTEM_CACHE', 'memcache' );
if ( !defined( 'AMP_SYSTEM_CACHE_TIMEOUT')) define( 'AMP_SYSTEM_CACHE_TIMEOUT', 600 );
if ( !defined( 'AMP_SYSTEM_CACHE_PATH')) define( 'AMP_SYSTEM_CACHE_PATH', AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'cache');

/**
 * Filesystem Settings 
 */
if ( !defined( 'AMP_SYSTEM_FILE_OWNER')) define( 'AMP_SYSTEM_FILE_OWNER', false );

/**
 * Strange legacy settings 
 */
if ( !defined( 'AMP_SYSTEM_SETTING_DB_ID')) define( 'AMP_SYSTEM_SETTING_DB_ID', 1);
if ( !defined( 'PHPLIST_CONFIG_ADMIN_ID')) define( 'PHPLIST_CONFIG_ADMIN_ID', 1);
if ( !defined( 'AMP_DBTABLE_BLAST_LISTS')) define ( 'AMP_DBTABLE_BLAST_LISTS', false );

/**
 * Email Blaster Options  
 */
if ( !defined( 'AMP_MODULE_BLAST')) define ( 'AMP_MODULE_BLAST', 'AMP');

/**
 * ADODB Options
 */
define('ADODB_REPLACE_INSERTED', 2);
define('ADODB_REPLACE_UPDATED',  1);


/**
 *  GLOBAL settings
 */
define( "AMP_SYSTEM_VERSION_ID", '3.7.0');
define( 'MAGIC_QUOTES_ACTIVE', get_magic_quotes_gpc());

/**
 * s3 settings - Amazon File Cache
 */
if ( !defined( 'AMP_SYSTEM_FILE_S3_KEY')) define( 'AMP_SYSTEM_FILE_S3_KEY', false );
if ( !defined( 'AMP_SYSTEM_FILE_S3_KEY_SECRET')) define( 'AMP_SYSTEM_FILE_S3_KEY_SECRET', false );


/**
 * Taggable Item Types
 **/

define( 'AMP_SYSTEM_ITEM_TYPE_FORM', 'form');
define( 'AMP_SYSTEM_ITEM_TYPE_EVENT', 'event');
define( 'AMP_SYSTEM_ITEM_TYPE_ARTICLE', 'article');
define( 'AMP_SYSTEM_ITEM_TYPE_FILE', 'file');
define( 'AMP_SYSTEM_ITEM_TYPE_GALLERY', 'gallery');
define( 'AMP_SYSTEM_ITEM_TYPE_GALLERY_IMAGE', 'gallery_image');
define( 'AMP_SYSTEM_ITEM_TYPE_LINK', 'link');

/**
 * Permission System Settings 
 */
define( 'AMP_PATH_PHPGACL', 'phpgacl' );
define( 'AMP_PATH_PHPGACL_ADMIN', 'phpgacl/admin');

if ( !defined( 'AMP_SYSTEM_MENU_PATH')) {
    define( 'AMP_SYSTEM_MENU_PATH', 'AMP/System/Menu.inc.php');
}

?>
