<?php
$modid = "11";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "links";
$listtitle ="Links";
$listsql ="select id, url, linkname, publish from $table  ";
$orderby =" order by linkname asc  ";
$fieldsarray=array( 'Link Name'=>'linkname',
					'URL'=>'url',
					'Status'=>'publish');
$filename="links.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "";
    $MM_columnsStr = ""; //|$delim,$altVal,$emptyVal|  |',none,''|
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
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"));
$html .= addfield('linkname','Name','text',$R->Fields("linkname"));
$html .= addfield('description','Description','textarea',$R->Fields("description"));

$link_options = makelistarray($L,'id','name','Select Link Type');
$Link = & new Select('linktype',$link_options,$R->Fields("linktype"));
$html .=  $buildform->add_row('Link Type', $Link);

$Type = & new Select('reltype[]', $obj->select_type_tree2(0),'','true');
$html .=  $buildform->add_row('Related Sections', $Type);

$html .= addfield('url','','text',$R->Fields("url"));
$html .= addfield('image','Thumbnail','text',$R->Fields("image"));
$html .=  $buildform->add_row('', '<a href="imgdir.php" target="_blank">view images</a>&nbsp;|&nbsp;<a href="imgup.php" target="_blank">upload image</a>');





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