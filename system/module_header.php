<?php
$mod_name="module";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
require_once("WYSIWYG/editor.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "moduletext";
$listtitle ="Module Header Text";
$listsql ="SELECT t.name, t.id, m.name as modname from moduletext t left join  modules m on  t.modid = m.id ";
$orderby =" order by m.name, t.name asc  ";
$fieldsarray=array( 'Module Page'=>'name','Module'=>'modname','ID'=>'id'
					);
$filename="module_header.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

#check auto inc value
	if ($_POST['MM_insert']) {
		$id = autoinc_check('moduletext',100);
	}

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = "module_header_list.php?modid=".$_POST['modid'];
	$MM_editColumn = "id";
$MM_fieldsStr = "title|value|subtitile|value|templateid|value|type|value|names|value|modid|value|html|value|article|value|searchtype|value";
    $MM_columnsStr = "title|',none,''|subtitile|',none,''|templateid|',none,''|type|',none,''|name|',none,''|modid|',none,''|html|none,1,0|test|',none,''|searchtype|',none,''";	
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$modid=$R->Fields("modid");
$T = $dbcon->Execute("SELECT name, id FROM template ORDER BY id ASC") or DIE($dbcon->ErrorMsg());
$M = $dbcon->Execute("SELECT id, name FROM modules ORDER BY name ASC") or DIE($dbcon->ErrorMsg());

$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');

$mod_options = makelistarray($M,'id','name','Select Module');
$Mod = & new Select('modid',$mod_options,$R->Fields("modid"));
$html .=  $buildform->add_row('Module', $Mod);

$html .= addfield('names','Page Name','text',$R->Fields("name"));
$html .= addfield('title','Title','text',$R->Fields("title"));
$html .= addfield('subtitile','Subtitle','text',$R->Fields("subtitile"));

$Text = WYSIWYG($R->Fields("test"),$R->Fields("html"));
$html .=  $buildform->add_row('Text', $Text);

$Type = & new Select('type', $obj->select_type_tree2(0),$R->Fields("type"));
$html .=  $buildform->add_row('Section', $Type);

$template_options = makelistarray($T,'id','name','Select Template');
$Tempalte = & new Select('templateid',$template_options,$R->Fields("templateid"));
$html .=  $buildform->add_row('Template', $Tempalte);
$html .= addfield('searchtype','Display URL','text',$R->Fields("searchtype"));


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
