<?php 
/*****
 *
 * AMP VoterGuide Edit View
 *
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org>
 *
 *****/

require_once( 'Modules/VoterGuide/ComponentMap.inc.php' );
require_once( 'AMP/System/Page.inc.php' );

$modin = (isset($_GET['modin']) ? $_GET[ 'modin' ] : AMP_FORM_ID_VOTERGUIDES );

$map = &new ComponentMap_VoterGuide();
$page = &new AMPSystem_Page ($dbcon, $map);
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();

print $page->output( );


/*
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

// Set default module.


// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $modin);
$udm->admin = true;
$udm->authorized = true;
#$udm->unregisterPlugin("userlist_html", "Output");

$modidselect=$dbcon->Execute("SELECT id from modules where userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
$udm->uid = $uid;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit'])) ? $_REQUEST['btnUdmSubmit'] : false;

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
	$my_slate = $dbcon->GetArray($my_slate_sql);
	$i = 0;
	while ($i<55){
		if (!isset($myslate[$i])) {$myslate[$i]=array('item'=>'', 'position'=>'', 'reason'=>'', 'textorder'=>$i);}
		
		$slate_html .= '	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Candidate/Ballot Item</b></td>
		<td valign="top" align="left" class="form_data_col"><input name="can['.$i.']" type="text" size="40" value="'.$my_slate[$i]['item'].'" /></td>
	</tr>
	<tr>
		<td align="left" valign="top" class="form_label_col"><b>Position</b></td>
		<td valign="top" align="left" class="form_data_col">'.return_stance_selectbox($i, $my_slate[$i]['position']).'</td>
	</tr>

	<tr>
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Reasons</b><br><textarea name="reason['.$i.']" cols="65">'.$my_slate[$i]['reason'].'</textarea><BR><span class=side>order&nbsp;<input name="textorder['.$i.']" type="text" value="'.$i.'" size="3" class=name></span></td></tr></table></td>
	</tr>
	<tr>';
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
		<td align="left" valign="top" colspan="2"><table class="form_span_col"><tr><td><b>Reasons</b><br><textarea name="reason['.$i.']" cols="45"></textarea><BR><span class=side>order&nbsp;<input name="textorder['.$i.']" type="text" value="'.$i.'" size="3" class=name></span></td></tr></table></td>
	</tr>
	<tr>';
		$i++;
	}
}


$insert_html = $slate_html;

//UPLOAD SCRIPT OPENS AN UPLOAD WINDOW
$upload_script="<script type=\"text/javascript\">

function showUploadWindow (pform, pfield) {


	url  = 'http://".$_SERVER['SERVER_NAME']."/custom/upload_popup.php?pform='+pform+'&pfield='+pfield;
	
	hWnd = window.open( url, 'recordWindow', 'height=175,width=300,scrollbars=no,menubar=no,toolbar=no,resizeable=no,location=no,status=no' );
	
}
</script>";
	 
//ACTION SCRIPT ALLOWS for User Creation and Notification
$action_script="<script type=\"text/javascript\">
	function makeAccount() {
		actform=document.forms['user_actions'];
		udmform=document.forms['".$udm->name."'];
		
		if (udmform.elements['Email'].value>'') {
			actform.elements['email'].value=udmform.elements['Email'].value;
		} else {
			alert ('You must specify an e-mail address for this account');
			return;
		}
		if (udmform.elements['Suffix'].value>'') {
			actform.elements['block'].value=udmform.elements['Suffix'].value;
		} else {
			alert ('You must specify the associated voter bloc for this account');
			return;
		}
		reply=prompt('Please enter a login name for this account', udmform.elements['First_Name'].value);
		if (reply>'') {
			actform.elements['login'].value=reply;
		} else {
			return;
		}
		actform.elements['action'].value='create';
		actform.submit();
	}
	
	function sendAccount() {
		actform=document.forms['user_actions'];
		udmform=document.forms['".$udm->name."'];
		
		if (udmform.elements['Email'].value>'') {
			actform.elements['email'].value=udmform.elements['Email'].value;
		} else {
			alert ('You must specify an e-mail address for this to work');
			return;
		}
		actform.elements['action'].value='sendAccount';
		actform.submit();
	}

	</script>";

//Action Form only appears for authorized users
if ( AMP_Authorized( AMP_PERMISSION_FORM_DATA_EDIT)) {
	
	$action_form="<form name=\"user_actions\" action=\"voterbloc_account.php\" method=\"POST\">";
	$action_form.= "<input type=\"button\" value=\"Create Account\" onclick=\"makeAccount();\">";
	$action_form.= "<input type=\"button\" value=\"Email Login\" onclick=\"sendAccount();\">";
	$action_form.="<input type=\"hidden\" name=\"action\" value=''>";
	$action_form.="<input type=\"hidden\" name=\"login\" value=''>";
	$action_form.="<input type=\"hidden\" name=\"email\" value=''>";
	$action_form.="<input type=\"hidden\" name=\"uid\" value='".$uid."'>";
	$action_form.="<input type=\"hidden\" name=\"block\" value=''>";
	$action_form.="<input type=\"hidden\" name=\"template_id\" value='203'>";
	$action_form.="<input type=\"hidden\" name=\"login_type\" value='voterbloc'>";
	#$action_form.="<input type=\"hidden\" name=\"include_admin\" value='1'>";
	$action_form.="</form>";

	$actions= $action_script.$action_form;
	
}


/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $udm object, or from print() or echo() statements.

   By default, the form will include AMP's base template code,
   and any database-backed intro text to the appropriate module.

*/
/*

$mod_id = $udm->modTemplateID;

require_once( 'header.php' );

print $upload_script;
print "<h2>Add/Edit " . $udm->name . "</h2>";
print $actions;
print "<center><table width='400'><tr><td>";
$udm->showForm = true;

$volform= $udm->output("html");
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
*/
?>
