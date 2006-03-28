<?php
if (file_exists_incpath( 'custom.sources.inc.php' )) include_once( 'custom.sources.inc.php' );

$class_names = &AMPContent_Lookup::instance( 'class' );
if ((! defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_FEATURES')) && defined( 'AMP_CONTENT_CLASS_FEATURE' ) )
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_FEATURES', 
            sprintf( AMP_TEXT_SECTIONLIST_ARTICLES_FEATURES_TEMPLATE, $class_names[AMP_CONTENT_CLASS_FEATURE] ));

if (  !defined( 'AMP_TEXT_SECTIONLIST_ARTICLES_PLUS_CLASS' ) && defined( 'AMP_CONTENT_SECTION_PLUS_CLASS' ) ) 
        define( 'AMP_TEXT_SECTIONLIST_ARTICLES_PLUS_CLASS', 
            sprintf(  AMP_TEXT_SECTIONLIST_ARTICLES_PLUS_CLASS_TEMPLATE, $class_names[ AMP_CONTENT_SECTION_PLUS_CLASS ] ));

?>
