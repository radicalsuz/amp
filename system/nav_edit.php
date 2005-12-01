<?php

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

?>
