<?php

/*****
 *
 * AMP UserData Form View - HACKED for Volunteer Form SEARCH
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

require_once( 'AMP/UserData/UserDataInput.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );

#set_error_handler( 'e' );
if (!isset($_REQUEST[modin])){ $modin=8;} else {$modin=$_GET[modin];}
#$modid=40;

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $modin );
$udm->admin = true;

$modidselect=$dbcon->Execute("SELECT id from modules where publish=1 and userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit'])) ? $_REQUEST['btnUdmSubmit'] : false;

// Search user data.
if ( $sub ) {

    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
    
} elseif ( !$sub && $uid ) {

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

if ($modin==8){//VOLUNTEER MODULE TABLES
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

require_once( 'header.php' );


print "<h2>Search " . $udm->name . "</h2>";
print "<center><table width='400'><tr><td><p class=\"bodytext\">Enter your criteria in the fields below.</p>";
$volform= $udm->output();
$submitspot=strpos($volform, "input name=\"btnUdmSubmit\"");
$insertpoint = strpos(substr($volform, $submitspot-200, 200), "<tr>");
$form_footer=substr($volform, $submitspot-200+$insertpoint);
$volform = substr($volform, 0, $submitspot-200+$insertpoint);
//ASSIGN FORM VARIABLES TO _POST DATASET
$actionspot = strpos($volform, "<form action=");
$actionspot2=strpos($volform,"\"",$actionspot);
$action_end = strpos($volform, "\"", $actionspot2+1);
$volform=substr($volform,0,$actionspot+$actionspot2)."vol_results2.php\" method=\"POST".substr($volform,($actionspot+$actionspot2+$action_end-1));


print $volform;
print $insert_html;
print $form_footer;
print $debug_html;
print "</td></tr></table></center>";

// Append the footer and clean up.
require_once( 'footer.php' );

?>
