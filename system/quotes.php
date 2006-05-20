<?php
$modid = "41";

require_once( 'AMP/Content/Quote/ComponentMap.inc.php');

$map = &new ComponentMap_Quote( );
$controller = &$map->get_controller( );
print $controller->execute( );

/*

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "quotes";
$listtitle ="Quotes";
$listsql ="select id, quote, publish   from $table  ";
$orderby =" order by quote asc  ";
$fieldsarray=array( 'Quote'=>'quote','Publish'=>'publish','ID'=>'id'
					);
$filename="quotes.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

	$date = DateConvertIn($_POST['date']);
    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
    $MM_fieldsStr = "quote|value|source|value|publish|value|date|value|type|value";
    $MM_columnsStr = "quote|',none,''|source|',none,''|publish|',none,''|date|',none,''|type|',none,''";
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
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"),1);
$html .= addfield('quote','Quote','text',$R->Fields("quote"));
$html .= addfield('source','Source','text',$R->Fields("source"));
$html .= addfield('date','Date','text',DateOut($R->Fields("date")));

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
*/
?>
