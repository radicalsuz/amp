<?php
$mod_name="module";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

function add_content($title=NULL,$shortdes=NULL,$type=1,$class=1,$link=NULL) {
	global $dbcon;
	$sql ="insert into articles (publish,uselink,linkover,title,shortdesc,type,class,link) values ('1','1','1','".addslashes($title)."','".addslashes($shortdesc)."','".$type."','".$class."','".addslashes($link)."')";
	$dbcon->Execute($sql)or DIE("Could not insert content Error: ".$sql.$dbcon->ErrorMsg());
}

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {
	add_content($_POST['title'],$_POST['shortdesc'],$_POST['type'],$_POST['class'],$_POST['link']);
	
	redirect("article_list.php?type=".$_POST['type']);
    ob_end_flush();	
}

//set for module pages
if (isset($_GET['mod_id'])) {
	$R=$dbcon->Execute("SELECT id,modid,title,type,searchtype FROM moduletext WHERE id = ".$_GET['mod_id']) or DIE("could not get module info error:".$dbcon->ErrorMsg());
	$modid = $R->Fields("modid");
	$title = $R->Fields("title");
	$link = $R->Fields("searchtype");
}
if (isset($_GET['pid'])) {
	$R=$dbcon->Execute("SELECT title, shortdesc FROM petition where id = ".$_GET['pid']) or DIE($dbcon->ErrorMsg());
	$redirect = 'petition.php?action=list';
	$title = $R->Fields("title");
	$shortdesc = $R->Fields("shortdesc");
	$link = 'petition.php?pid='.$_GET['pid'];
	$classid = 5;
}
if (isset($_GET['action'])) {
	$R=$dbcon->Execute("SELECT title, shortdesc FROM action_text WHERE id = ".$_GET['action']) or DIE($dbcon->ErrorMsg());
	$redirect = 'sendfax_list.php';
	$title = $R->Fields("title");
	$shortdesc = $R->Fields("shortdesc");
	$link = 'action.php?action='.$_GET['action'];
	$classid = 5;
}

if (isset($_GET['gallery'])) {
	$R=$dbcon->Execute("SELECT galleryname, description FROM gallerytype WHERE id = ".$_GET['gallery']) or DIE($dbcon->ErrorMsg());
	$redirect = 'gallery_type.php?action=list';
	$title = 'Photo Gallery: '.$R->Fields("galleryname");
	$shortdesc = $R->Fields("description");
	$link = 'gallery.php?gal='.$_GET['gallery'];
	$classid = 1;
}
$C=$dbcon->Execute("SELECT * from class order by class") or DIE($dbcon->ErrorMsg());
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add to Content System', 'banner');
if ($link == 'article.php') {$link = NULL;}
if (!$link) {
	$html .= $buildform->add_colspan("<b>This page does not have a link associated with it.  Please add a link below or the page will not link.</b>");
}
$html .= addfield('redirect','','hidden',$redirect);

$html .= addfield('title','Title','text',$title);
$html .= addfield('shortdesc','Short Description','textarea',$shortdesc);
$Type = & new Select('type', $obj->select_type_tree2(0));
$html .=  $buildform->add_row('Section', $Type);
$class_options = makelistarray($C,'id','class','Select Class');
$Class = & new Select('class',$class_options,$classid);
$html .=  $buildform->add_row('Class', $Class);
$html .= addfield('link','Link','text',$link);
$html .= $buildform->add_content($buildform->add_btn());
$html .= $buildform->end_table();
$form = & new Form();
$form->set_contents($html);

include ("header.php");
echo $form->fetch();
include ("footer.php");
?>