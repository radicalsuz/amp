<?php
/**
 *  article.php
 *
 *  Standard Content display page for lists and articles
 *
 *  @author Austin Putman <austin@radicaldesigns.org>
 *  @version AMP 3.5.3
 *  @copyright Radical Designs 2005
 *  @license released under GPL 2+
 *  @package Content
 *
 */

require_once("AMP/BaseDB.php");

/**
 *  Check for a cached page
 */
if (AMP_SITE_MEMCACHE_ON) {
    require_once( "AMP/Content/Page/Cached.inc.php" );
    $cached_page = &new AMPContent_Page_Cached();
    if ($cached_page->execute()) exit;
    
}

/**
 *  Initialize the Page
 */
require_once("AMP/BaseTemplate.php");
$currentPage = &AMPContent_Page::instance();
$listType = $currentPage->isList();


/**
 * If no List or Article is set, check if a valid Region has been passed 
 */
if (!($listType || $currentPage->isArticle()) && $currentPage->isRegion() ) {
    $listType = AMP_CONTENT_LISTTYPE_REGION;
    $currentPage->setListType( $listType );
}

/**
 * Redirect to Search Page for invalid page types, i.e. no valid List or Article
 */
if (!( $listType  || $currentPage->isArticle() ))   ampredirect ( AMP_CONTENT_URL_SEARCH );

/**
 * Redirect to Search Page for unpublished articles unless in preview mode
 */
if (!( $listType  || $currentPage->isArticle() ))   ampredirect ( AMP_CONTENT_URL_SEARCH );
if ( ( !AMP_DISPLAYMODE_PREVIEW )  && ($currentArticle = &$currentPage->getArticle()) ){

    if (!$currentArticle->isLive() ) ampredirect( AMP_CONTENT_URL_SEARCH );
}

/**
 * Check Section List pages for a redirect in the SectionHeader 
 */
if ( $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION ) 
    && ($currentSection = &$currentPage->getSection() )) {
    if ( $target = $currentSection->getHeaderRedirect() ) ampredirect( $target );
}

/**
 * Check if a section was specified for class lists 
 */
if ($currentPage->isList( AMP_CONTENT_LISTTYPE_CLASS ) 
    &&  isset( $_GET['type']) 
    && ($currentSection = &$currentPage->getSection() ) 
    && ($currentClass = &$currentPage->getClass() )) {
            #$currentClass->addContentsCriteria( $currentSection->getCriteriaForContent() );
            $currentClass->addContentsCriteriaSection( $currentSection->id );
}

/**
 * Get Listing Display  
 */
if ($listType) {
    $show_intro =  !(isset($_GET['nointro']) && $_GET['nointro']==1); 
    $filter = ( isset( $_GET['filter']) && $_GET['filter'])? $_GET['filter'] : false;
    $display = &$currentPage->getListDisplay();
    if ( $filter ) $display->addFilter( $filter );
    if ( method_exists( $display, 'setListIntro') ) $display->setListIntro( $show_intro );
} 

/**
 * Get Article Display 
 */
if ( $currentArticle = &$currentPage->getArticle() ) {
    if (isset($_GET['vid']) && $_GET['vid']) $currentArticle->readVersion( $_GET['vid']);
    $display = $currentArticle->getDisplay(); 
}


/**
 * Add the display to the Content Manager 
 * 
 */
$currentPage->contentManager->addDisplay( $display );

require_once("AMP/BaseFooter.php");

?>
