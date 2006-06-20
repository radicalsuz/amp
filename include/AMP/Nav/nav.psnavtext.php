<?php
require_once( 'AMP/Content/Page.inc.php');
$currentPage = & AMPContent_Page::instance( );
if ( $article = & $currentPage->getArticle( ) ) {
    if ( $nav_data = $article->getSidebar( ) )  {
        print converttext( $nav_data );
    }
}
?>
