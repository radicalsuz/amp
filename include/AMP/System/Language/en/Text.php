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
define( 'AMP_TEXT_DATA_RESTORE_SUCCESS', "%s has been restored." );
define( 'AMP_TEXT_DATA_COPY_SUCCESS',  "Your working copy was saved as %s");
define( 'AMP_TEXT_DATA_DELETE_SUCCESS',  "%s was deleted");
define( 'AMP_TEXT_DATA_DELETE_VERSION_SUCCESS',  "%s, version %s was deleted");

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
define( 'AMP_TEXT_ITEM_NAME', 'Item');
define( 'AMP_TEXT_SEARCH', 'Search');

define( 'AMP_TEXT_ALL', 'All');
if ( !defined( 'AMP_TEXT_PAGER_NEXT'))      define( 'AMP_TEXT_PAGER_NEXT', 'Next' );
if ( !defined( 'AMP_TEXT_PAGER_PREVIOUS'))  define( 'AMP_TEXT_PAGER_PREVIOUS', 'Prev' );
define( 'AMP_TEXT_PAGER_POSITION', 'Displaying %s of %s');
define( 'AMP_TEXT_PUBLISH', 'Publish');
define( 'AMP_TEXT_DELETED', 'Deleted');
define( 'AMP_TEXT_ADD_ITEM','Add new record' );
define( 'AMP_TEXT_PREVIEW_ITEM','Preview this Item' );
define( 'AMP_TEXT_SEARCH_NO_MATCHES', 'No items matched your search');
define( 'AMP_TEXT_LIST_CONFIRM_DELETE', 'Are you sure you want to DELETE these items?');
define( 'AMP_TEXT_LIST_CONFIRM_RECALCULATE_IMAGES', 'Recalculating image sizes may take several minutes.\n  Please do not interfere with your browser during this time.\n  Are you sure you want to continue?');

define( 'AMP_TEXT_CROP', 'Crop');
define( 'AMP_TEXT_RECALCULATE', 'Recalculate');
define( 'AMP_TEXT_PREVIEW', 'Preview');
define( 'AMP_TEXT_CANCEL', 'Cancel');
define( 'AMP_TEXT_SIZE', 'Size');
define( 'AMP_TEXT_WIDTH', 'Width');
define( 'AMP_TEXT_HEIGHT', 'Height');
define( 'AMP_TEXT_ALL_IMAGE_SIZES', 'All Image Sizes');
define( 'AMP_TEXT_SELECT_NEW_WIDTHS_FOR', 'Select new widths for');
define( 'AMP_TEXT_GALLERY', 'Gallery');
define( 'AMP_TEXT_SELECT', 'Select %s');
define( 'AMP_TEXT_ACTION_NOTICE', 'Performing %s on %s');

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
define( 'AMP_TEXT_ARTICLE', 'article');
define( 'AMP_TEXT_PUBLIC_PAGE', 'public page');

define( 'AMP_TEXT_TEMPLATE', 'template');
define( 'AMP_TEXT_CONTENT_MAP_HEADING', 'Site Map');
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
        
/**
 * Content Page Names for tools
 */
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_SITEMAP' ))      define ( 'AMP_TEXT_CONTENT_PAGE_SITEMAP',   'Site Map' );

if ( !defined( 'AMP_TEXT_CONTENT_PAGE_SEARCH' ))      define ( 'AMP_TEXT_CONTENT_PAGE_SEARCH',   'Search' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_ARTICLE_INPUT' ))      define ( 'AMP_TEXT_CONTENT_PAGE_ARTICLE_INPUT',   'Submit Content' );

if ( !defined( 'AMP_TEXT_CONTENT_PAGE_GALLERY' ))      define ( 'AMP_TEXT_CONTENT_PAGE_GALLERY',   'Photo Galleries' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_ACTION' ))      define ( 'AMP_TEXT_CONTENT_PAGE_ACTION',   'Action Center' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_VOLUNTEERS' ))      define ( 'AMP_TEXT_CONTENT_PAGE_VOLUNTEERS',   'Add Volunteer' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_GROUPS' ))      define ( 'AMP_TEXT_CONTENT_PAGE_GROUPS',   'Groups Display' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_GROUP_ADD' ))      define ( 'AMP_TEXT_CONTENT_PAGE_GROUP_ADD',   'Add Group' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_ENDORSER_LIST' ))      define ( 'AMP_TEXT_CONTENT_PAGE_ENDORSER_LIST',   'Endorser Display' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_ENDORSER_ADD' ))      define ( 'AMP_TEXT_CONTENT_PAGE_ENDORSER_ADD',   'Add Endorser' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_EMAIL_ADD' ))      define ( 'AMP_TEXT_CONTENT_PAGE_EMAIL_ADD',   'Email Signup' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_MEDIA_ADD' ))      define ( 'AMP_TEXT_CONTENT_PAGE_MEDIA_ADD',   'Media Sign In' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_TELL_A_FRIEND' ))      define ( 'AMP_TEXT_CONTENT_PAGE_TELL_A_FRIEND',   'Tell A Friend' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_SPEAKER_LIST' ))      define ( 'AMP_TEXT_CONTENT_PAGE_SPEAKER_LIST',   'Speakers Display' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_SPEAKER_ADD' ))      define ( 'AMP_TEXT_CONTENT_PAGE_SPEAKER_ADD',   'Add Speakers' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_TRAINER_LIST' ))      define ( 'AMP_TEXT_CONTENT_PAGE_TRAINER_LIST',   'Trainers Display' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_TRAINER_ADD' ))      define ( 'AMP_TEXT_CONTENT_PAGE_TRAINER_ADD',   'Add Trainers' );

if ( !defined( 'AMP_TEXT_CONTENT_PAGE_EVENT_LIST'))    define ( 'AMP_TEXT_CONTENT_PAGE_EVENT_LIST',       'Calendar' );
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_EVENT_ADD'))    define ( 'AMP_TEXT_CONTENT_PAGE_EVENT_ADD',       'Add Event' );

if ( !defined( 'AMP_TEXT_CONTENT_PAGE_PETITIONS'))    define ( 'AMP_TEXT_CONTENT_PAGE_PETITIONS',       'Petitions');
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_FAQ'))    define ( 'AMP_TEXT_CONTENT_PAGE_FAQ',       'FAQ');
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_BOARD_HOUSING'))    define ( 'AMP_TEXT_CONTENT_PAGE_BOARD_HOUSING',       'Housing Board');
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_BOARD_RIDE'))    define ( 'AMP_TEXT_CONTENT_PAGE_BOARD_RIDE',       'Ride Board');
if ( !defined( 'AMP_TEXT_CONTENT_PAGE_LINKS'))    define ( 'AMP_TEXT_CONTENT_PAGE_LINKS',       'Links' );

/**
 *  Article Related
 */
define( 'AMP_TEXT_DOCUMENT_INFO', 'Document Info' );
define( 'AMP_TEXT_DATE_CREATED', 'Date Created' );
define( 'AMP_TEXT_DATE_UPDATED', 'Date Updated' );
define( 'AMP_TEXT_CREATED', 'Created');
define( 'AMP_TEXT_UPDATED', 'Updated');
define( 'AMP_TEXT_CURRENT_ACTION', '%sing %s');
define( 'AMP_TEXT_BY', 'by' );
define( 'AMP_TEXT_ID', 'ID' );
define( 'AMP_TEXT_REDIRECTED_TO', 'Redirected To');
define( 'AMP_TEXT_ALIAS', 'Alias');
define( 'AMP_TEXT_ATTACHED_FILE', 'Attached File');
define( 'AMP_TEXT_VIEW_ALL', 'View All %s');
define( 'AMP_TEXT_VERSION_ID', 'Archived Content - Version ID: %s');

define( 'AMP_TEXT_IMAGE_CLASS_THUMB', 'thumbnail' );
define( 'AMP_TEXT_IMAGE_CLASS_OPTIMIZED_TALL', 'tall image' );
define( 'AMP_TEXT_IMAGE_CLASS_OPTIMIZED_WIDE', 'wide image' );

?>
