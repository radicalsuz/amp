<?php

/* * * * * * * * * *
 * AMP Base Footer
 *
 * Send buffers to content Manager for processing
 * return final output
 *
 */

if (AMP_USE_OLD_CONTENT_ENGINE) {
    require_once( 'AMP/BaseFooter2.php' );
    exit;
}

require_once ('AMP/Content/Page.inc.php' );

$currentPage = AMPContent_Page::instance();
if (isset($modulefooter) && $modulefooter){
    $currentPage->addtoContentFooter( $modulefooter );
}

$currentPage->setContent( ob_get_clean() );

$displayType = AMP_CONTENT_PAGE_DISPLAY_DEFAULT;
if  (isset($_GET['printsafe']) && $_GET['printsafe'] == 1) $displayType = AMP_CONTENT_PAGE_DISPLAY_PRINTERSAFE ;
    
$final_page_html = $currentPage->output( $displayType );

print $final_page_html;

if (AMP_SITE_MEMCACHE_ON && isset($GLOBALS['cached_page'])) {
    $cached_page->save( $final_page_html );
}

#ob_end_flush();
?>
