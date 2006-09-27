<?php

require_once( 'AMP/System/Tool/Control/ComponentMap.inc.php');
require_once( 'AMP/System/Page.inc.php');

$map = &new ComponentMap_ToolControl( );
$controller = &$map->get_controller( );

if ( ( array_search( $controller->get_action( ), array( 'add', 'edit')) === FALSE )  
        && $tool_id = $controller->assert_var( 'tool_id') ) {
    ampredirect( AMP_url_add_vars( AMP_SYSTEM_URL_TOOLS , "id=".$tool_id ));
}

print $controller->execute( );

/*

$systemPage = &new AMPSystem_Page( $dbcon, $map );
if (isset($_GET['action']) && $_GET['action'] == "list")  $systemPage->showList( true );

$tool_id = false;
if (isset( $_GET[ 'tool_id' ]) && intval($_GET[ 'tool_id' ]) ){
    $tool_id = intval( $_GET[ 'tool_id'] );
}

$systemPage->execute( );
if ( $tool_id && $systemPage->showList( )) ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_TOOLS , "id=".$tool_id ));
*/
/*
if ( $tool_id ) {
    print $systemPage->getAction( );
    ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_TOOLS , "id=".$tool_id ));
}
*/


#print $systemPage->output( );

?>
