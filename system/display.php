<?php
$modid = "";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "display";
$listtitle ="Dispaly Page";
$listsql ="select id, name  from $table  ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'Name'=>'name','ID'=>'id'
					);
$filename="display.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "name|value|mod_id|value|mod_intro_list_id|value|mod_intro_detail_id|value|sql|value|list_html|value|detail_html|value|display_fields|value|sort_field|value|sort_class|sort_field2|value|sort_class2|value|sql_order|value";
    $MM_columnsStr = "name|',none,''|mod_id|',none,''|mod_intro_list_id|',none,''|mod_intro_detail_id|',none,''|sql|',none,''|list_html|',none,''|detail_html|',none,''|display_fields|',none,''|sort_field|',none,''|sort_class|',none,''|sort_field2|',none,''|sort_class2|',none,''|sql_order|',none,''";
; //|$delim,$altVal,$emptyVal|  |',none,''|
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

$html .= addfield('name');

$html .= $buildform->add_header('HTML for Display Pages');
$html .= addfield('list_html','List HTML','textarea',$R->Fields("list_html"),'','55','15');
$html .= addfield('detail_html','Detail HTML','textarea',$R->Fields("detail_html"),'','55','15');

$html .= $buildform->add_header('SQL');
$html .= addfield('sql','SQL','textarea',$R->Fields("sql"));
$html .= addfield('sql_order');
$html .= addfield('display_fields','Display Fields','textarea',$R->Fields("display_fields"));

$html .= $buildform->add_header('Fields for Grouping');
$html .= addfield('sort_field');
$html .= addfield('sort_class',"Sort HTML",'textarea',$R->Fields("sort_class"));
$html .= addfield('sort_field2');
$html .= addfield('sort_class2',"2nd Level Sort HTML",'textarea',$R->Fields("sort_class2"));

$html .= $buildform->add_header('Module and Text Page IDs');
$html .= addfield('mod_id');
$html .= addfield('mod_intro_list_id');
$html .= addfield('mod_intro_detail_id');


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
