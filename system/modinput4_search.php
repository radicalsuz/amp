<?php

/*****
 *
 * AMP UserData Search 
 *
 * (c) 2004 Radical Designs
 * Written by Blaine Cook, blaine@radicaldesigns.org
 *
 *****/

require_once( 'AMP/UserData.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );
require_once('udm_list.inc.php');

#set_error_handler( 'e' );
//Set default Search Mode to Email Alert Module
if (!isset($_REQUEST[modin])){ $modin=3;} else {$modin=$_REQUEST[modin];}

// Fetch the form instance specified by submitted modin value.
$udm = new UserData( $dbcon, $modin );
$udm->admin = true;

$modidselect=$dbcon->Execute("SELECT id from modules where publish=1 and userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");

// User ID.
$uid = (isset($_REQUEST['uid'])) ? $_REQUEST['uid'] : false;

// Was data submitted via the web?
$sub = (isset($_REQUEST['btnUdmSubmit'])) ? $_REQUEST['btnUdmSubmit'] : false;

// Search user data.
if ( $sub ) {
	//Search form has been submitted, assemble SQL query
	
	$usersearch= &new UserList;
	$fieldnames=array_keys($udm->fields);
	if(!isset($_REQUEST['AMP_search_count'])) {$search_set_count=0;$search_set_start=0;}
	else {
		$search_set_count=$_REQUEST['AMP_search_count']++;
		$search_set_start=$search_set_count;
		
	}
	
	foreach ($fieldnames as $searchfield) {
		 if ($_POST[$searchfield] == "EMPTY") {
			 $search_set_count++;
			$usersearch->setLogic("OR", $search_set_count, 'internal');
			$usersearch->addCriteria($searchfield, "=", "''", $search_set_count);
			$usersearch->addCriteria("IsNull(".$searchfield.")", "", "", $search_set_count);}
		elseif ($_POST[$searchfield]  == "NOT EMPTY"){
			$search_set_count++;
			#$usersearch->setLogic("OR", $search_set_count, 'internal');
			$usersearch->addCriteria("!IsNull(".$searchfield.")", "", "", $search_set_count);
			$usersearch->addCriteria($searchfield, "!=", "''", $search_set_count);}
		elseif ($_POST[$searchfield] != NULL) {
			$usersearch->addCriteria($searchfield, "LIKE ", "'%".$_POST[$searchfield]."%'");
		}
	}
	//NEED GLOBAL PHONE/FAX SEARCH
	#if ($_POST[phone]  != NULL){$sql7.= " (userdata.Phone LIKE '%".$_POST[phone]."%' or userdata.Cell_Phone LIKE '%$phone%' or userdata.Work_Phone LIKE '%$phone%') AND  ";}

	$usersearch->addModule($modin);
	$search_set=array();
	for ($i=$search_set_start; $i<=$search_set_count; $i++){
		$search_set[$i]=$i;
	}
	$usersearch->setupSearch($search_set_count, $search_set);
	$usersearch->runSearch($dbcon);
	$insert_html="<input type=\"hidden\" name=\"AMP_search_count\" value=\"$search_set_count\">";
	
	require_once( 'header.php' );
	print "<script type=\"text/javascript\">\r\n 
	
	function hideClass(theclass, objtype) {
	if (objtype=='') {objtype='div';}
	for (i=0;i<document.getElementsByTagName(objtype).length; i++) {
		if (document.getElementsByTagName(objtype).item(i).className == theclass){
			document.getElementsByTagName(objtype).item(i).style.display = 'none';
		}
	}
	}

	function showClass(theclass, objtype) {
	if (objtype=='') {objtype='div';}
	for (i=0;i<document.getElementsByTagName(objtype).length; i++) {
		if (document.getElementsByTagName(objtype).item(i).className == theclass){
			document.getElementsByTagName(objtype).item(i).style.display = 'block';
		}
	}
	}

	function change(which, whatkind) {
	if (whatkind!='') {hideClass(whatkind, '');}
		if(document.getElementById(which).style.display == 'block' ) {
			document.getElementById(which).style.display = 'none';
		} else {
		document.getElementById(which).style.display = 'block';
		//alert(which+'/'+whatkind);
		}
	}
	</script>";

	print $usersearch->tab_navs();
	$div_header_html="<div class=\"tabpage\" id=\"tabpage_%s\">";	
		
	//1st page shows search results

	//Create 2nd page with search form
	//3rd page will offer to combine lists or searches		  

	printf($div_header_html, "Results");	
	print "<h2>Results from " . $udm->name . "</h2>";
	print "<center>";#<table width='400'><tr><td>";
	print $usersearch->output_list();
	print $debug_html;
	#print "</td></tr></table></center>";
	print "</center>";
	print "</div>";
	
	$show_div_footer=TRUE;
	printf($div_header_html,"Add Search");


    // Save only if submitted data is present, and the user is
    // authenticated, or if the submission is anonymous (i.e., !$uid)
    
} elseif ( !$sub && $uid ) {

    // Fetch the user data for $uid if there is no submitted data
    // and the user is authenticated.
    #$udm->getUser( $uid ); 
	echo "Search form does not accept uid values";
	
} 

#else if(!$sub && !$uid) { //Search form must be filled out
	//CREATE ADDITIONAL fields for UDM search construction
	#$insert_html = $avail_html.$interests_html.$skills_html;


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
	$search_form = $udm->output();
	$submitspot = strpos($search_form, "input name=\"btnUdmSubmit\"");
	$insertpoint = strpos(substr($search_form, $submitspot-200, 200), "<tr>");
	$form_footer = substr($search_form, $submitspot-200+$insertpoint);
	$search_form = substr($search_form, 0, $submitspot-200+$insertpoint);
	//ASSIGN FORM VARIABLES TO _POST DATASET

	$actionspot = strpos($search_form, "<form action=");
	$actionspot2=strpos($search_form,"\"",$actionspot);
	$action_end = strpos($search_form, "\"", $actionspot2+1);
	$search_form=substr($search_form,0,$actionspot+$actionspot2)."modinput4_search.php\" method=\"POST".substr($search_form,($actionspot+$actionspot2+$action_end-1));

	print $search_form;
	print $insert_html;	 
	print $form_footer;
	print $debug_html;
	print "</td></tr></table></center>";

#}
// Append the footer and clean up.
if ($show_div_footer) {
    print "</div>";
}
require_once( 'footer.php' );

?>