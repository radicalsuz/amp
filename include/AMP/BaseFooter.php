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
/*
if ( AMP_SITE_MEMCACHE_ON 
    && isset($GLOBALS['cached_page']) 
    && empty( $_POST) 
    && ( ! $currentPage->isRedirected()) 
    && ( ! AMP_Authenticate( 'content' )) 
    && ( ! defined( 'AMP_SYSTEM_FLASH_OUTPUT')) 
    ){
        $cached_page->save( $finalPageHtml );
}
*/

if ( AMP_is_cacheable_url( ) ) {
    $cache_key = AMP_CACHE_TOKEN_URL_CONTENT . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
    $user_id =  ( defined( 'AMP_SYSTEM_USER_ID' ) && AMP_SYSTEM_USER_ID ) ? AMP_SYSTEM_USER_ID : null; 
    AMP_cache_set( $cache_key, $finalPageHtml, $user_id );
}


?>
