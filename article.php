<?php
/********************
 *  article.php
 *
 *  Standard Content display page for lists and articles
 *
 *  @author Austin Putman <austin@radicaldesigns.org>
 *  @version AMP 3.5.3
 *  @copyright Radical Designs 2005, released under GPL 2+
 *  @package AMP::Content
 *
 ******/

require_once("AMP/BaseDB.php");

if (!defined( 'AMP_USE_OLD_CONTENT_ENGINE' )) define ('AMP_USE_OLD_CONTENT_ENGINE', false );

if ( AMP_USE_OLD_CONTENT_ENGINE ) {
    require_once ( 'article2.php' );
} else {

    #################################
    ###  Check for a cached page  ###
    #################################

    if (AMP_SITE_MEMCACHE_ON) {
        require_once( "AMP/Content/Page/Cached.inc.php" );
        $cached_page = &new AMPContent_Page_Cached();
        if ($cached_page->execute()) exit;
        
    }

    #################################
    ###  Load the Template  ###
    #################################

    require_once("AMP/BaseTemplate.php");
    $currentPage = &AMPContent_Page::instance();
    $listType = $currentPage->isList();

    
    //************************/
    // before redirecting, check if a valid Region has been passed
    if (!($listType || $currentPage->isArticle()) && $currentPage->isRegion() ) {
        $listType = AMP_CONTENT_LISTTYPE_REGION;
        $currentPage->setListType( $listType );
    }

    //************************/
    // Redirect to Search for invalid page types
    // No Valid List or Article
    if (!( $currentPage->getListSource()  || $currentPage->isArticle() ))   ampredirect ( AMP_CONTENT_URL_SEARCH );

    // Article is Not Live, Preview Mode is Not on
    if ( !AMP_DISPLAYMODE_PREVIEW ) {    
        if ($currentPage->isArticle() && (!$currentPage->article->isLive() )) ampredirect( AMP_CONTENT_URL_SEARCH );
    }
    // Section List page has a redirect in the SectionHeader
    if ( $listType == AMP_CONTENT_LISTTYPE_SECTION ) {
        if ( $target = $currentPage->section->getHeaderRedirect() ) ampredirect( $target );
    }

    //************************/
    // check if section criteria exists for class lists
    if ($listType == AMP_CONTENT_LISTTYPE_CLASS) {
        if ( isset( $_GET['type']) && isset( $currentPage->section ) && isset( $currentPage->class ) ) {
            $currentPage->class->addContentsCriteria( 
                $currentPage->section->getCriteriaForContent() );
        }
    }

    // get Listing Display
    if ($listType) {
        $show_intro =  !(isset($_GET['nointro']) && $_GET['nointro']==1); 
        $display = &$currentPage->getListDisplay( $show_intro );
    } 

    //************************/
    // get Article Display
    if ( $currentPage->isArticle() ) {
        if (isset($_GET['vid']) && $_GET['vid']) $currentPage->article->readVersion( $_GET['vid']);
        $display = $currentPage->article->getDisplay(); 
    }


    $currentPage->contentManager->addDisplay( $display );

    require_once("AMP/BaseFooter.php");
}

?>
