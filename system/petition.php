<?php

// something needs to deal with the petition redirect
$modid = "7";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "petition";
$listtitle ="Petitions";
$listsql ="select id, title, udmid   from $table  ";
$orderby =" order by  title asc  ";
$fieldsarray=array( 'Title'=>'title','ID'=>'id');

$extra = array('Petition Signers'=>'petition_udm_list.php?modin=','Petition Fields'=>'modinput4_edit.php?modin=','Add to Content System'=>'module_contentadd.php?pid=');
$extramap = array('Petition Fields'=>'udmid','Petition Signers'=>'udmid');

$filename="petition.php";



ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

	if ($_POST['MM_insert']) {
		require_once( 'AMP/UserData/Input.inc.php' );
		require_once( 'utility.functions.inc.php');
		$chsql = "select id from userdata_fields where id =4";
	    $check  = $dbcon->Execute($chsql) or die('petition setup faild ' . $chsql . $dbcon->ErrorMsg());

		if (!$check->Fields("id")) {
		
			$addsql = "INSERT INTO `userdata_fields` VALUES (4, 'Petition', 0, 1, 1, 0, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 1, 1, 1, 0, 1, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 1, 1, 1, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'text', 'text', 'text', 'text', 'text', 'text', 'textarea', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'select', 'text', 'select', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'checkbox', 'header', 'header', 'header', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'text', 'Title', 'First Name', 'Last Name', 'Suffix', 'MI', 'Organization', 'Comments to display on website', 'Email', 'Phone', 'Call Phone', 'Phone Provider', 'Work Phone', 'Pager', 'Work Fax', 'Home Fax', 'Web Page', 'Street', 'Street 2', 'Street 3', 'City', 'State', 'Zip', 'Country', 'Title/Position', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 'Show my name and comments on this site', 'Personal Information', 'Contact Information', 'Other Information', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 1, 1, 0, 0, 0, 0, 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '1', 'Personal Information', 'Contact Information', 'Other Information', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'US', 0, 'WORLD', 0, '', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 35, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, '0000-00-00', '', 0, 0, 0, '', '', 3, 2, 0, 0, 0, 0, 0, 'custom20,First_Name,Last_Name,Company,occupation,custom21,Email,Phone,Web_Page,Street,Street_2,City,State,Zip,Country,custom22,Notes,custom19', 1)";
	        $dbcon->Execute($addsql) or die('pettion setup faild ' . $addsql . $dbcon->ErrorMsg());
		}
  
		$_POST['core_name'] = "Petition - ".$_POST['title'];
		
		$udm = new UserDataInput( $dbcon, '7', true );
	    $udm->doPlugin( "QuickForm", "BuildAdmin" );
    	$new_modin = $udm->doPlugin( "AMPsystem", "CopyAdmin" );
		if ($new_modin) {
			$udmid = $new_modin;
		} else {
		    die("copy failed");
		}
	}
	$datestarted =DateConvertIn($datestarted);
	$dateended =DateConvertIn($dateended);
    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
    $MM_fieldsStr = "title|',none,''|addressedto|value|shortdesc|value|text|value|intsigner|value|intsignerad|value|intsignerem|value|org|value|url|value|datestarted|value|dateended|value|udmid|value";
    $MM_columnsStr = "title|',none,''|addressedto|',none,''|shortdesc|',none,''|text|',none,''|intsigner|',none,''|intsignerad|',none,''|intsignerem|',none,''|org|',none,''|url|',none,''|datestarted|',none,''|dateended|',none,''|udmid|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= $buildform->add_header('Petition Text');
$html .= addfield('title','Petition Title','text',$R->Fields("title"));
$html .= addfield('addressedto','Addressed to','text',$R->Fields("addressedto"));
$html .= addfield('shortdesc','Short Description','textarea',$R->Fields("shortdesc"));
$html .= addfield('text','Text of Petition','textarea',$R->Fields("text"));
$html .= $buildform->add_header('Petition Length');
$html .= addfield('datestarted','Start Date (format:01/20/2005)','text',DateOut($R->Fields("datestarted")));
$html .= addfield('dateended','End Date (format:01/20/2005)','text',DateOut($R->Fields("dateended")));
$html .= $buildform->add_header('Petition Sponser');
$html .= addfield('intsigner','Submitted By','text',$R->Fields("intsigner"));
$html .= addfield('intsignerad','Contact Info','text',$R->Fields("intsignerad"));
$html .= addfield('intsignerem','E-mail','text',$R->Fields("intsignerem"));
$html .= addfield('org','Organization','text',$R->Fields("org"));
$html .= addfield('url','Web Address','text',$R->Fields("url"));

$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");
if ($_GET['action'] == "list") {
	listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra,$extramap);
}
else {
	echo $form->fetch();
}	
include ("footer.php");
?>
