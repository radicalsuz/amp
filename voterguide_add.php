<?php 
/*****
 *
 * AMP UserData Form View
 *
 * (c) 2004 Radical Designs
 * Written by David Taylor, david@radicaldesigns.org
 *
 *****/

//ob_start();

// Set default module.
$mod_id = 57;
$modid = 1;

require_once( 'AMP/UserDataInput.php' );
require_once( 'AMP/BaseDB.php' );


// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ] );

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

// Was data submitted via the web?
$sub = isset($_REQUEST['btnUdmSubmit']);

// Check fo rduplicates, setting $uid if found.
if ( !$uid ) {

	//$uid = $udm->findDuplicates();

} 
// Check for authentication, sending authentication mail if necessary.
if ( $uid ) {

    // Set authentication token if uid present
    $auth = $udm->authenticate( $uid, $otp );

}

// Fetch or save user data.
if ( ( !$uid || $auth ) && $sub ) {

    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
 	
    $udm->saveUser();
		if ($_POST['can']) {
		$i=0;
		if (!$uid) {
			$guide_id =$dbcon->Insert_ID();
		} else {$guide_id =$uid;}
		//$del_guide=$dbcon->Execute("delete from voterguide where $guide_id = $guide_id"); 

		while ($i <= 40){ 
			if ($_POST['can'][$i]) {
				$guide_sql="INSERT INTO voterguide (item,reason,position,guide_id) VALUES ('".addslashes($_POST['can'][$i])."','".addslashes($_POST['reason'][$i])."','".addslashes($_POST['stance'][$i])."','$guide_id')";
				//DIE( $guide_sql);
				$add_guide=$dbcon->Execute($guide_sql) or DIE("Could not insert guide record".$dbcon->ErrorMsg());
			}
			$i++;
		}		
	}




} elseif ( $uid && $auth && !$sub ) {

    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
    $udm->getUser( $uid ); 
	$my_slate_sql="Select * from voterguide where guide_id=".$uid;
	$my_slate = $dbcon->Execute($my_slate_sql) or DIE("Could not get guide record".$dbcon->ErrorMsg());
	$i = 0;
	while (!$my_slate->EOF){
		$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item (e.g. "John Kerry for President" or "Prop 66")</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" value="'.$my_slate->Fields("item").'" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Your Position</b></td>
		<td valign="top" align="left" class="form_data_col"><select name="stance['.$i.']">
	<option >'.$my_slate->Fields("position").'</option>

	<option value="Yes">Yes</option>
	<option value="No"> No</option>
	<option value="Hell Yeah"> Hell Yeah</option>
	<option value="No Way"> No Way</option>
	<option value="No Endorsement">No Endorsement</option>
</select></td>
	</tr>

	<tr>
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Why?</b><br><textarea name="reason['.$i.']" cols="65">'.$my_slate->Fields("reason").'</textarea></td></tr></table></td>
	</tr>
	<tr>';
		$my_slate->MoveNext();
		$i++;
	}



}

if (!$slate_html) {
	$i = 0;
	while ($i <= 40){
		$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item (e.g. "John Kerry for President" or "Prop 66")</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Your Position</b></td>
		<td valign="top" align="left" class="form_data_col"><select name="stance['.$i.']">
	<option value="">Select one</option>

	<option value="Yes">Yes</option>
	<option value="No"> No</option>
	<option value="Hell Yeah"> Hell Yeah</option>
	<option value="No Way"> No Way</option>
	<option value="No Endorsement">No Endorsement</option>
</select></td>
	</tr>

	<tr>
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Why?</b><br><textarea name="reason['.$i.']" cols="45"></textarea></td></tr></table></td>
	</tr>
	<tr>';
		$i++;
	}
}

$insert_html = $slate_html;

	 
/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $udm object, or from print() or echo() statements.

   By default, the form will include AMP's base template code,
   and any database-backed intro text to the appropriate module.

*/


$mod_id = $udm->modTemplateID;

require_once( 'AMP/BaseTemplate.php' );
require_once( 'includes/moduleintro.php' );

$udm->showForm = true;
$volform= $udm->output();
$submitspot=strpos($volform, "input name=\"btnUdmSubmit\"");
$insertpoint = strpos(substr($volform, $submitspot-200, 200), "<tr>");
$form_footer=substr($volform, $submitspot-200+$insertpoint);
$volform = substr($volform, 0, $submitspot-200+$insertpoint);


#print "<center><table width='400'><tr><td>"
print $volform;
print $insert_html;
print $form_footer;
#print "</td></tr></table></center>";

#print $debug_html;

// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );
?>