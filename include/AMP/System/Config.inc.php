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
if ( !defined( 'AMP_NULL_DATETIME_VALUE')) define('AMP_NULL_DATETIME_VALUE', '0000-00-00 00:00:00' );
if ( !defined( 'AMP_NULL_DATETIME_VALUE_FORM')) define('AMP_NULL_DATETIME_VALUE_FORM', '2001-11-30 00:00:00' );
if ( !defined( 'AMP_BLANK_DATETIME_VALUE_FORM')) define('AMP_BLANK_DATETIME_VALUE_FORM', '2001-01-01 00:00:00' );
?>
