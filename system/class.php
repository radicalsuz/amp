<?php
$modid = "19";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "class";
$listtitle ="Class";
$listsql ="select id,   from $table  ";
$orderby =" order by class asc  ";
$fieldsarray=array( 'Class'=>'class','ID'=>'id'
					);
$filename="class.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = "edittypes.php";
	$MM_editColumn = "id";
   $MM_fieldsStr =
"type|value|templateid|value|linkurl|value|uselink|value|up|value|class|value|description|value|useclass|value";
    $MM_columnsStr = "type|',none,''|templateid|',none,''|linkurl|',none,''|uselink|',none,''|up|',none,''|class|',none,''|description|',none,''|useclass|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$T = $dbcon->Execute("SELECT name, id FROM template ORDER BY id ASC") or DIE($dbcon->ErrorMsg());

$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');

$html .= addfield('class','Name','text',$R->Fields("class"));
$html .= addfield('description','Description','textarea',$R->Fields("description"));
$html .= addfield('useclass','Publish','checkbox',$R->Fields("useclass"));

$html .= $buildform->add_header('Class Index Page Settings');
$html .= addfield('up','Class index page content list repeats','text',$R->Fields("up"));
$html .= addfield('uselink','Redirect class index page','checkbox',$R->Fields("uselink"));
$html .= addfield('linkurl','Redirect index page to this url','text',$R->Fields("linkurl"));
//$html .= addfield('header','Use content for the class index page header','checkbox',$R->Fields("header"));
//$html .= addfield('url','Header content ID# to use for header','text',$R->Fields("url"));

$html .= $buildform->add_header('Layout Settings');
$template_options = makelistarray($T,'id','name','Select Template');
$Tempalte = & new Select('tempateid',$template_options,$R->Fields("tempateid"));
$html .=  $buildform->add_row('Tempalte Override', $Tempalte);
$Type = & new Select('type', $obj->select_type_tree2(0),$R->Fields("type"));
$html .=  $buildform->add_row('Section', $Type);


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