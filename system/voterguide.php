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


require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );
if (!isset($_GET[modin])){ $modin=52;} else {$modin=$_GET[modin];}


// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $modin);
$udm->admin = true;
$udm->authorized = true;

$modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
$udm->uid = $uid;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit'])) ? $_REQUEST['btnUdmSubmit'] : false;

// Fetch or save user data.
if ( $sub ) {

    // Save only if submitted data is present, and the 	user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
   if( $udm->saveUser()) {
		if ($_POST['can']) {
			$i=0;
			if (!$uid) {
				$guide_id =$dbcon->Insert_ID();
			} else {$guide_id =$uid;}
			$del_guide=$dbcon->Execute("delete from voterguide where guide_id = $guide_id"); 

			while ($i <= 55){ 
				if ($_POST['can'][$i]) {
					$guide_sql="INSERT INTO voterguide (item,reason,position,guide_id, textorder) VALUES (".$dbcon->qstr($_POST['can'][$i]).", ".$dbcon->qstr($_POST['reason'][$i]).",".$dbcon->qstr($_POST['stance'][$i]).",'$guide_id', '".$_POST['textorder'][$i]."')";
					//DIE( $guide_sql);
					$add_guide=$dbcon->Execute($guide_sql) or DIE("Could not insert guide record".$dbcon->ErrorMsg());
				}
				$i++;
			}		
		}
		header ("Location:modinput4_data.php?modin=52&editlink=voterguide.php");
   }
} elseif ( !$sub && $uid ) {

    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
    $udm->getUser( $uid ); 

	$my_slate_sql="Select * from voterguide where guide_id=".$uid." ORDER BY textorder";
	$my_slate = $dbcon->Execute($my_slate_sql) or DIE("Could not get guide record".$dbcon->ErrorMsg());
	$i = 0;
	while (!$my_slate->EOF){
		$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" value="'.$my_slate->Fields("item").'" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Position</b></td>
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
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Reasons</b><br><textarea name="reason['.$i.']" cols="65">'.$my_slate->Fields("reason").'</textarea><input name="textorder['.$i.']" type="hidden" value="'.$i.'"></td></tr></table></td>
	</tr>
	<tr>';
		$my_slate->MoveNext();
		$i++;
	}


}

if (!$slate_html) {
	$i = 0;
	while ($i <= 55){
		$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Position</b></td>
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
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Reasons</b><br><textarea name="reason['.$i.']" cols="45"></textarea><input name="textorder['.$i.']" type="hidden" value="'.$i.'"></td></tr></table></td>
	</tr>
	<tr>';
		$i++;
	}
}


$insert_html = $slate_html;
$upload_script="<script type=\"text/javascript\">

function showUploadWindow (pform, pfield) {


	url  = 'http://".$_SERVER['SERVER_NAME']."/custom/upload_popup.php?pform='+pform+'&pfield='+pfield;
	
	hWnd = window.open( url, 'recordWindow', 'height=175,width=300,scrollbars=no,menubar=no,toolbar=no,resizeable=no,location=no,status=no' );
	
}
</script>";
	 
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

print $upload_script;
print "<h2>Add/Edit " . $udm->name . "</h2>";
print "<center><table width='400'><tr><td>";
$udm->showForm = true;
$volform= $udm->output();
$submitspot=strpos($volform, "input name=\"btnUdmSubmit\"");
$insertpoint = strpos(substr($volform, $submitspot-200, 200), "<tr>");
$form_footer=substr($volform, $submitspot-200+$insertpoint);
$volform = substr($volform, 0, $submitspot-200+$insertpoint);
print $volform;
print $insert_html;
print $form_footer;
#print $debug_html;
print "</td></tr></table></center>";

// Append the footer and clean up.
require_once( 'footer.php' );

?>
