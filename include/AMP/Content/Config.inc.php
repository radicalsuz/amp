<?php

/**
 * Section List Configurations
 */
if (!defined( 'AMP_CONTENT_LISTORDER_MAX')) define('AMP_CONTENT_LISTORDER_MAX', 999999999);

if (!defined( 'AMP_SECTIONLIST_ARTICLES'))      define( 'AMP_SECTIONLIST_ARTICLES', '1' );
if (!defined( 'AMP_SECTIONLIST_NEWSROOM'))      define( 'AMP_SECTIONLIST_NEWSROOM', '2' );
if (!defined( 'AMP_SECTIONLIST_SUBSECTIONS'))   define( 'AMP_SECTIONLIST_SUBSECTIONS', '5' );
if (!defined( 'AMP_SECTIONLIST_ARTICLES_BY_SUBSECTION')) 
                                                define( 'AMP_SECTIONLIST_ARTICLES_BY_SUBSECTION', '3' );
if (!defined( 'AMP_SECTIONLIST_SUBSECTIONS_PLUS_ARTICLES')) 
                                                define( 'AMP_SECTIONLIST_SUBSECTIONS_PLUS_ARTICLES', '6' );
if (!defined( 'AMP_SECTIONLIST_ARTICLES_AGGREGATOR')) 
                                                define( 'AMP_SECTIONLIST_ARTICLES_AGGREGATOR', '7' );

if ( file_exists_incpath( 'custom.layouts.inc.php' )) include_once ('custom.layouts.inc.php' );
if ( file_exists_incpath( 'custom.sources.inc.php' )) include_once ('custom.sources.inc.php' );

/**
 * Class List Configurations
 */
define( 'AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT', 'ContentClass_Display');
define( 'AMP_CONTENT_CLASSLIST_DISPLAY_BLOG', 'ContentClass_Display_Blog');

/**
 * Content Status Configuration
 */
define ('AMP_CONTENT_STATUS_LIVE', 1);
define ('AMP_CONTENT_STATUS_DRAFT', 0);

/**
 *  Article Class Configurations
 */
define ('AMP_CONTENT_CLASS_DEFAULT' , 1 );
if (!defined( 'AMP_CONTENT_CLASS_FRONTPAGE' ))      define ('AMP_CONTENT_CLASS_FRONTPAGE' , 2 );
if (!defined( 'AMP_CONTENT_CLASS_SECTIONHEADER' ))  define ('AMP_CONTENT_CLASS_SECTIONHEADER' , 8 );
if (!defined( 'AMP_CONTENT_CLASS_NEWS' ))           define ('AMP_CONTENT_CLASS_NEWS' , 3 );
define ('AMP_CONTENT_CLASS_MORENEWS' , 4 );
if (!defined( 'AMP_CONTENT_CLASS_PRESSRELEASE'))    define ('AMP_CONTENT_CLASS_PRESSRELEASE' , 10 );
define ('AMP_CONTENT_CLASS_USERSUBMITTED' , 9 );
if (!defined( 'AMP_CONTENT_CLASS_ACTIONITEM'))      define ('AMP_CONTENT_CLASS_ACTIONITEM' , 5 );
if (!defined( 'AMP_CONTENT_CLASS_BLOG' ))           define ('AMP_CONTENT_CLASS_BLOG', '20');
if (!defined( 'AMP_CONTENT_CLASS_SECTIONFOOTER'))      define ('AMP_CONTENT_CLASS_SECTIONFOOTER' , false );


/**
 *  Article Layouts
 */
if (!defined( 'AMP_ARTICLE_DISPLAY_DEFAULT'))   define( 'AMP_ARTICLE_DISPLAY_DEFAULT', 'Article_Display' );
if (!defined( 'AMP_ARTICLE_DISPLAY_FRONTPAGE')) define( 'AMP_ARTICLE_DISPLAY_FRONTPAGE', 'ArticleDisplay_FrontPage' );
if (!defined( 'AMP_ARTICLE_DISPLAY_NEWS'))      define( 'AMP_ARTICLE_DISPLAY_NEWS', 'ArticleDisplay_News' );
if (!defined( 'AMP_ARTICLE_DISPLAY_PRESSRELEASE')) define( 'AMP_ARTICLE_DISPLAY_PRESSRELEASE', 'ArticleDisplay_PressRelease' );
if (!defined( 'AMP_ARTICLE_DISPLAY_BLOG')) define( 'AMP_ARTICLE_DISPLAY_BLOG', 'ArticleDisplay_Blog' );

/**
 * Article Sidebar Configuration 
 */
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_DEFAULT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_DEFAULT' , 'sidebar_right') ;
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_LEFT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_LEFT' , 'sidebar_left') ;
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_RIGHT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_RIGHT' , 'sidebar_right') ;

/**
 * Definitions for system images 
 */
define( 'AMP_ICON_SPACER', 'spacer.gif' );

/**
 * Notation used within navs and introtexts to indicate a php include file 
 */
define( 'AMP_INCLUDE_START_TAG', '{{' );
define( 'AMP_INCLUDE_END_TAG', '}}' );

/**
 * Image Classes and Image settings 
 */
if ( !defined( 'AMP_IMAGE_CLASS_ORIGINAL')) define( 'AMP_IMAGE_CLASS_ORIGINAL', 'original' );
if ( !defined( 'AMP_IMAGE_CLASS_THUMB'))    define( 'AMP_IMAGE_CLASS_THUMB', 'thumb' );
if ( !defined( 'AMP_IMAGE_CLASS_OPTIMIZED'))define( 'AMP_IMAGE_CLASS_OPTIMIZED', 'pic' );
if ( !defined( 'AMP_IMAGE_CLASS_CROP'))     define( 'AMP_IMAGE_CLASS_CROP', 'crop' );

if (!defined('AMP_IMAGE_DEFAULT_ALIGNMENT')) define( 'AMP_IMAGE_DEFAULT_ALIGNMENT', 'right' );
if ( !defined( 'AMP_IMAGE_PATH'))
    define( 'AMP_IMAGE_PATH', DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR ); 

/**
 * Content List Types
 */
define ( 'AMP_CONTENT_LISTTYPE_CLASS', 'class' );
define ( 'AMP_CONTENT_LISTTYPE_SECTION', 'type' );
define ( 'AMP_CONTENT_LISTTYPE_FRONTPAGE', 'index' );
define ( 'AMP_CONTENT_LISTTYPE_REGION', 'region' );

/**
 * Memcache Keys 
 */
define ( 'MEMCACHE_KEY_CONTENTMAP', 'ContentMap' );

/**
 * Page Types 
 */

define( 'AMP_CONTENT_PAGETYPE_ARTICLE', 'article' );
define( 'AMP_CONTENT_PAGETYPE_LIST', 'list' );
define( 'AMP_CONTENT_PAGETYPE_TOOL', 'tool' );

/**
 * System Icon File Paths
 */
define ('AMP_SYSTEM_ICON_EDIT', '/system/images/edit.png' ); 
define ('AMP_SYSTEM_ICON_PREVIEW', '/system/images/view.gif' );
define ('AMP_SYSTEM_ICON_DELETE', '/system/images/delete.png' );

/**
 * Default value for unlimited navs 
 */
if ( !defined( 'AMP_NAV_NO_LIMIT')) define('AMP_NAV_NO_LIMIT', 700);

/**
 * Default Introtext page 
 */
if (!defined( 'AMP_CONTENT_INTRO_ID_DEFAULT' )) define( 'AMP_CONTENT_INTRO_ID_DEFAULT' , 1 );
/**
 * Allow multiple sections per article 
 */
if ( !defined( 'AMP_ARTICLE_ALLOW_MULTIPLE_SECTIONS'))
    define('AMP_ARTICLE_ALLOW_MULTIPLE_SECTIONS', ( ( isset($MM_reltype) && !$MM_reltype ) ? false : true ) );
?>