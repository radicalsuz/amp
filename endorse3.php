<?php  
/*********************
01-06-2005  v3.0x
Module:  Endorse
Description:  display page for endorsements
CSS: eventtitle, eventsubtitle,  bodygrey
Userdata vars: .custom1 =  show on page
Get  Vars: area  -if set  shows only endorsements in that area
To Do:  make modular so that  it can be passed  data from new modules
				write a better sql statement
*********************/ 

$modid = 6;
$mod_id = (is_numeric($_GET['modtext']))?$_GET['modtext']:67;
$userdata_modin=(is_numeric($_GET['modin']))?$_GET['modin']:1;

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/BaseTemplate.php' );
require_once('Modules/UDM/Output/userlist_html2.inc.php');
require_once( 'AMP/UserData.php' );

#set_error_handler( 'e' );

// Fetch the form instance specified by submitted modin value.
$udm = new UserData( $dbcon, $userdata_modin );

 function udm_endorser_display($data_record) {
	$output.='<span class ="eventtitle">'; 
	if (strlen($data_record['Web_Page'])>2&& $data_record['Web_Page'] != "http://") {
		if (substr($data_record['Web_Page'],7)!="http://") {
			$data_record['Web_Page']="http://".$data_record['Web_Page'];
		}
		$output.= "<a href=\"".$data_record['Web_Page']."\"";
		$output.='class ="eventtitle" target="_blank">';
		$output.= $data_record['Company'].'</a>';
	} else {
		$output.=$data_record['Company'];
	}
	$output.='</span><br>';
	if ($data_record['City'] && $data_record['State']) {
		$output.='<span class="eventsubtitle">'.$data_record['City'].', '.$data_record['State'].'</span><br>'; 
	}
	if (is_string($data_record['First_Name']) || is_string($data_record['Last_Name'] )) { 
		$output.='<span class="bodygrey">'.$data_record['First_Name'].'&nbsp;'.$data_record['Last_Name'].'</span><br>';
	}
	if ($data_record['Email'] !=NULL ) { 
		$output.='<span class="bodygrey"><a href="mailto:'. $data_record['Email'].'">'.$data_record['Email'].'</a></span><br>';
	} 
	if ($data_record['Phone'] !=NULL ) { 
		$output.='<span class="bodygrey">'.$data_record['Phone'].'</span><br>'; 
	} 
	$output.="<br>";
	return $output;
}


# $areacalled= $_GET["area"];
#include area designator for listing

$start_options['item_display_function']="udm_endorser_display";
$start_options['list_criteria']="publish=1 AND modin=$userdata_modin";
$start_options['sort_by']="Company";
$start_options['display_fields']="First_Name, Last_Name, Web_Page, Email, Phone, City, State, Company";
$udm->authorized=true;
$endorselist=new UserList_HTML($udm, $start_options);
if($output= $endorselist->output($dbcon)) {
	print $output;

} else {
	print $endorselist->error;

}

// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>