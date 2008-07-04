<?php

/**
 *  Gallery Display Page
 *
 *  @version 3.5.7
 *  @author Austin Putman <austin@radicaldesigns.org>
 *  @module Content
 */
$modid =8;
$mod_id = 27 ;

include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");
require_once( 'Modules/Gallery/Gallery.php');

$currentPage = &AMPContent_Page::instance( );
$gallery_id = ( isset( $_GET['gal']) && $_GET['gal']) ? intval( $_GET['gal'] ) : false; 
$gallery_id = ( isset( $_GET['id']) && $_GET['id']) ? intval( $_GET['id'] ) : $gallery_id; 

if ( isset( $fullgal ) && ( 2 == $fullgal ) && !defined( 'AMP_GALLERY_DISPLAYTYPE')) define( 'AMP_GALLERY_DISPLAYTYPE' , 'Single');
if ( !defined( 'AMP_GALLERY_DISPLAYTYPE')) define( 'AMP_GALLERY_DISPLAYTYPE', 'Full');

$gallery = false;
if ( $gallery_id ) $gallery = &new Gallery( $dbcon, $gallery_id ) ;

if ( $gallery && $gallery->isLive( )){
    $currentPage->contentManager->addDisplay( $gallery->getDisplay( AMP_GALLERY_DISPLAYTYPE ));
} else {
    //require_once( 'Modules/Gallery/SetDisplay.inc.php');
    //$gallerySet = &new GallerySet( $dbcon );
    //$gallerySet->addCriteriaLive( );
    $gallery = &new Gallery( $dbcon );
    $set_display = 'Gallery_Display_Tree';
    require_once( 'Modules/Gallery/Display/Tree.php');
    if ( defined( 'AMP_RENDER_GALLERY_SET_DISPLAY') && class_exists( AMP_RENDER_GALLERY_SET_DISPLAY )) {
        $set_display = AMP_RENDER_GALLERY_SET_DISPLAY;
    }
    $display = &new $set_display( $gallery, array( 'live' => true ));
    $currentPage->contentManager->addDisplay( $display );
}

require_once( 'AMP/BaseFooter.php');

?>
