<?php
$modid = "";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

$table = "users";
$listtitle ="Users";
$listsql ="select u.id, u.name, p.name as perm   from $table u left join per_group p on  u.permission = p.id	 ";
$orderby =" order by name asc  ";
$fieldsarray=array( 'User'=>'name','Permission Group'=>'perm');
$filename="user.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = $filename."?action=list";
	$MM_editColumn = "id";
   $MM_fieldsStr = "name|value|passwordx|value|userlevel|value|email|value|system_home|value|system_allow_only|value";
   $MM_columnsStr = "name|',none,''|password|',none,''|permission|none,none,NULL|email|',none,''|system_home|',none,''|system_allow_only|',none,''|";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$P=$dbcon->Execute("SELECT id, name FROM per_group") or DIE($dbcon->ErrorMsg());

$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');

$html .= addfield('name','Name','text',$R->Fields("name"));
$html .= addfield('passwordx','Password','text',$R->Fields("password"));

$per_options = makelistarray($P,'id','name','Select Permission Group');
$Per = & new Select('userlevel',$per_options,$R->Fields("permission"));
$html .=  $buildform->add_row('Access Level', $Per);

$html .= addfield('email','Email','text',$R->Fields("email"));
$html .= addfield('system_home','System Home','text',$R->Fields("system_home"));
$html .= addfield('system_allow_only','system allowed pages:<br>separate with commas','textarea',$R->Fields("system_allow_only"));

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