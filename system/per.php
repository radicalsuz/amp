<?php
$mod_name = "system";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "per_group";
$listtitle ="Permission Groups";
$listsql ="select id, name, description  from $table  ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'Permission Group'=>'name','Description'=>'description');
$filename="per.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

	if (!$_POST['MM_insert']) {
		//delete all records that match
		$sql = "delete from permission where groupid = ".$_POST['MM_recordId'];
		$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
		//insert 
		if (!$_POST['MM_delete']) {
			foreach ($_POST['per'] as $k=>$v) {
				if ($v == 1) {
					$sql = "insert into  permission (perid,groupid) values ('".$k."','".$_POST['MM_recordId']."') ";
					$dbcon->Execute($sql) or DIE($dbcon->ErrorMsg());
				}
			}
		}
	}

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
	$MM_fieldsStr = "subsite|value|name|value|description|value";
   	$MM_columnsStr = "subsite|',none,''|name|',none,''|description|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$permissions =$dbcon->Execute("SELECT * FROM per_description where publish = 1 ORDER BY name ASC") or DIE($dbcon->ErrorMsg());

$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= $buildform->add_header('Premission Group Settings');
$html .= addfield('name','Permission Group','text',$R->Fields("name"));
$html .= addfield('description','Description','textarea',$R->Fields("description"));
$html .= addfield('subsite','Subsite','text',$R->Fields("subsite"));
$html .= $buildform->add_header('Premissions');

while (!$permissions->EOF) { 
	$per_id = $permissions->Fields("id");
	$instance=$dbcon->Execute("SELECT id FROM permission WHERE groupid = ". $R__MMColParam ." and perid= $per_id LIMIT 1") or DIE($dbcon->ErrorMsg());
	if ($instance->Fields("id")) {$inst =1;} else {$inst =0;} 
	
	$html .= addfield("per[".$permissions->Fields("id")."]",$permissions->Fields("name"),'checkbox',$inst);
	$permissions->MoveNext();
}

$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");
if ($_GET['action'] == "list") {
	listpage($listtitle,$listsql,$fieldsarray,$filename,$orderby,$sort,$extra);
	echo '<br>&nbsp;&nbsp;<a href="permissiondetail.php?action=list">View/Edit Permission Details</a>';
}
else {
	echo $form->fetch();
}	
include ("footer.php");
?>