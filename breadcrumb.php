<?php

require_once( 'AMP/Content/Map/Breadcrumb.inc.php' );

$breadcrumb = &AMP_Breadcrumb_Content::instance();
$urlvars = AMP_URL_Read();
$reg = &AMP_Registry::instance();
$intro_id = $reg->getEntry( AMP_REGISTRY_CONTENT_INTRO_ID );

if (isset($urlvars['list']) && $urlvars['list']== AMP_CONTENT_LISTTYPE_CLASS ) {
    $breadcrumb->findClass( $urlvars[ AMP_CONTENT_LISTTYPE_CLASS ] );
}
if (isset($urlvars['id']) && $urlvars['id'] && (!isset($urlvars['list'])) && (strpos( $_SERVER['PHP_SELF'], 'article.php')!==FALSE) ) {
    $breadcrumb->findArticle( $urlvars[ 'id' ] );
}
if (isset($urlvars['list']) && $urlvars['list']== AMP_CONTENT_LISTTYPE_SECTION ) {
    $breadcrumb->findSection( $urlvars[ AMP_CONTENT_LISTTYPE_SECTION ] );
}
if (strpos($_SERVER['PHP_SELF'], 'article.php')===FALSE && isset( $intro_id ) && ($intro_id !== 1)) {
    $breadcrumb->findIntroText( $intro_id );
}
if (isset($urlvars['template_section']) && $urlvars['template_section'] ) {
    $breadcrumb->findSection( $urlvars[ 'template_section' ] );
}

//this guard clause is a temporary measure until breadcrumb is reliably called
//by the template
if ( !isset( $avoid_printing_breadcrumb ) ) {
    print $breadcrumb->execute();
}
?>
