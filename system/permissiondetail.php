<?php
#generic update page
$mod_name="system";

require("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "per_description";
$listtitle ="Permissions";
$listsql ="select * from $table  ";
$orderby =" order by name  ";
$fieldsarray=array('Name'=>'name','Publish'=>'publish','ID'=>'id');
$filename="permissiondetail.php";


ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "id|value|name|value|description|value|publish|value";
    $MM_columnsStr = "id|',none,''|name|',none,''|description|',none,''|publish|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|
	require ("../Connections/insetstuff.php");
 	require ("../Connections/dataactions.php");
	ob_end_flush();	
}

// build sql
if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());

//declare form objects
$rec_id = & new Input('hidden', 'MM_recordId', $_GET[id]);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"),1);
$html .= addfield('name','Name','text',$R->Fields("name"));
$html .= addfield('description','Description','textarea',$R->Fields("description"));
$html .= addfield('id','ID','text',$R->Fields("id"));
$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");

if ($_GET[action] == "list") {
	listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort);
	
}
else {
	echo $form->fetch();
}	

include ("footer.php");
?>