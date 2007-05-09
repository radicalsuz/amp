<?php
require_once( 'AMP/Content/Page.inc.php');
$currentPage = & AMPContent_Page::instance( );
$nav_data = false;
if ( $article = & $currentPage->getArticle( ) ) {
    $nav_data = $article->getSidebar( );
}
if ( $currentPage->isList( AMP_CONTENT_LISTTYPE_SECTION ) 
     && ( $section = $currentPage->getSection( ))
     && ( $header = $section->getHeaderRef( )) 
     ) {
     $nav_data = $header->getSidebar( ) ;
}
if ( $nav_data ) {
    print converttext( $nav_data );
}
?>
