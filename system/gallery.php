<?php
/*
$modid = "8";
$mod_name="gallery";

require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
require_once( 'AMP/System/Upload.inc.php');
require_once( 'AMP/Content/Image/Resize.inc.php');

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

    if ( isset ($_FILES['file']['tmp_name']) && file_exists( $_FILES['file']['tmp_name'])){
        $upLoader = &new AMPSystem_Upload( $_FILES['file']['name'] );
        $image_path = AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL ; 

        if ($upLoader->setFolder( $image_path ) && $upLoader->execute( $_FILES['file']['tmp_name'] )) {
            $new_file_name = basename( $upLoader->getTargetPath() ) ;
            $reSizer = &new ContentImage_Resize();
            if ( ! ( $reSizer->setImageFile( $upLoader->getTargetPath() ) && $reSizer->execute() )) {
                $result_message = "Resize failed:<BR>". join( "<BR>", $reSizer->getErrors() ) . $result_message ;
            } 

        } else {
            $result_message =  "File Upload Failed<BR>\n" . join( '<BR>', $upLoader->getErrors() );
        }
    } else {

        if (isset( $_POST['img'])) $new_file_name = $_POST['img'];
    }

	#$img = upload_image('',$getimgset->Fields("optw"),$getimgset->Fields("optl"),$getimgset->Fields("thumb"));

    $MM_editTable  = $table;
    $MM_recordId = $_POST['MM_recordId'];
    $MM_editRedirectUrl = "gallery_list.php";
	$MM_editColumn = "id";
    $MM_fieldsStr = "section|value|new_file_name|value|caption|value|photoby|value|date|value|byemail|value|publish|value|galleryid|value";
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
} else {
	$html .= addfield('file','Upload File <br>','file');
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
*/
?>
