<?php
$mod_name="content";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "hotwords";
$listtitle ="Hot Words";
$listsql ="select id, word, url, publish   from $table  ";
$orderby =" order by  word asc  ";
$fieldsarray=array( 'Word'=>'word','URL'=>'url','Status'=>'publish'
					);
$filename="hotwords";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
    $MM_fieldsStr = "word|value|url|value|section|value|publish|value";
    $MM_columnsStr = "word|',none,''|url|',none,''|section|',none,1|publish|none,1,0";
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

$html .= addfield('word','Word','text',$R->Fields("word"));
$html .= addfield('url','URL','text',$R->Fields("url"));
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"));

$Type = & new Select('section', $obj->select_type_tree2(0),$R->Fields("section"));
$html .=  $buildform->add_row('Section Specific HotWord', $Type);

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
