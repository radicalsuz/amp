<?php

/* * * * * * * * * *
 * AMP Base Footer
 */

require_once ('AMP/Content/Page.inc.php' );

$currentPage = AMPContent_Page::instance();
if (isset($modulefooter) && $modulefooter){
    $currentPage->addtoContentFooter( $modulefooter );
}

if (!isset($bodydata2)) $bodydata2 = "";
$currentPage->setContent( ob_get_clean().$bodydata2 );


if  (isset($_GET['printsafe']) && $_GET['printsafe'] == 1) {
    print $currentPage->output( AMP_CONTENT_PAGE_DISPLAY_PRINTERSAFE );
} else {
    print $currentPage->output();
}

@ob_end_flush();

?>
