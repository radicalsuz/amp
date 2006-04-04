<?php
require_once( 'AMP/Content/Link/Type/ComponentMap.inc.php');

$modid = 11;
$map = &new ComponentMap_Link_Type( );
$controller = &$map->get_controller( );
print $controller->execute( );

/*
$mod_name = "links";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "linktype";
$listtitle ="Link Type";
$listsql ="select id, name, publish from $table  ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'Type Name'=>'name',
					'ID'=>'id',
					'Publish'=>'publish');
$filename="link_type.php";


ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "name|value|publish|value";
    $MM_columnsStr = "name|',none,''|publish|',none,''"; //|$delim,$altVal,$emptyVal|  |',none,''|
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
$html .= addfield('name','Link Type','text',$R->Fields("name"));
$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"));

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
