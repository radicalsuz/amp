<?php
#generic update page
$modid = "";

require("Connections/freedomrising.php");
$buildform = new BuildForm;


$table = "redirect";
$listtitle ="Redirects";
$listsql ="select * from $table  ";
$orderby =" order by id desc  ";
$fieldsarray=array('Old Address'=>'old','New Address'=>'new');
$filename="redirect.php";


ob_start();
// insert, update, delete
if ((($_POST[MM_update]) && ($_POST[MM_recordId])) or ($_POST[MM_insert]) or (($_POST[MM_delete]) && ($_POST[MM_recordId]))) {
    $MM_editTable  = $table;
	$MM_editColumn="id";
    $MM_recordId = $_POST[MM_recordId];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_fieldsStr = "old|value|new|value|publish|value|conditional|value|num|value";
    $MM_columnsStr = "old|',none,''|new|',none,''|publish|',none,''|conditional|',none,''|num|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|
	require ("../Connections/insetstuff.php");
 	require ("../Connections/dataactions.php");
ob_end_flush();	
}

// build sql
if ($_REQUEST['id']) {
	$q = "select * from $table where id=". $_REQUEST['id'];
	$row = $dbcon->getRow($q);
	foreach ($row as $k=>$v) {
		$r[$k] = $buildform->db_in($v);
	}
}

//declare form objects
$rec_id = & new Input('hidden', 'MM_recordId', $_GET[id]);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= addfield('publish','Publish','checkbox',$r['publish'],1);
$html .= addfield('old','Old Address','text',$r['old']);
$html .= addfield('new','New Address','text',$r['new']);
$html .= addfield('conditional','Conditional Redirect','checkbox',$r['conditional']);
$html .= addfield('num','Number of Charecters','text',$r['num']);
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