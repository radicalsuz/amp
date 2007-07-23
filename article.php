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
 * Check for a cached copy of this request
 */
if ( $cached_output = AMP_cached_request( )) {
    print $cached_output;
    exit;
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
if (!( $listType  || $currentPage->isArticle() )) {
    AMP_make_404( );
}

/**
 * Redirect to Search Page for unpublished articles unless in preview mode
 */
if ( ( !AMP_DISPLAYMODE_PREVIEW )  && ($currentArticle = &$currentPage->getArticle()) ){

    if (!$currentArticle->isLive() ) AMP_make_404( ) ;

    //check to make sure parent section is live
    require_once( 'AMP/Content/Section.inc.php' );
    $currentSection = new Section( AMP_Registry::getDbcon(  ), $currentArticle->getParent(  ) );
    if ( !$currentSection->hasData(  ) || !$currentSection->isLive( )) {
        if ( $currentSection->id != 1 ) AMP_make_404( );
    }

}


/**
 * Check if specified article is a section header and redirect to that section
 */
if ( $currentArticle = &$currentPage->getArticle() ){
    if ( AMP_CONTENT_REDIRECT_SECTIONHEADERS_TO_SECTIONS && ( $currentArticle->getClass(  ) == AMP_CONTENT_CLASS_SECTIONHEADER )) {
        require_once( 'AMP/Content/Section.inc.php' );
        $currentSection = new Section( AMP_Registry::getDbcon(  ), $currentArticle->getParent(  ) );
        if ( $currentSection->hasData(  ) ) {
            ampredirect( $currentSection->getURL(  ) );
        }
    }
}

/**
 * Check Section List pages for a redirect in the SectionHeader 
 */
if ( $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION ) 
    && ($currentSection = &$currentPage->getSection() )) {
    if ( !$currentSection->isLive( )) AMP_make_404( );
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
    $display = &$currentPage->getListDisplay();
    if ( $display ){

        $filter = false;
        //assign a default filter for section list pages
        if ( $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION )) {
            $section = &$currentPage->getSection( );
            $filter = $section->getData( 'filter' );
        }

        //check for a requested filter, override the default if one exists
        if ( isset( $_GET['filter']) && $_GET['filter']) {
            $filter = $_GET['filter'] ;
        }
        if ( $filter ) $display->addFilter( $filter );

        // hide list intro page if nointro $_GET var is set
        $show_intro =  !(isset($_GET['nointro']) && $_GET['nointro']==1); 
        if ( method_exists( $display, 'setListIntro') ) $display->setListIntro( $show_intro );

        //filter by tag(s) if specified
        if ( isset( $_GET['tag']) && $_GET['tag']) {
            $tag_filter = $_GET['tag'];
            if ( !is_array( $tag_filter )) {
                $tag_filter = array( $tag_filter );
            }
            foreach( $tag_filter as $single_tag ) {
                $display->addFilter( 'tag', $single_tag );
            }
        }

        if ( isset( $_GET['date']) && $_GET['date']) {
            $date_value = $_GET['date'];
            $display->addFilter( 'date', $date_value );
        }

    } else {
        AMP_make_404( );
    }
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
