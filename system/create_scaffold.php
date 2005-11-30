<?php

require_once( 'AMP/System/Base.php');
require_once ( 'AMP/System/Page/Display.inc.php');
require_once( 'AMP/System/Scaffold/Factory.inc.php');

$engine = &new AMPScaffold_Factory( $dbcon );
$form = &$engine->buildForm( );
$form_values = $form->getValues( );
$result_message = "";

if ( $form->submitted( )){
    $engine->setScaffoldItem( $form_values['new_scaffold'] );
    $engine->setDataTable( $form_values['sourcetable']);
    $engine->setScaffoldItemType( $form_values['new_itype'] );
    $engine->setNameField( $form_values['new_namefield']);
    $engine->execute( );
    if ( !$message = $engine->getErrors( )) ampredirect( '/system/'.$engine->getControllerPage( ));
    else $result_message = "<span class='page_error'>$message</div>";
}

include ("header.php");
echo AMPSystem_Page_Display::pagetitle( 'New Scaffold', 'Add');
echo $result_message;
echo $form->output();
include ("footer.php");
?>
