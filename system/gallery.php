<?php
$modid = "8";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;

//SELECT DISTINCT g.season, g.section, g.relsection1, g.relsection2, g.img, g.id, g.publish,  gt.galleryname  From gallery g, gallerytype gt where   g.galleryid=gt.id $order 

$table = "gallery";
$listtitle ="Photo Gallery";
$listsql ="select id,   from $table  ";
$orderby =" order by  asc  ";
$fieldsarray=array( 'Thumbnail'=>'','Image'=>'img','Gallery'=>'','Section'=>'','Status'=>'publish','ID'=>'id'
					);
$filename="gallery.php";

ob_start();
// insert, update, delete
if ((($_POST['MM_update']) && ($_POST['MM_recordId'])) or ($_POST['MM_insert']) or (($_POST['MM_delete']) && ($_POST['MM_recordId']))) {

	$getimgset=$dbcon->Execute("SELECT thumb, optw, optl FROM sysvar where id =1") or DIE($dbcon->ErrorMsg());
if ($_FILES['file']['name']) {
	$img = upload_image('',$getimgset->Fields("optw"),$getimgset->Fields("optl"),$getimgset->Fields("thumb"));
}

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = "gallery_list.php";
	$MM_editColumn = "id";
    $MM_fieldsStr = "section|value|img|value|caption|value|photoby|value|date|value|byemail|value|publish|value|galleryid|value";
    $MM_columnsStr = "section|',none,''|img|',none,''|caption|',none,''|photoby|',none,''|date|',none,''|byemail|',none,''|publish|',none,''|galleryid|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}

if (isset($_GET['id'])) {	$R__MMColParam = $_GET['id']; }
else {$R__MMColParam = "8000000";}

$R=$dbcon->Execute("SELECT * FROM $table WHERE id = $R__MMColParam") or DIE($dbcon->ErrorMsg());
$G=$dbcon->Execute("SELECT id, galleryname FROM gallerytype") or DIE($dbcon->ErrorMsg());

$rec_id = & new Input('hidden', 'MM_recordId', $_GET['id']);
//build form
$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Add/Edit '.$listtitle, 'banner');

if ($_GET['id'])  {
	$html .= $buildform->add_colspan("<div align=center><img src =\"../img/pic/".$R->Fields("img")."\" align=center></div>");
	$html .= addfield('img','Image','text',$R->Fields("img"));
}
elseif ($_GET['p']) {
	$html .= $buildform->add_colspan("<div align=center><img src =\"../img/pic/".$_GET['p']."\" align=center></div>");
	$html .= addfield('img','Image','text',$_GET['p']);
}
else {
	$html .= addfield('file','Uplaod File <br>(jpg files only)','file');
}

$html .= addfield('publish','Publish','checkbox',$R->Fields("publish"),1);

$gal_options = makelistarray($G,'id','galleryname','Select Gallery');
$Gal = & new Select('galleryid',$gal_options,$R->Fields("galleryid"));
$html .=  $buildform->add_row('Photo Gallery', $Gal);

$html .= addfield('caption','Caption','textarea',$R->Fields("caption"));

$html .= addfield('photoby','Photo By','text',$R->Fields("photoby"));
$html .= addfield('date','Date','text',$R->Fields("date"));
$Type = & new Select('section', $obj->select_type_tree2(0),$R->Fields("section"));
$html .=  $buildform->add_row('Section', $Type);

$html .= $buildform->add_content($buildform->add_btn() .'&nbsp;'. $buildform->del_btn().$rec_id->fetch());
$html .= $buildform->end_table();
$form = & new Form('POST', $_SERVER['PHP_SELF'],'','multipart/form-data');
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
