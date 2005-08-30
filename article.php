<?php
/********************
 *  Article.php
 *
 *  Standard Content display page
 *
 *  2005-08-22 
 *  austin@radicaldesigns.org
 *  AMP 3.5.3
 *
 ******/

require_once("AMP/BaseDB.php");

if (!defined( 'AMP_USE_OLD_CONTENT_ENGINE' )) define ('AMP_USE_OLD_CONTENT_ENGINE', false );

if ( AMP_USE_OLD_CONTENT_ENGINE ) {
    require_once ( 'article2.php' );
} else {

    if (AMP_SITE_MEMCACHE_ON) {
        require_once( "AMP/Content/Page/Cached.inc.php" );
        $cached_page = &new AMPContent_Page_Cached();
        $cached_page->execute();
    }
    require_once("AMP/BaseTemplate.php");
    
    $currentPage = &AMPContent_Page::instance();

    $listType = $currentPage->isList();
    if ($listType && $listType != AMP_CONTENT_LISTTYPE_CLASS ) $isanarticle  = 1;


    if (isset($_GET["id"]) && !isset($currentPage->article)) ampredirect ( AMP_CONTENT_URL_SEARCH ); 
    if ($listType && $listType == AMP_CONTENT_LISTTYPE_CLASS ) {
        $currentPage->setSection( $currentPage->class->getSection());
    }
    if (!( $listType || $currentPage->isArticle() )) ampredirect( AMP_CONTENT_URL_FRONTPAGE );

    //set article or list inc
    if (isset($_GET['region']) && ($_GET['region'])) {
        $currentPage->setRegion( $_GET['region'] ); 
        $display = &new ArticleSet_Region_Display( $currentPage );
    }
    if ($listType) {
        if ($listType == AMP_CONTENT_LISTTYPE_SECTION ) {
            $display = &$currentPage->section->getDisplay();
        }
        if ($listType == AMP_CONTENT_LISTTYPE_CLASS ) {
            $display = &$currentPage->class->getDisplay();
        }
        if (isset($_GET['nointro']) && $_GET['nointro']==1) {
            $currentPage->contentManager->setListIntro( false );
        }
    } 

    if ( $currentPage->isArticle() ) {
        if (!(isset($_GET['preview']) || $currentPage->article->isLive())) ampredirect( AMP_CONTENT_URL_INDEX );
        if (isset($_GET['vid']) && $_GET['vid']) $currentPage->article->readVersion( $_GET['vid']);

        $display = $currentPage->article->getDisplay(); 
    }

    $currentPage->contentManager->addDisplay( $display );

    require_once("AMP/BaseFooter.php");
}

?>
