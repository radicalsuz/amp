<?php

require_once( 'AMP/System/Base.php');
$url = AMP_SYSTEM_URL_PUBLIC_PAGE_ADD;

if ( isset( $_GET['id']) && $_GET['id']) {
    $url = AMP_url_add_vars( AMP_SYSTEM_URL_PUBLIC_PAGE, array( 'id='. $_GET['id']));
}
if ( isset( $_GET['tool_id']) && $_GET['tool_id']) {
    $url = AMP_url_add_vars( AMP_SYSTEM_URL_PUBLIC_PAGE_ADD, array( 'tool_id='. $_GET['tool_id']));
}
ampredirect( $url );
/*
require_once( 'AMP/Content/Nav/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_Nav( );
$systemPage = &new AMPSystem_Page( $dbcon, $map );
if (isset($_GET['action']) && $_GET['action'] == "list")  $systemPage->showList( true );

// allow default to link to referring module
if (isset( $_GET[ 'tool_id' ]) && ($tool_id = $_GET[ 'tool_id' ])) {
    $systemPage->addCallback( 'form', 'setDefaultValue', array( 'modid', $tool_id ));
}

$systemPage->execute( );
print $systemPage->output( );
*/
?>
