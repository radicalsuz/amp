<?php

/*****
 *
 * AMP UserData Search Admin Interface
 *
 * (c) 2004 Radical Designs
 * 
 *****/

require_once( 'AMP/UserData.php' );
require_once( 'Connections/freedomrising.php' );
require_once( 'utility.functions.inc.php' );
require_once('AMP/UserDataSearch.php');

#set_error_handler( 'e' );

//Set default Search Mode 
if (!isset($_REQUEST[modin])){ $modin=1;} else {$modin=$_REQUEST[modin];}

// Fetch the form instance specified by submitted modin value.
$udm = new UserData( $dbcon, $modin );
$udm->admin = true;
$usersearch= &new UserList;
$usersearch->addModule($modin);

$modidselect=$dbcon->Execute("SELECT id from modules where publish=1 and userdatamodid=" . $udm->instance ) or DIE($dbcon->ErrorMsg());
$modid=$modidselect->Fields("id");


// Was search submitted via the web?
$sub=(isset($_REQUEST['UDM_search_items']));

// Search user data.
if ( $sub ) {
	//Search form has been submitted, assemble SQL query
	
	$usersearch->readSearch($udm); 		
	$usersearch->setupSearch($dbcon);
	$usersearch->runSearch($dbcon);
	
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
		}
	}
	
	</script>";

	$div_header_html="<div class=\"tabpage\" id=\"tabpage_%s\">";	
		
	//1st page shows search results

	//Create 2nd page with search form
	//3rd page will offer to combine lists or searches		  

	if (count($usersearch->current_list)>0){
		$pagehead= "<h2>Results from " . $udm->name . "</h2>";
		$pagehead.= "<P><center>";#<table width='400'><tr><td>";
				
		$tabhead.= $usersearch->tab_navs();
		$tabhead.=sprintf($div_header_html, "Results");	
		#print($usersearch->translateSearch(0, $udm);	
		$results_page= $usersearch->output_list();
		$tabfoot= "</center></div>";
	
	
		$show_div_footer=TRUE;
		$formhead=sprintf($div_header_html,"Refine Search");
	} 
$search_summary= '<div style ="background-color:E3E3E3; width:300px; min-height=50px; vertical-align:center; text-align:left; padding: 5px;">'.$usersearch->translateSearch($udm).'</div><P>';


    
} 


	/* Now Output the Form.

   Any necessary changes to the form should have been registered
   before now, including any error messages, notices, or
   complete form overhauls. This can happen either within the
   $usersearch object, or from print() or echo() statements.
	
	*/

require_once( 'header.php' );
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