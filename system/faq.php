<?php
$modid = "4";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "faq";
$listtitle ="Frequently Asked Questions";
$listsql ="select id, question, publish, answered   from $table  ";
$orderby =" order by publish,question asc  ";
$fieldsarray=array( 'Question'=>'question','Answered'=>'answered','Status'=>'publish',
					);
$filename="faq.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "question|value|firstname|value|lastname|value|email|value|shortanswer|value|longanswer|value|publish|value|typeid|value|answered|value";	;
    $MM_columnsStr = "question|',none,''|firstname|',none,''|lastname|',none,''|email|',none,''|shortanswer|',none,''|longanswer|',none,''|publish|none,1,0|typeid|none,none,NULL|answered|none,1,0";	
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
	ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$T=$dbcon->Execute("SELECT * FROM faqtype ORDER BY type ASC") or DIE($dbcon->ErrorMsg());

$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"),1);
$html .= addfield('question','Question','text',$R->Fields("question"));
$html .= addfield('firstname','First Name','text',$R->Fields("firstname"));
$html .= addfield('lastname','Last Name','text',$R->Fields("lastname"));
$html .= addfield('email','E-mail','text',$R->Fields("email"));
$html .= addfield('shortanswer','Short Answer','textarea',$R->Fields("shortanswer"));
$html .= addfield('longanswer','Long Answer','textarea',$R->Fields("longanswer"));

$type_options = makelistarray($T,'id','type','Select FAQ Type');
$faq_type = & new Select('typeid',$type_options,$R->Fields("typeid"));
$html .=  $buildform->add_row('FAQ Type', $faq_type);

$html .= addfield('answered','Answered','checkbox',$R->Fields("answered"));


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
