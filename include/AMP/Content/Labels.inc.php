<?php
if (file_exists_incpath( 'custom.sources.inc.php' )) include_once( 'custom.sources.inc.php' );

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

$class_names = &AMPContent_Lookup::instance( 'class' );
if ((! defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_FEATURES')) && defined( 'AMP_CONTENT_CLASS_FEATURE' ) )
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_FEATURES', 'List of '.$class_names[AMP_CONTENT_CLASS_FEATURE].' content in section'  );

if (  !defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_PLUS_CLASS' ) && defined( 'AMP_CONTENT_SECTION_PLUS_CLASS' ) ) 
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_PLUS_CLASS', 'Content in section plus all '.$class_names[ AMP_CONTENT_SECTION_PLUS_CLASS ].' content' );

if (  !defined( 'AMP_TEXT_SECTIONLIST_SECTIONS_BY_SUBSECTION' ))
        define( 'AMP_TEXT_SECTIONLIST_SECTIONS_BY_SUBSECTION', 'List of subsections within each subsection' );
?>
