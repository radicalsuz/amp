<?php

/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/
$mod_name='udm';
require_once( 'AMP/UserData/Set.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataSet( $dbcon, $_REQUEST[ 'modin' ] );

$modidselect=$dbcon->Execute("SELECT id, perid from modules where userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");
$modin_permission=$modidselect->Fields("perid");


//Accept URL values for editlink and sortby options
if (isset($_GET['editlink'])) { $options['editlink_action']=$_GET['editlink'];
} else { $options=array();}
if (isset($_GET['sortby'])) { $options['sort_by']=$_GET['sortby'].", First_Name, Last_Name";
}


if ($userper[53]&&$userper[$modin_permission]) { 
	$udm->admin = true;
	$options['allow_publish']=true;
	$udm->authorized = true;
	$options['allow_edit']=true;
	$options['allow_export']=true;
	$options['allow_include_modins']=true;
	$options['allowed_modins']="*";
} elseif ($userper[54]&&$userper[$modin_permission]) {
	$udm->authorized = true;
	$options['allow_edit']=false;
	$options['allow_publish']=false;
} else {
	$udm->authorized=false;
}




// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit'])) ? $_REQUEST['btnUdmSubmit'] : false;

// Fetch or save user data.
if ( $sub ) {

    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
   # $udm->saveUser();

} elseif ( !$sub && $uid ) {

    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
    #$udm->submitted = false;
    #$udm->getUser( $uid ); 

}

/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $udm object, or from print() or echo() statements.

   By default, the form will include AMP's base template code,
   and any database-backed intro text to the appropriate module.

*/

$mod_id = $udm->modTemplateID;

require_once( 'header.php' );

print "<h2>View/Edit " . $udm->name . "</h2>";


$format="userlist_html";




	if (isset($udm->plugins['userlist_html'])) {
		$output=$udm->doAction("userlist_html", $options);
	} else {
		$udm->registerPlugin("Output", "userlist_html");
		$output=$udm->doAction("userlist_html", $options);
	}
	print $output;


// Append the footer and clean up.
require_once( 'footer.php' );

?>
