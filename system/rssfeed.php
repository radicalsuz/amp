<?php
#generic update page
$modid = "45";
$mod_name = "rss";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;


$table = "rssfeed";
$listtitle ="RSS Feeds";
$listsql ="select id, title title from $table  ";
$orderby =" order by id desc  ";
$fieldsarray=array('ID'=>'id',
					'Title'=>'title');
$filename="rssfeed.php";


ob_start();
// insert, update, delete
if ((($_POST[MM_update]) && ($_POST[MM_recordId])) or ($_POST[MM_insert]) or (($_POST[MM_delete]) && ($_POST[MM_recordId]))) {
    $MM_editTable  = $table;
    $MM_recordId = $_POST[MM_recordId];
	$MM_editColumn = "id";  
    $MM_editRedirectUrl = $filename."?action=list";
	if ($_POST["class"]) {$sqlwhere = " class = ".$_POST["class"]." ";}
	if ($_POST["type"]) {$sqlwhere = "( type = ".$_POST["type"]." OR typeid = ".$_POST["type"].") ";}
	if (($_POST["class"]) && ($_POST["type"])) {$sqlwhere = " class = ".$_POST["class"]." and type = ".$_POST["type"]." ";}

	$MM_fieldsStr = "title|value|description|value|sqllimit|value|orderbysql|value|orderbyorder|value|sqlwhere|value";
    $MM_columnsStr = "title|',none,''|description|',none,''|sqllimit|',none,''|orderbysql|',none,''|orderbyorder|',none,''|sqlwhere|',none,''";//|$delim,$altVal,$emptyVal|  |',none,''|
	require ("../Connections/insetstuff.php");
	require ("../Connections/dataactions.php");
ob_end_flush();	
}

// build sql
if (isset($_GET["id"])) {	$R__MMColParam = $_GET["id"]; }
else {$R__MMColParam = "8000000";}
$R=$dbcon->Execute("SELECT * FROM rssfeed WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$classsql=$dbcon->Execute("SELECT id, class FROM class order by class asc") or DIE($dbcon->ErrorMsg());
$section=$dbcon->Execute("SELECT id, type FROM articletype order by type asc") or DIE($dbcon->ErrorMsg());

//declare form objects
$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);

//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');
$html .= addfield('title','Feed Title','text',$R->Fields("title"));
$html .= addfield('description','Feed Description','textarea',$R->Fields("description"));
$html .= $buildform->add_header('Feed Conditions');


$c_options = makelistarray($classsql,'id','class','Select Class');
$csel = & new Select('class',$c_options);
$html .=  $buildform->add_row('Feed from content in this class', $csel);


$Type = & new Select('type', $obj->select_type_tree2(0));
$html .=  $buildform->add_row('Feed from content in this section', $Type);

$html .= addfield('sqlwhere','Where Statement','textarea',$R->Fields("sqlwhere"));

$order2_options = array(''=>'','date'=>'Date','updated'=>'Date Updated','id'=>'ID','title'=>'Title');
$order2 = & new Select('orderbysql',$order2_options,$R->Fields("orderbysql"));
$html .=  $buildform->add_row('Order By', $order2);

$order_options = array(''=>'','asc'=>'asc','desc'=>'desc');
$order = & new Select('orderbyorder',$order_options,$R->Fields("orderbyorder"));
$html .=  $buildform->add_row('Order By', $order);

$html .= addfield('sqllimit','Number of Items','text',$R->Fields("sqllimit"));

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
