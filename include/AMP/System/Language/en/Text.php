<?php

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

/**
 * List explanatory text  
 */
define( 'AMP_TEXT_WITH_SELECTED', 'With Selected:&nbsp;');

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
if ( !defined( 'AMP_TEXT_LIST_PAGES')) define('AMP_TEXT_LIST_PAGES', 'List Pages');
if ( !defined( 'AMP_TEXT_CONTENT_PAGES')) define( 'AMP_TEXT_CONTENT_PAGES', 'Content Pages');
if ( !defined( 'AMP_TEXT_PERMISSION_DENIED_LIST')) define( 'AMP_TEXT_PERMISSION_DENIED_LIST', 'You do not have permission to view this list');
?>
