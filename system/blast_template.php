<?php
$mod_name="email";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
require_once( 'AMP/Form/HTMLEditor.inc.php' );

$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "blast_templates";
$listtitle ="Email Blast Template";
$listsql ="select id, name  from $table  ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'Template'=>'name','ID'=>'id'
					);
$filename="blast_template.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "name|value|description|value|article|value";
    $MM_columnsStr = "name|',none,''|description|',none,''|template|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|
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
$html .= addfield('name','Template Name','text',$R->Fields("name"));
$html .= addfield('description','Template Description','textarea',$R->Fields("description"));
$html .= addfield('template','HTML Email Template<br>add [CONTENT] <br>where the content should appear',
                        'textarea',$R->Fields("template"));

$WYSIWYG = AMPFormElement_HTMLEditor::instance();
$WYSIWYG->addEditor( 'template' );


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
    echo $WYSIWYG->output();
}	
include ("footer.php");
?>
