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

require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'AMP/BaseDB.php' );


// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $_REQUEST[ 'modin' ] );

// User ID.
#$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
#$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;

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

function return_stance_selectbox($which, $selected=NULL) {
	$output="<select name=\"stance[".$which."]\"><option value=''>Select One</option>";
	
	$optionlist="Yes,No,Hell Yeah,No Way,No Endorsement";
	$optionset=split(",", $optionlist);
	foreach ($optionset as $current_option) {
		$output.="<option value=\"".$current_option."\"";
		if ($current_option==$selected) {
			$output.=" selected";
		}
		$output.=">$current_option</option>";
	}
	$output.="</select>";
	return $output;

}



// Fetch or save user data.
if ( ( !$uid || $auth ) && $sub ) {
    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
 	
	if ($udm->saveUser()) { // check to see if save was successful - no form errors
		$udm->showForm=FALSE;

		if ($_POST['can']) {
			$i=0;
			if (!$uid) {
				$guide_id =$dbcon->Insert_ID();
			} else {$guide_id =$uid;}
			$del_guide=$dbcon->Execute("delete from voterguide where guide_id = $guide_id"); 

			while ($i <= 55){ 
				if ($_POST['can'][$i]) {
					$guide_sql="INSERT INTO voterguide (item,reason,position,guide_id, textorder ) VALUES (".$dbcon->qstr($_POST['can'][$i]).", ".$dbcon->qstr($_POST['reason'][$i]).",".$dbcon->qstr($_POST['stance'][$i]).",'$guide_id', '".$_POST['textorder'][$i]."')";
				//DIE( $guide_sql);
					$add_guide=$dbcon->Execute($guide_sql) or DIE("Could not insert guide record".$dbcon->ErrorMsg());
				}
				$i++;
			}		
		}
	} else { //save wasn't successful, show form with errors
		$udm->showForm=TRUE;
		$i=0;
		while($i<=55) {
			$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item (e.g. "John Kerry for President" or "Prop 66")</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" value="'.$_POST['can'][$i].'" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Your Position</b></td>
		<td valign="top" align="left" class="form_data_col">'.return_stance_selectbox($i, $_POST['stance'][$i]).'</td>
	</tr>

	<tr>
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Why?</b><br><textarea name="reason['.$i.']" cols="65">'.$_POST[reason][$i].'</textarea>
		<input name="textorder['.$i.']" type="hidden" value="'.$i.'"></td></tr></table></td>
	</tr>
	<tr>';
		$i++;
		}
	}

} elseif ( $uid && $auth && !$sub ) {
    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
	$udm->getUser( $uid ); 
	$udm->showForm=TRUE;
	$my_slate_sql="Select * from voterguide where guide_id=".$uid." ORDER BY textorder";
	$my_slate = $dbcon->GetArray($my_slate_sql) or DIE("Could not get guide record".$dbcon->ErrorMsg());
	$i = 0;
	while ($i<=55){
		if (!isset($myslate[$i])) {$myslate[$i]=array('item'=>'', 'stance'=>'', 'reason'=>'', 'textorder'=>$i);}
		$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item (e.g. "John Kerry for President" or "Prop 66")</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" value="'.$my_slate[$i]['item'].'" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Your Position</b></td>
		<td valign="top" align="left" class="form_data_col">'.return_stance_selectbox($i, $myslate[$i]['stance']).'</td>
	</tr>

	<tr>
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Why?</b><br><textarea name="reason['.$i.']" cols="65">'.$my_slate[$i]['reason'].'</textarea>
		<input name="textorder['.$i.']" type="hidden" value="'.$myslate[$i]['textorder'].'"></td></tr></table></td>
	</tr>
	<tr>';
		$i++;
	}
} else { //create a blank slate
	$udm->showForm=TRUE;
	$i = 0;
	while ($i <= 55){
		$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item (e.g. "John Kerry for President" or "Prop 66")</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Your Position</b></td>
		<td valign="top" align="left" class="form_data_col">'.return_stance_selectbox($i).'</td>
	</tr>

	<tr>
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Why?</b><br><textarea name="reason['.$i.']" cols="45"></textarea><input name="textorder['.$i.']" type="hidden" value="'.$i.'"></td></tr></table></td>
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

$volform= $udm->output();
if ($udm->showForm==TRUE) {
$submitspot=strpos($volform, "input name=\"btnUdmSubmit\"");
$insertpoint = strpos(substr($volform, $submitspot-200, 200), "<tr>");
$form_footer=substr($volform, $submitspot-200+$insertpoint);
$volform = substr($volform, 0, $submitspot-200+$insertpoint);


#print "<center><table width='400'><tr><td>"
print $volform;
print $insert_html;
print $form_footer;
#print "</td></tr></table></center>";
}
#print $debug_html;

// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );
?>
