<?php


require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "module_control";
$listtitle ="Settings";
$listsql ="select id, description, setting   from $table  ";
$orderby =" order by  description asc  ";
$fieldsarray=array( 'Setting'=>'description','Value'=>'setting');
$filename="module_control.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = "module_control_list.php?modid=".$_POST['modid'];
	$MM_editColumn = "id";
    $MM_fieldsStr =  "modid|value|var|value|display|value|description|value|setting|value";
    $MM_columnsStr = "modid|',none,''|var|',none,''|display|',none,''|description|',none,''|setting|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$modid = $R->Fields("modid");


$M = $dbcon->Execute("SELECT id, name FROM modules ORDER BY name ASC") or DIE($dbcon->ErrorMsg());

$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');

$mod_options = makelistarray($M,'id','name','Select Module');
$Mod = & new Select('modid',$mod_options,$R->Fields("modid"));
$html .=  $buildform->add_row('Module', $Mod);

$html .= addfield('description','Setting Description','text',$R->Fields("description"));
$html .= addfield('setting','Value','textarea',$R->Fields("setting"));
$html .= $buildform->add_header('Advanced Settings', 'intitle');
$html .= addfield('var','Var Name','text',$R->Fields("var"));
$html .= addfield('display','Public','text',$R->Fields("display"),1);

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
