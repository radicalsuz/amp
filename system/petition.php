<?php
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

$extra = array('Petition Signers'=>'modinput4_data.php?modin=','Petition Fields'=>'modinput4_edit.php?modin=');
$extramap = array('Petition Fields'=>'udmid','Petition Signers'=>'udmid');

$filename="petition.php";



ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

	if ($_POST['MM_insert']) {
		require_once( 'AMP/UserData/Input.inc.php' );
		require_once( 'utility.functions.inc.php');

		$_POST['core_name'] = "Petition - ".$_POST['title'];
		$udm = new UserDataInput( $dbcon, 7, true );
		$udm->doPlugin( "QuickForm", "build_admin" );
		if($new_modin=$udm->doPlugin( "AMP", "copy_admin" )) 
		$udmid = $new_modin;
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
$html .= addfield('title','Title','text',$R->Fields("title"));
$html .= addfield('addressedto','Addressed to','text',$R->Fields("addressedto"));
$html .= addfield('shortdesc','Short Description','textarea',$R->Fields("shortdesc"));
$html .= addfield('text','Text of Petition','textarea',$R->Fields("text"));
$html .= addfield('intsigner','Submitted By','text',$R->Fields("intsigner"));
$html .= addfield('datestarted','Start Date','text',DateOut($R->Fields("datestarted")));
$html .= addfield('dateended','End Date','text',DateOut($R->Fields("dateended")));
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
