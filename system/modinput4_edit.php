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

$modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=$modin") or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ], true );
$udm->doPlugin( "QuickForm", "build_admin" );
$mod_id = $udm->modTemplateID;

// Was data submitted via the web?
$sub = isset($_REQUEST['btnUdmSubmit']);


// Fetch or save user data.
if ( $sub ) {
    $udm->doPlugin( 'AMP', 'save_admin' );
}

//A shortcut for copying UDM, modules, permissions, etc
$copy_button="<script type=\"text/javascript\">
function getCopyName () {
	var copyname=prompt('Please enter a name for the new module:');
	var copyform=document.forms['".$udm->name."'];	
	if (copyname> '' ) { //do nothing if no search name is entered
		if (copyname==copyform.elements['core_name'].value) {
			alert ('Please choose a different name to prevent confusion.');
		} else {
			copyform.action='modinput4_copy.php';
			copyform.elements['core_name'].value=copyname; 
			copyform.submit();
		}
	} else return false;
}
</script>
<table width=\"100%\"><TR><TD align=\"right\">
<form name=\"copy_structure\" action=\"modinput4_copy.php?modin=".$udm->instance."\" method=\"POST\"><input name=\"UDM_copy_name\" type=\"hidden\" value=''>
<span style=\"text-align:right\"><a href=\"#\" onClick=\"getCopyName();\">Copy this Module</A></span></form></td></tr></table>"; 



/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $udm object, or from print() or echo() statements.

   By default, the form will include AMP's base template code,
   and any database-backed intro text to the appropriate module.

*/

include("header.php"); 

print "<h2>Add/Edit " . $udm->name . "</h2>";
print $udm->output();
print $copy_button;


include("footer.php"); 

?>