<?php

/*****
 *
 * AMP UserData Search Admin Interface
 *
 * (c) 2004 Radical Designs
 * 
 *****/

require_once( 'AMP/UserDataInput.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );
require_once('AMP/UserDataSearch.php');

#set_error_handler( 'e' );

//Set default Search Mode 
if (!isset($_REQUEST[modin])){ $modin=1;} else {$modin=$_REQUEST[modin];}

// Fetch the form instance specified by submitted modin value.
$udm = new UserDataInput( $dbcon, $modin );
$udm->admin = true;
$usersearch= &new UserList;
$usersearch->addModule($modin);


// Was search submitted via the web?
$sub=(isset($_REQUEST['UDM_search_items']));

// Search user data.
if ( $sub ) {
	//Search form has been submitted, assemble SQL query
	
	$udm=$usersearch->readSearch($udm); 		
	$usersearch->runSearch($dbcon);
	

	$div_header_html="<div class=\"tabpage\" id=\"tabpage_%s\">";	
		
	//1st page shows search results

	if (count($usersearch->current_list)>0){
		$pagehead= "<h2>Results from " . $udm->name . "</h2>";
		$pagehead.= "<P><center>";#<table width='400'><tr><td>";
				
		$tabhead.= $usersearch->tab_navs();
		$tabhead.=sprintf($div_header_html, "Results");	
		#print($usersearch->translateSearch(0, $udm);	
		$results_page= $usersearch->output_list();
		$tabfoot= "</center></div>";
	
	
		$show_div_footer=TRUE;
		$formhead=sprintf($div_header_html,"Search Options");
	} 
$search_summary= '<div style ="background-color:E3E3E3; width:300px; min-height=50px; vertical-align:center; text-align:left; padding: 5px;">'.$usersearch->translateSearch($udm).'</div><P>';


    
} 

$modidselect=$dbcon->Execute("SELECT id from modules where publish=1 and userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");


	/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $usersearch object, or from print() or echo() statements.
	
	*/


require_once( 'header.php' );

	//Create page with search form

if (!isset($pagehead)) {
	$pagehead= "<h2>Search " . $udm->name . "</h2>";
	$pagehead.= "<P align=\"left\">";
} else {
	$formhead.="<h3>Search " . $udm->name . "</h3>";
	$formhead.="<P align=\"left\">";
}
print $pagehead.$search_summary.$tabhead.$results_page.$tabfoot.$formhead;
print $usersearch->SearchForm($udm);
print $formfoot;
	
if ($show_div_footer) {
    print "</div>";
}
require_once( 'footer.php' );

?>