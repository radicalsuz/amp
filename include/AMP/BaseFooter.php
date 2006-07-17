<?php

/**
 * Determine the status of the current Page and send the correct output
 *
 * @package Content 
 * @author Austin Putman <austin@radicaldesigns.org
 * @copyright Radical Designs 2005
 * @version 3.5.3
 * @since 2.0
 */

require_once ('AMP/Content/Page.inc.php' );

$currentPage = &AMPContent_Page::instance();

if ($currentPage->isRedirected() ){
    ob_end_flush();
    exit;
}

if ( $buffer_contents = ob_get_clean() ) AMP_directDisplay( $buffer_contents, AMP_CONTENT_DISPLAY_KEY_BUFFER );

/**
 * displayType controls the formatting of the Page output  
 *
 * @var string
 */
$displayType = AMP_CONTENT_PAGE_DISPLAY_DEFAULT;
if  (isset($_GET['printsafe']) && $_GET['printsafe'] == 1) $displayType = AMP_CONTENT_PAGE_DISPLAY_PRINTERSAFE ;
    
/**
 * finalPageHtml is the complete representation of the Page  
 *
 * @var string
 */
$finalPageHtml = $currentPage->output( $displayType );
print $finalPageHtml;

/**
 *  GLOBALS['cached_page'] is a signal that some part of the script checked the Memcache
 *
 *  @var    AMPContentPage_Cached
 */
if ( AMP_SITE_MEMCACHE_ON 
    && isset($GLOBALS['cached_page']) 
    && empty( $_POST) 
    && ( ! $currentPage->isRedirected()) 
    && ( ! AMP_Authenticate( 'content' )) 
    && ( ! defined( 'AMP_SYSTEM_FLASH_OUTPUT')) 
    ){
        $cached_page->save( $finalPageHtml );
}
/*
if ( $cache = &AMP_get_cache( ) && AMP_is_cacheable_url( ) ) {
    $cache_key = $_SERVER['REQUEST_URI'];
    if ( AMP_SYSTEM_USER_ID ) {
        $cache_key = $cache->identify( $_SERVER['REQUEST_URI']);
    }
    $cache->add( $finalPageHtml, $cache_key );
}
*/

?>
