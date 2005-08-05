<?php
$mod_name="content";
require_once("Connections/freedomrising.php");
require_once("Connections/sysmenu.class.php");
$obj = new SysMenu; 
$buildform = new BuildForm;


include ("header.php");

$getimgset=$dbcon->Execute("SELECT thumb, optw, optl FROM sysvar where id =1") or DIE($dbcon->ErrorMsg());
if ($_FILES['file']['name']) {
	$imnae2 = upload_image($_POST['newname'],$getimgset->Fields("optw"),$getimgset->Fields("optl"),$getimgset->Fields("thumb"));
}

ob_start();
// insert, update, delete
if ($_POST['add'] ) {
	$MM_insert=$_REQUEST['MM_insert'];
    $MM_editTable  = "gallery";
    $MM_fieldsStr = "section|value|imnae2|value|caption|value|photoby|value|date|value|byemail|value|publish|value|galleryid|value";
    $MM_columnsStr = "section|',none,''|img|',none,''|caption|',none,''|photoby|',none,''|date|',none,''|byemail|',none,''|publish|',none,''|galleryid|',none,''";
	require ("../Connections/insetstuff.php");
    require ("../Connections/dataactions.php");
    ob_end_flush();	
}




$G=$dbcon->Execute("SELECT id, galleryname FROM gallerytype") or DIE($dbcon->ErrorMsg());


$html  = $buildform->start_table('name');
$html .= $buildform->add_header('Photo Gallery Settings');
$html .= addfield('add','Add to Gallery','checkbox');
$html .= addfield('publish','Publish','checkbox');

$gal_options = makelistarray($G,'id','galleryname','Select Gallery');
$Gal = & new Select('galleryid',$gal_options);
$html .=  $buildform->add_row('Photo Gallery', $Gal);

$html .= addfield('caption','Caption','textarea');

$Type = & new Select('section', $obj->select_type_tree2(0));
$html .=  $buildform->add_row('Section', $Type);

$html .= addfield('photoby','Photo By','text');
$html .= addfield('date','Date','text');
$html .= $buildform->end_table();




echo "<h2>Image Upload</h2> ";

if ($response) {
	echo '<br>'.$response.'<br><br><br>'; 
}	
	?>
<p><strong>Upload .JPG Image Files Only (<a href="imgother_upload.php">click here</a> for other formats, <a href="crop_step1.php" target="_blank">click here</a> to upload a .JPG you want to crop) </strong></p>
<form method="POST" action="<?php echo $MM_editAction ?>" enctype="multipart/form-data">
<?php
echo $buildform->start_table('name');
echo $buildform->add_header('Upload Image');
?>
	<tr>
		<td>File:</td>
		<td><input type=file name=file size=25></td>
	</tr>
	<tr>
		<td>New File Name</td>
		<td><input type=text name=newname size=20>&nbsp;&nbsp;no extension</td>
	</tr>
<?php
echo $buildform->end_table();
?>

 <input name="MM_insert" type="submit" value="Upload File">
  <br>
  <br>        


<?php

echo $html;
echo "</form>";

include ("footer.php");

?>
