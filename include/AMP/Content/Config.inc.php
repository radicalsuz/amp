<?php

AMP_config_load( 'content');


// why do you seek the living among the dead?
// go to Config/content.ini.php
// there you will find your answers
/*

define( 'AMP_CACHE_TOKEN_URL_CONTENT', '__URL__' );

/**
 * Section List Configurations
 *
if (!defined( 'AMP_CONTENT_LISTORDER_MAX')) define('AMP_CONTENT_LISTORDER_MAX', 999999999);
if (!defined( 'AMP_CONTENT_LIST_DISPLAY_MAX')) define('AMP_CONTENT_LIST_DISPLAY_MAX', 300 );

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
if ( file_exists_incpath( 'custom.includes.inc.php' )) include_once ('custom.includes.inc.php' );
if ( file_exists_incpath( 'custom.translations.inc.php' )) include_once ('custom.translations.inc.php' );

/**
 * Class List Configurations
 *
if ( !defined( 'AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT'  ))
    define( 'AMP_CONTENT_CLASSLIST_DISPLAY_DEFAULT',    'ContentClass_Display' );
if ( !defined( 'AMP_CONTENT_CLASSLIST_DISPLAY_BLOG'     ))
    define( 'AMP_CONTENT_CLASSLIST_DISPLAY_BLOG',       'ContentClass_Display_Blog' );
if ( !defined( 'AMP_CONTENT_CLASSLIST_DISPLAY_FRONTPAGE'))
    define( 'AMP_CONTENT_CLASSLIST_DISPLAY_FRONTPAGE',  'ContentClass_Display_FrontPage' );

/**
 * Content Status Configuration
 *
define ( 'AMP_CONTENT_STATUS_LIVE',  1);
define ( 'AMP_CONTENT_STATUS_DRAFT', 0);

/**
 *  Article Class Configurations
 *
if (!defined( 'AMP_CONTENT_CLASS_DEFAULT'))         define ('AMP_CONTENT_CLASS_DEFAULT' , 1 );
if (!defined( 'AMP_CONTENT_CLASS_FRONTPAGE' ))      define ('AMP_CONTENT_CLASS_FRONTPAGE' , 2 );
if (!defined( 'AMP_CONTENT_CLASS_SECTIONHEADER' ))  define ('AMP_CONTENT_CLASS_SECTIONHEADER' , 8 );
if (!defined( 'AMP_CONTENT_CLASS_NEWS' ))           define ('AMP_CONTENT_CLASS_NEWS' , 3 );
if (!defined( 'AMP_CONTENT_CLASS_MORENEWS'))        define ('AMP_CONTENT_CLASS_MORENEWS' , 4 );
if (!defined( 'AMP_CONTENT_CLASS_PRESSRELEASE'))    define ('AMP_CONTENT_CLASS_PRESSRELEASE' , 10 );
if (!defined( 'AMP_CONTENT_CLASS_USERSUBMITTED'))   define ('AMP_CONTENT_CLASS_USERSUBMITTED' , 9 );
if (!defined( 'AMP_CONTENT_CLASS_ACTIONITEM'))      define ('AMP_CONTENT_CLASS_ACTIONITEM' , 5 );
if (!defined( 'AMP_CONTENT_CLASS_BLOG' ))           define ('AMP_CONTENT_CLASS_BLOG', '20');
if (!defined( 'AMP_CONTENT_CLASS_SECTIONFOOTER'))   define ('AMP_CONTENT_CLASS_SECTIONFOOTER' , false );


/**
 *  Article Layouts
 *
if (!defined( 'AMP_ARTICLE_DISPLAY_DEFAULT'))   define( 'AMP_ARTICLE_DISPLAY_DEFAULT', 'Article_Display' );
if (!defined( 'AMP_ARTICLE_DISPLAY_FRONTPAGE')) define( 'AMP_ARTICLE_DISPLAY_FRONTPAGE', 'ArticleDisplay_FrontPage' );
if (!defined( 'AMP_ARTICLE_DISPLAY_NEWS'))      define( 'AMP_ARTICLE_DISPLAY_NEWS', 'ArticleDisplay_News' );
if (!defined( 'AMP_ARTICLE_DISPLAY_PRESSRELEASE')) define( 'AMP_ARTICLE_DISPLAY_PRESSRELEASE', 'ArticleDisplay_PressRelease' );
if (!defined( 'AMP_ARTICLE_DISPLAY_BLOG')) define( 'AMP_ARTICLE_DISPLAY_BLOG', 'ArticleDisplay_Blog' );

/**
 * Article Sidebar Configuration 
 *
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_DEFAULT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_DEFAULT' , 'sidebar_right') ;
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_LEFT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_LEFT' , 'sidebar_left') ;
if (!defined( 'AMP_CONTENT_SIDEBAR_CLASS_RIGHT'))      define ('AMP_CONTENT_SIDEBAR_CLASS_RIGHT' , 'sidebar_right') ;

/**
 * Definitions for system images 
 *
define( 'AMP_ICON_SPACER', 'spacer.gif' );
define( 'AMP_ICON_UP', 'go-up.png' );
define( 'AMP_ICON_DOWN', 'go-down.png' );
define( 'AMP_ICON_LIST', 'list_page.png' );
define( 'AMP_ICON_LIST_ADD', 'list_page_create.png' );
define( 'AMP_ICON_CONTENT', 'content_page.png' );
define( 'AMP_ICON_CONTENT_ADD', 'content_page_create.png' );

if ( !defined( 'AMP_ICON_COLUMN_FOOTER' ))              define( 'AMP_ICON_COLUMN_FOOTER', false );

/**
 * Notation used within navs and introtexts to indicate a php include file 
 *
define( 'AMP_INCLUDE_START_TAG', '{{' );
define( 'AMP_INCLUDE_END_TAG', '}}' );

/**
 * Image Classes and Image settings 
 *
if ( !defined( 'AMP_IMAGE_CLASS_ORIGINAL')) define( 'AMP_IMAGE_CLASS_ORIGINAL', 'original' );
if ( !defined( 'AMP_IMAGE_CLASS_THUMB'))    define( 'AMP_IMAGE_CLASS_THUMB', 'thumb' );
if ( !defined( 'AMP_IMAGE_CLASS_OPTIMIZED'))define( 'AMP_IMAGE_CLASS_OPTIMIZED', 'pic' );
if ( !defined( 'AMP_IMAGE_CLASS_CROP'))     define( 'AMP_IMAGE_CLASS_CROP', 'crop' );

if (!defined('AMP_IMAGE_DEFAULT_ALIGNMENT')) define( 'AMP_IMAGE_DEFAULT_ALIGNMENT', 'right' );
if ( !defined( 'AMP_IMAGE_PATH'))
    define( 'AMP_IMAGE_PATH', DIRECTORY_SEPARATOR . 'img' . DIRECTORY_SEPARATOR ); 

if ( !defined( 'AMP_IMAGE_GALLERY_PAGE_LIMIT')) define( 'AMP_IMAGE_GALLERY_PAGE_LIMIT', 24 );

/**
 * Content List Types
 *
define ( 'AMP_CONTENT_LISTTYPE_CLASS', 'class' );
define ( 'AMP_CONTENT_LISTTYPE_SECTION', 'type' );
define ( 'AMP_CONTENT_LISTTYPE_FRONTPAGE', 'index' );
define ( 'AMP_CONTENT_LISTTYPE_REGION', 'region' );

/**
 * Page Types 
 *

define( 'AMP_CONTENT_PAGETYPE_ARTICLE', 'article' );
define( 'AMP_CONTENT_PAGETYPE_LIST', 'list' );
define( 'AMP_CONTENT_PAGETYPE_TOOL', 'tool' );

/**
 * System Icon File Paths
 *
define ('AMP_SYSTEM_ICON_EDIT', '/system/images/edit.png' ); 
define ('AMP_SYSTEM_ICON_VIEW', '/system/images/view2.png' ); 
define ('AMP_SYSTEM_ICON_PREVIEW', '/system/images/view.gif' );
define ('AMP_SYSTEM_ICON_DELETE', '/system/images/delete.png' );
define ('AMP_SYSTEM_ICON_ENLARGE', '/img/magnify-clip.png' );

/**
 * Default value for unlimited navs 
 *
if ( !defined( 'AMP_CONTENT_NAV_NO_LIMIT_QTY')) define('AMP_CONTENT_NAV_NO_LIMIT_QTY', 700);

if (!defined('AMP_NAVLINK_ALTERNATE_CSS_CLASS')) define( 'AMP_NAVLINK_ALTERNATE_CSS_CLASS', 'sidelist2' );
if (!defined( 'AMP_NAVLINK_CSS_CLASS' )) define ('AMP_NAVLINK_CSS_CLASS', 'sidelist' );
if (!defined( 'AMP_CONTENT_NAV_LIMIT_DEFAULT' )) define ('AMP_CONTENT_NAV_LIMIT_DEFAULT', 20 );

/**
 * Default Introtext pages
 *
if (!defined( 'AMP_CONTENT_INTRO_ID_DEFAULT' )) define( 'AMP_CONTENT_INTRO_ID_DEFAULT' , 1 );
if (!defined( 'AMP_CONTENT_INTRO_ID_FRONTPAGE' )) define( 'AMP_CONTENT_INTRO_ID_FRONTPAGE' , 2 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_ARTICLE_INPUT'))  
        define( 'AMP_CONTENT_PUBLICPAGE_ID_ARTICLE_INPUT', 41 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_ARTICLE_RESPONSE')) 
        define( 'AMP_CONTENT_PUBLICPAGE_ID_ARTICLE_RESPONSE', 49 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_SEARCH'))  
        define( 'AMP_CONTENT_PUBLICPAGE_ID_SEARCH', 40 );

define( 'AMP_CONTENT_PUBLICPAGE_ID_LINKS_DISPLAY', 12 );

if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_COMMENT_INPUT'))  
        define( 'AMP_CONTENT_PUBLICPAGE_ID_COMMENT_INPUT', 34 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_TAGS_DISPLAY')) 
        define( 'AMP_CONTENT_PUBLICPAGE_ID_TAGS_DISPLAY', 28 );

if (!defined('AMP_CONTENT_PUBLICPAGE_ID_WEBACTION_INPUT' ))
     define( 'AMP_CONTENT_PUBLICPAGE_ID_WEBACTION_INPUT', 62 );

if ( !defined( 'AMP_CONTENT_PUBLICPAGE_ID_CONTACT_US')) define( 'AMP_CONTENT_PUBLICPAGE_ID_CONTACT_US', 52 );
if ( !defined( 'AMP_CONTENT_PUBLICPAGE_ID_CONTACT_US_RESPONSE')) define( 'AMP_CONTENT_PUBLICPAGE_ID_CONTACT_US_RESPONSE', 53 );

if ( !defined( 'AMP_INTROTEXT_ID_CONTACT_US')) define( 'AMP_INTROTEXT_ID_CONTACT_US', 52 );
if ( !defined( 'AMP_INTROTEXT_ID_CONTACT_US_RESPONSE')) define( 'AMP_INTROTEXT_ID_CONTACT_US_RESPONSE', 53 );

if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_INPUT'))  
        define( 'AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_INPUT', 15 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_RESPONSE')) 
        define( 'AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_RESPONSE', 51 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_DISPLAY')) 
        define( 'AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_DISPLAY', 57 );

if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_HOUSING_DISPLAY')) 
        define( 'AMP_CONTENT_PUBLICPAGE_ID_HOUSING_DISPLAY', 8 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_HOUSING_INPUT'))  
        define( 'AMP_CONTENT_PUBLICPAGE_ID_HOUSING_INPUT', 6 );
if ( !defined(  'AMP_CONTENT_PUBLICPAGE_ID_HOUSING_RESPONSE')) 
        define( 'AMP_CONTENT_PUBLICPAGE_ID_HOUSING_RESPONSE', 19 );

if ( !defined( 'AMP_CONTENT_ARTICLE_BLURB_LENGTH_DEFAULT'))
    define( 'AMP_CONTENT_ARTICLE_BLURB_LENGTH_DEFAULT', 750 );
if ( !defined( 'AMP_CONTENT_BLURB_LENGTH_MAX'))
    define( 'AMP_CONTENT_BLURB_LENGTH_MAX', 9000 );

/**
 * RSS definitions 
 *
if ( !defined( 'AMP_CONTENT_RSS_FULLTEXT'))
    define( 'AMP_CONTENT_RSS_FULLTEXT', false);
if (!defined('AMP_CONTENT_RSS_CUSTOMFORMAT')) define ('AMP_CONTENT_RSS_CUSTOMFORMAT', false);

/**
 * Default Module Definitions 
 *
define( 'AMP_MODULE_ID_CONTENT', 19 );
if ( !defined( 'AMP_MODULE_ID_GALLERY')) define( 'AMP_MODULE_ID_GALLERY', 8 );
if ( !defined( 'AMP_MODULE_ID_COMMENTS')) define( 'AMP_MODULE_ID_COMMENTS', 23 );
if ( !defined( 'AMP_MODULE_ID_RSS_SUBSCRIPTIONS')) define( 'AMP_MODULE_ID_RSS_SUBSCRIPTIONS', 45 );
if ( !defined( 'AMP_MODULE_ID_PETITION')) define( 'AMP_MODULE_ID_PETITION', 7 );
define( 'AMP_MODULE_ID_LINKS', 11 );
if ( !defined( 'AMP_MODULE_ID_CONTACT_US')) define( 'AMP_MODULE_ID_CONTACT_US', 17 );
if ( !defined( 'AMP_MODULE_ID_TELL_A_FRIEND')) define( 'AMP_MODULE_ID_TELL_A_FRIEND', 22 );

if ( !defined( 'AMP_FORM_ID_WEBACTION' )) define( 'AMP_FORM_ID_WEBACTION', 21 );

/**
 * Content Caching Settings
 *
if ( !defined( 'AMP_SYSTEM_CACHE_TIMEOUT_FRONTPAGE')) 
    define( 'AMP_SYSTEM_CACHE_TIMEOUT_FRONTPAGE', AMP_SYSTEM_CACHE_TIMEOUT );

/**
 * Bizarre Legacy Settings  
 *
if ( !defined( 'AMP_CONTENT_SECTION_ID_TOOL_PAGES' ))
    define( 'AMP_CONTENT_SECTION_ID_TOOL_PAGES', 2 );

if ( !defined ( 'AMP_CONTENT_TRACKBACKS_ENABLED' )) {
    define( 'AMP_CONTENT_TRACKBACKS_ENABLED', false );
}

/**
 * Document Types
 *
define ('AMP_CONTENT_DOCUMENT_PATH', 'downloads');
define ('AMP_CONTENT_DOCUMENT_TYPE_PDF', 'pdf');
define ('AMP_CONTENT_DOCUMENT_TYPE_WORD', 'word');
define ('AMP_CONTENT_DOCUMENT_TYPE_DEFAULT', 'file');
define ('AMP_CONTENT_DOCUMENT_TYPE_IMAGE', 'img');

define ('AMP_CONTENT_DOCUMENT_TYPE_MOV',  'mov');
define ('AMP_CONTENT_DOCUMENT_TYPE_FLV',  'flv');
define ('AMP_CONTENT_DOCUMENT_TYPE_WMV',  'wmv');

define ('AMP_ICON_WORD', 'worddoc.gif' );
define ('AMP_ICON_PDF', 'pdf.gif' );
define ('AMP_ICON_IMAGE', 'img.gif' );
define ('AMP_ICON_IMG', 'img.gif' );

define ('AMP_ICON_WMV', 'wmv.jpg' );
define ('AMP_ICON_FLV', 'flv.jpg' );
define ('AMP_ICON_MOV', 'mov.jpg' );

define ('AMP_CONTENT_URL_ICONS', '/img/' );

/**
 * Settings for the Display Manager
 *

if (!defined( 'AMP_CONTENT_CONTAINER_ID_BUFFER' )) define ('AMP_CONTENT_CONTAINER_ID_BUFFER', false );
if (!defined( 'AMP_CONTENT_CONTAINER_ID_FLASH' )) define ('AMP_CONTENT_CONTAINER_ID_FLASH', 'AMP_flash');
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_FLASH' )) define ('AMP_CONTENT_DISPLAY_KEY_FLASH', "flash");
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_INTRO' )) define ('AMP_CONTENT_DISPLAY_KEY_INTRO', "intro");
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_BUFFER' )) define ('AMP_CONTENT_DISPLAY_KEY_BUFFER', "buffer");

/**
 * Default CSS classes 
 *
if (!defined( 'AMP_CONTENT_LIST_SUBHEADER_CLASS' )) define( 'AMP_CONTENT_LIST_SUBHEADER_CLASS', 'title' );


/**
 * DIA settings 
 *

if ( !defined( 'DIA_URL_EVENT_PAYMENT')) define( 'DIA_URL_EVENT_PAYMENT', 'https://secure.democracyinaction.org/dia/organizations/%s/event/checkOut.jsp?event_KEY=%s');
if ( !defined( 'DIA_URL_EVENT_RSVP')) define( 'DIA_URL_EVENT_RSVP', 'http://www.democracyinaction.org/dia/organizations/%s/event/index.jsp?event_KEY=%s');

//automatically save calendar events to DIA
if ( !defined( 'AMP_CALENDAR_DIA_AUTO_SAVE')) define( 'AMP_CALENDAR_DIA_AUTO_SAVE', false );
*/

?>
