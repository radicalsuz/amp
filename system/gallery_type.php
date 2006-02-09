<?php
require_once("Modules/Gallery/ComponentMap.inc.php");
require_once("AMP/System/Page.inc.php");

$modid = AMP_MODULE_ID_GALLERY;

$map = &new ComponentMap_Gallery();
$page = &new AMPSystem_Page ($dbcon, $map);
if (isset($_GET['action']) && $_GET['action'] == "list")  $page->showList( true );

$page->execute();
print $page->output( );

/*
$mod_name="gallery";

require("Connections/freedomrising.php");
$buildform = new BuildForm;

$table = "gallerytype";
$listtitle ="Photo Gallery Types";
$listsql ="select id, galleryname  from $table  ";
$orderby =" order by galleryname desc  ";
$fieldsarray=array( 'Gallery'=>'galleryname','ID'=>'id'
					);
$extra = array('Add to Content System'=>'module_contentadd.php?gallery=');

$filename="gallery_type.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "galleryname|value|description|value|date|value";
    $MM_columnsStr = "galleryname|',none,''|description|',none,''|date|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|
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
$html .= $buildform->add_header('Edit '.$listtitle, 'banner');
$html .= addfield('galleryname','Gallery Name','text',$R->Fields("galleryname"));
$html .= addfield('description','Description','textarea',$R->Fields("description"));
$html .= addfield('date','Date','text',$R->Fields("date"));

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
*/
?>
