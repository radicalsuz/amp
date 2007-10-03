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
 */
$finalPageHtml = $currentPage->output( $displayType );
print $finalPageHtml;

AMP_cache_this_request( $finalPageHtml );

AMP_cache_close( );

?>
