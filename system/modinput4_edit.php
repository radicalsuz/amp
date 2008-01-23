<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

//ob_start();
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'utility.functions.inc.php');

require("Connections/freedomrising.php");

$modin = $form_id_nav = $_REQUEST['modin'];

$tool_set = &AMPSystem_Lookup::instance( 'ToolsbyForm');
$modid = isset( $tool_set[$modin]) ? $tool_set[$modin] : null;
/*
$modidselect = $dbcon->Execute("SELECT id from modules where userdatamodid=" . $dbcon->qstr( $modin ) )
                    or die( "Couldn't get module information: " . $dbcon->ErrorMsg() );

$modid = $modidselect->Fields("id");
                   */ 

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $modin, true );
$udm->doPlugin( "QuickForm", "BuildAdmin" );
$mod_id = $udm->modTemplateID;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit']) && $_REQUEST['btnUdmSubmit']);

// Fetch or save user data.
if ( $sub ) {
    $udm->doPlugin( 'AMPsystem', 'Save' );
    ampredirect( AMP_url_update( $_SERVER['PHP_SELF'], array( 'modin' => $modin )));
}

/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $udm object, or from print() or echo() statements.

   By default, the form will include AMP's base template code,
   and any database-backed intro text to the appropriate module.

*/

include("header.php"); 

print "<h2>Add/Edit " . $udm->name . " Form</h2>";
print $udm->output();

include("footer.php"); 

?>
