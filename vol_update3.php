<?php 
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData.php' );
#include_once("AMP/DBfunctions.inc.php"); 
include_once( 'DIA/API.php');

if ( !defined( 'DIA_API_ORGCODE')) define ( 'DIA_API_ORGCODE', 199) ;
ob_start();


if (!isset($_GET[modin])){ $modin=8;} else {$modin=$_GET[modin];}

// Fetch the form instance specified by submitted modin value.
$udm = new UserData( $dbcon, $modin );

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;
$otp = (isset($_REQUEST['otp'])) ? $_REQUEST['otp'] : null;


 // Was data submitted via the web?
$sub = isset($_REQUEST['btnUdmSubmit']);

// Check fo rduplicates, setting $uid if found.
if ( !$uid ) {

	$uid = $udm->findDuplicates();

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
	//SEND DIA INFO
	if (isset($_REQUEST['custom12'])) {
		$supporter = array('Title' => $_REQUEST['Title'], 'Last_Name' => $_REQUEST['Last_Name'], 'Suffix' => $_REQUEST['Suffix'], 'First_Name' => $_REQUEST['First_Name'], 'MI' => $_REQUEST['MI'], 'Company' => $_REQUEST['Company'], 'Notes' => $_REQUEST['Notes'], 'Email' => $_REQUEST['Email'], 'Phone' => $_REQUEST['Phone'], 'Cell_Phone' => $_REQUEST['Cell_Phone'], 'Phone_Provider' => $_REQUEST['Phone_Provider'], 'Work_Phone' => $_REQUEST['Work_Phone'], 'Pager' => $_REQUEST['Pager'], 'Home_Fax' => $_REQUEST['Home_Fax'], 'WebPage' => $_REQUEST['WebPage'], 'Street' => $_REQUEST['Street'], 'City' => $_REQUEST['City'], 'State' => $_REQUEST['State'], 'Zip' => $_REQUEST['Zip'], 'Country' => $_REQUEST['Country'],  'Work_Fax' => $_REQUEST['Work_Fax'], 'Street_2' => $_REQUEST['Street_2'], 'Street_3' => $_REQUEST['Street_3'], 'occupation' => $_REQUEST['occupation']    );
	
	  $supp_email = $_REQUEST['Email'];
	  $diaReq = & DIA_API::create( );
	  $result = $diaReq->addSupporter( $supp_email, $supporter );
	}
	//Check for POST DATA from VOLUNTEER RECORD
	if (is_array($_POST[available])) {
		$d=$dbcon->Execute("delete from vol_relavailability where personid = ".$udm->uid) or DIE($dbcon->ErrorMsg());
		foreach($_POST[available] as $v) { 
			$rel_avail_sql="INSERT INTO vol_relavailability (personid,availabilityid) VALUES (".$udm->uid.",'$v')";
			$relupdate=$dbcon->Execute($rel_avail_sql);# or DIE("40".$dbcon->ErrorMsg());
			#$debug_html.=$rel_avail_sql."<BR>";
		}
		
	}
	if (is_array($_POST[skills])) {
		$d=$dbcon->Execute("delete  from vol_relskill where personid = ".$udm->uid) or DIE($dbcon->ErrorMsg());
		foreach($_POST[skills] as $v) { 
			$rel_skill_sql="INSERT INTO vol_relskill (personid,skillid) VALUES (".$udm->uid.",'$v')";
			$relupdate=$dbcon->Execute($rel_skill_sql);# or DIE("40".$dbcon->ErrorMsg());
		}
	}
	if (is_array($_POST[interests])) {
		$d=$dbcon->Execute("delete from vol_relinterest where personid = ".$udm->uid) or DIE($dbcon->ErrorMsg());
		foreach($_POST[interests] as $v) { 
			$rel_interest_sql="INSERT INTO vol_relinterest (personid,interestid) VALUES (".$udm->uid.",'$v')";
			$relupdate=$dbcon->Execute($rel_interest_sql);# or DIE("40".$dbcon->ErrorMsg());
			$debug_html .= $rel_interest_sql."<BR>";
		}
	}


	



} elseif ( $uid && $auth && !$sub ) {

    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
    $udm->getUser( $uid ); 
		//Get Associated Data Sets
	if ($modin==8){
		$my_avail_sql="Select availabilityid, id from vol_relavailability where personid=".$uid;
		$my_avail = $dbcon->GetAssoc($my_avail_sql);
		$my_skill_sql="Select skillid, id from vol_relskill where personid=".$uid;
		$my_skill=$dbcon->GetAssoc($my_skill_sql);
		$my_interest_sql="Select interestid, id from vol_relinterest where personid=".$uid;
		$my_interest=$dbcon->GetAssoc($my_interest_sql);
		
	}

}

if ($modin==8){//VOLUNTEER MODULE CONNECTIONS
		$skills_sql="Select * from vol_skill ORDER BY orderby ASC";
		$skills = $dbcon->Execute($skills_sql);
		$interests_sql="Select * from vol_interest";
		$interests=$dbcon->Execute($interests_sql);
		$avail_sql="Select * from vol_availability ORDER by id ASC";
		$avail=$dbcon->Execute($avail_sql);# or DIE($dbcon->ErrorMsg();
		//Create Form HTML for related datasets
		$avail_html = '<tr><td align="center" valign="top" nowrap class="intitle" colspan=2><B>Indicate Availability</B></td></tr>';
		while (!$avail->EOF){
			$avail_html.='<tr><td  valign="top" align="right" class="form_data_col"><input type="checkbox" name="available[]" value="'.$avail->Fields("id").'"';
			if (is_array($my_avail)) {
				if (array_key_exists($avail->Fields("id"), $my_avail)){$avail_html.=" checked";}
			}
			$avail_html.='></td><td  align="left" valign="top" class="form_label_col"><b>'.ucwords(str_replace("_", " ", $avail->Fields("availability"))).'</b></td></tr>';
			$avail->MoveNext();
		}
		#$avail_html.=' </table></td></tr>';

		$interests_html='<tr><td align="center" valign="top" nowrap class="intitle" colspan=2><B>Select Interests</B></td></tr>';
		while (!$interests->EOF){
			$interests_html.='<tr><td  valign="top" align="right" class="form_data_col"><input type="checkbox" name="interests[]" value="'.$interests->Fields("id").'"';
			if (is_array($my_interest)) {
				if (array_key_exists($interests->Fields("id"), $my_interest)){$interests_html.=" checked";}
			}
			$interests_html.='></td><td  align="left" valign="top" class="form_label_col"><b>'.$interests->Fields("interest").'</b></td></tr>';
			$interests->MoveNext();
		}
		#$interests_html.=' </table></td></tr>';

		$skills_html='<tr><td align="center" valign="top" nowrap class="intitle" colspan=2><B>Select Skills</B></td></tr>';

		while (!$skills->EOF){
			$skills_html.='<tr><td  valign="top" align="right" class="form_data_col"><input type="checkbox" name="skills[]" value="'.$skills->Fields("id").'"';
			if (is_array($my_skill)) {
				if (array_key_exists($skills->Fields("id"), $my_skill)){$skills_html.=" checked";}
			}
			$skills_html.='></td><td  align="left" valign="top" class="form_label_col"><b>'.$skills->Fields("skill").'</b></td></tr>';
			$skills->MoveNext();
		}
		#$skills_html.=' </table></td></tr>';

}

$insert_html = $avail_html.$interests_html.$skills_html;

	 
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
