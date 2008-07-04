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
/* disabled due to disuse ap 2008-07
 */
/*

if (!defined( 'AMP_ENDORSE_FORM_ID_DEFAULT' )) define( 'AMP_ENDORSE_FORM_ID_DEFAULT', 1 );
if (!defined( 'AMP_ENDORSE_MODULE_ID_DEFAULT' )) define( 'AMP_ENDORSE_MODULE_ID_DEFAULT', 1 );

$modid = AMP_ENDORSE_MODULE_ID_DEFAULT;
$mod_id = (is_numeric($_GET['modtext']))?$_GET['modtext']:67;
$userdata_modin=(is_numeric($_GET['modin']))?$_GET['modin']:AMP_ENDORSE_FORM_ID_DEFAULT;

require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/BaseTemplate.php' );
#require_once('Modules/UDM/Output/userlist_html2.inc.php');
require_once( 'AMP/UserData/Set.inc.php' );

#set_error_handler( 'e' );

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

$list_options['display_format']="udm_endorser_display";
$sort_options['default_sortname']="Organization";
$sort_options['default_select']="Company";
$sort_options['default_orderby']="Company";

// Fetch the form instance specified by submitted modin value.
$admin=false;
$userlist = new UserDataSet( $dbcon, $userdata_modin );

//setup sort
if (is_array($sort_options)) {
    $sort = $userlist->getPlugins("Sort");
    $sort_plugin = current($sort);
    $sort_plugin->setOptions($sort_options);
}

//require searching to be possible
$search = $userlist->getPlugins('Search');
if (!$search) $userlist->registerPlugin('AMP', 'Search');
$searchform = $userlist->getPlugins('SearchForm');
if (!$searchform) $userlist->registerPlugin('Output', 'SearchForm');

//display result list
$order = null;
$output=$userlist->output_list('DisplayHTML', $list_options, $order ); 

$intro_id = $userlist->modTemplateID;
require_once( 'AMP/BaseTemplate.php' );
if ($intro_id != AMP_CONTENT_INTRO_ID_DEFAULT) require_once( 'AMP/BaseModuleIntro.php' );

print $output;


// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );
*/
?>
