<?php
$mod_name="module";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "modules";
$listtitle ="Modules";
$listsql ="select id, name  from $table  ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'Module'=>'name','ID'=>'id'
					);
$extra=array('Settings'=>'module_control_list.php?modid=');
$filename="module.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
    $MM_fieldsStr =
"id|value|name|value|userdatamod|value|userdatamodid|value|file|value|perid|value|navhtml|value|publish|value|module_type|value";
    $MM_columnsStr = "id|',none,''|name|',none,''|userdatamod|',none,''|userdatamodid|',none,''|file|',none,''|perid|',none,''|navhtml|',none,''|publish|',none,''|module_type|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$T=$dbcon->Execute("SELECT * FROM module_type ") or DIE($dbcon->ErrorMsg());


$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= addfield('name','Module','text',$R->Fields("name"));
$tem_options = makelistarray($T,'id','name','Select Module Type');
$Tem = & new Select('module_type',$tem_options,$R->Fields("module_type"));
$html .=  $buildform->add_row('Module Type', $Tem);
$html .= addfield('perid','Permission','text',$R->Fields("perid"));
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"));
$html .= addfield('navhtml','Navigation HTML','textarea',$R->Fields("navhtml"));
$html .= addfield('file','Default File','text',$R->Fields("file"));
$html .= addfield('userdatamod','User Data Module','checkbox',$R->Fields("userdatamod"));
$html .= addfield('userdatamodid','UDM ID','text',$R->Fields("userdatamodid"));
$html .= addfield('id','ID','text',$R->Fields("id"));

$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");
if ($_GET['action'] == "list") {
	listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);
}
else {
	echo $form->fetch();
}	
include ("footer.php");
?>