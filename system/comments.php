<?php
$modid = "23";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "comments";
$listtitle ="Comments";
if ($_GET['cid']) {
	$listsql ="select title, articleid, publish, id   from $table where articleid =".$_GET['cid']  ;
} else {
	$listsql ="select title, articleid, publish, id   from $table  ";
}
$orderby =" order by  asc  ";
$fieldsarray=array( 'Title'=>'title','Article ID'=>'articleid','Status'=>'publish');
$filename="comments.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
   $MM_fieldsStr = "author|value|title|value|email|value|comment|value|publish|value";
   $MM_columnsStr = "author|',none,''|title|',none,''|email|',none,''|comment|',none,''|publish|none,1,0";
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
$html .= addfield('field','Line Description','text',$R->Fields(""));
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"));
$html .= addfield('author','Author','text',$R->Fields("author"));
$html .= addfield('email','Email','text',$R->Fields("email"));
$html .= addfield('title','Title','text',$R->Fields("title"));
$html .= addfield('comment','Comment','textarea',$R->Fields("comment"),'',55,15);

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
