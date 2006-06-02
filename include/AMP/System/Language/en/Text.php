<?php

/**
 * Publish Status 
 */
define( 'AMP_PUBLISH_STATUS_LIVE' , 'live' );
define( 'AMP_PUBLISH_STATUS_DRAFT' , 'draft' );
define( 'AMP_TEXT_CONTENT_STATUS_EXPIRED' , 'expired' );
define( 'AMP_TEXT_CONTENT_STATUS_LIVE' , AMP_PUBLISH_STATUS_LIVE );
define( 'AMP_TEXT_CONTENT_STATUS_DRAFT' , AMP_PUBLISH_STATUS_DRAFT );


/**
 * Form Result messages
 */
define( 'AMP_TEXT_DATA_SAVE_SUCCESS', "%s has been saved." );
define( 'AMP_TEXT_DATA_COPY_SUCCESS',  "Your working copy was saved as %s");
define( 'AMP_TEXT_DATA_DELETE_SUCCESS',  "%s was deleted");

/**
 * List result messages
 */
define( 'AMP_TEXT_LIST_ACTION_SUCCESS', '%s %s items successfully ');
define( 'AMP_TEXT_LIST_ACTION_FAIL', 'Nothing was %s');
define( 'AMP_TEXT_CONTENT_RSS_ITEMS_ADDED', 'Received %s new items from %s');

/**
 * List explanatory text  
 */
define( 'AMP_TEXT_WITH_SELECTED', 'With Selected:&nbsp;');
define( 'AMP_TEXT_EDIT_ITEM', 'Edit this Item');
define( 'AMP_TEXT_EDIT', 'Edit');
define( 'AMP_TEXT_VIEW', 'View');
define( 'AMP_TEXT_LIST', 'List');
define( 'AMP_TEXT_ADD', 'Add');
define( 'AMP_TEXT_SEARCH', 'Search');

define( 'AMP_TEXT_ALL', 'All');
define( 'AMP_TEXT_PUBLISH', 'Publish');
define( 'AMP_TEXT_DELETED', 'Deleted');
define( 'AMP_TEXT_ADD_ITEM','Add new record' );
define( 'AMP_TEXT_PREVIEW_ITEM','Preview this Item' );
define( 'AMP_TEXT_SEARCH_NO_MATCHES', 'No items matched your search');
define( 'AMP_TEXT_LIST_CONFIRM_DELETE', 'Are you sure you want to DELETE these items?');

/**
 * RSS listpage text
 */
define( 'AMP_TEXT_VIEW_SOURCE', 'View Source');
define( 'AMP_TEXT_SOURCE', 'Source');
define( 'AMP_TEXT_SUBTITLE', 'Subtitle');
define( 'AMP_TEXT_CONTACTS', 'Contacts');
define( 'AMP_TEXT_PUBLISH_TO', AMP_TEXT_PUBLISH . ' To');

/**
 *  Labels for each different Sectional Listing Type 
 */
if (!  defined( 'AMP_TEXT_SECTIONLIST_ARTICLES'))
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES', 'List of general content in section' );

if (!  defined( 'AMP_TEXT_SECTIONLIST_DEFAULT'))
        define( 'AMP_TEXT_SECTIONLIST_DEFAULT', 'List of general content in section' );

if (!  defined( 'AMP_TEXT_SECTIONLIST_NEWSROOM'))
        define( 'AMP_TEXT_SECTIONLIST_NEWSROOM', 'Newsroom' );
        
if (!  defined( 'AMP_TEXT_SECTIONLIST_SUBSECTIONS'))
        define( 'AMP_TEXT_SECTIONLIST_SUBSECTIONS', 'List of subsections in current section' );

if (!  defined( 'AMP_TEXT_SECTIONLIST_SUBSECTIONS_PLUS_ARTICLES'))
        define( 'AMP_TEXT_SECTIONLIST_SUBSECTIONS_PLUS_ARTICLES', 'List of content and sections' );

if (!  defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_BY_SUBSECTION'))
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_BY_SUBSECTION', 'List of subsections and content in each subsection' );

if (!  defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_AGGREGATOR'))
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_AGGREGATOR', 'List of all content in all subsections' );

if (!  defined( 'AMP_TEXT_SECTIONLIST_SECTIONS_BY_SUBSECTION' ))
        define( 'AMP_TEXT_SECTIONLIST_SECTIONS_BY_SUBSECTION', 'List of subsections within each subsection' );

if (! defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_FEATURES_TEMPLATE')) 
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_FEATURES_TEMPLATE', 'List of %s content in section'  );

if (! defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_PLUS_CLASS_TEMPLATE' ))
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_PLUS_CLASS_TEMPLATE', 'Content in section plus all %s content' );

if ( !defined( 'AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT')) 
        define( 'AMP_TEXT_SYSTEM_LINK_NAV_LAYOUT_EDIT', 'Edit Navigation Layouts');
if ( !defined( 'AMP_TEXT_CONTENT_NAV_LAYOUT_HEADER')) 
        define( 'AMP_TEXT_CONTENT_NAV_LAYOUT_HEADER', 'Navigation Layout for %s: %s');
if ( !defined( 'AMP_TEXT_LIST_PAGES')) define('AMP_TEXT_LIST_PAGES', 'List Pages');
if ( !defined( 'AMP_TEXT_CONTENT_PAGES')) define( 'AMP_TEXT_CONTENT_PAGES', 'Content Pages');
if ( !defined( 'AMP_TEXT_PERMISSION_DENIED_LIST')) define( 'AMP_TEXT_PERMISSION_DENIED_LIST', 'You do not have permission to view this list');

/**
 * Descriptions for Tools 
 */

if ( !defined( 'AMP_TEXT_MODULE_NAME_GALLERY')) define( 'AMP_TEXT_MODULE_NAME_GALLERY', 'Photo Gallery');

/**
 * Login page
 */
if ( !defined( 'AMP_TEXT_LOGIN_HELP_ADMIN')) 
    define( 'AMP_TEXT_LOGIN_HELP_ADMIN', 'If you are having trouble logging in, please contact the <a href="mailto:%s">site administrator</a>.' );
/**
 * General purpose 
 * component names
 * system-wide values
 */
if ( !defined( 'AMP_TEXT_ACTION')) define( 'AMP_TEXT_ACTION', 'action');

if ( !defined( 'AMP_TEXT_SYSTEM_INTERFACE_FOOTER')) 
    define( 'AMP_TEXT_SYSTEM_INTERFACE_FOOTER', "AMP %s for %s \nPlease report problems to %s");

define( 'AMP_TEXT_SECTION', 'section');
define( 'AMP_TEXT_SECTION_LIST', 'section list');
define( 'AMP_TEXT_CLASS', 'class');
define( 'AMP_TEXT_PUBLIC_PAGE', 'public page');

define( 'AMP_TEXT_TEMPLATE', 'template');
define( 'AMP_TEXT_CACHE_RESET', 'The cache has been reset' );

/**
 * DIA related 
 */
if ( !defined( 'AMP_TEXT_DIA_SAVE_SUCCESS'))
        define( 'AMP_TEXT_DIA_SAVE_SUCCESS', 'Saved DIA supporter %s');
/**
 * Public Pages related 
 */
if ( !defined( 'AMP_TEXT_CONTENT_PUBLIC_NO_LINK'))
        define( 'AMP_TEXT_CONTENT_PUBLIC_NO_LINK', 
                'This page does not have a link auto-associated with it.  Please add a link below or the page will not link.');
        

?>
