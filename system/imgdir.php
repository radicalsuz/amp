<?php
$mod_name="content";
require("Connections/freedomrising.php");

if (isset($_REQUEST['actdel'])){
	$dir_name1="".$base_path_amp."img/thumb/";
	$dir_name2="".$$base_path_amp."img/pic/";
	$dir_name3="".$base_path_amp."img/original/";
	unlink($dir_name1.$_REQUEST['actdel']);
	unlink($dir_name2.$_REQUEST['actdel']);
	unlink($dir_name3.$_REQUEST['actdel']);
}
$dir_name= $base_path_amp."img/thumb";  
$dir = opendir($dir_name);
$basename = basename($dir_name);
$fileArr = array();

while ($file_name = readdir($dir))
{
	if (($file_name !=".") && ($file_name != "..")) {
		#Get file modification date...
		$fName = "$dir_name/$file_name";
		$fTime = filemtime($fName);
		$fileArr[$file_name] = $fTime;    
	}
}

# Use arsort to get most recent first
# and asort to get oldest first
arsort($fileArr);
$numberOfFiles = sizeOf($fileArr);

include("header.php"); 
?>
<h2><?php echo helpme(""); ?>Images</h2>
      <P><a href="gallery_list.php">view image gallery</a></P>
 
<div class='list_table'> 	<table class='list_table'>
	<tr class="intitle">
<td>Thumbnail</td>
	<td >File Name</td>	
		<td>Date</td>
			<td>ID</td>
		<td>Gallery</td>
		<td>Section</td>
	<?php if (isset($relsection1id)) {?> 
			<td><?php echo $relsection1label ;?></td>
			<?php }?>
					    <?php if (isset($relsection2id)) {?> 
			<td><?php echo $relsection2label ;?></td>
			<?php }
			if (isset($MM_season)) {	?>
		<td>Season</td><?php }?>
		<td>Delete</td>
		
		</tr><?php

for($t=0;$t<$numberOfFiles;$t++)	{
	$thisFile = each($fileArr);
    $thisName = addslashes($thisFile[0]);
	$getgal = $dbcon->Execute("SELECT  g.season, g.section, g.relsection1, g.relsection2, g.img, g.id, g.publish,  gt.galleryname  From gallery g, gallerytype gt where   g.galleryid=gt.id and g.img = '".$thisName."'  ") ;
	$getimgset=$dbcon->Execute("SELECT thumb, optw, optl FROM sysvar where id =1") or DIE($dbcon->ErrorMsg());
	$tsize=$getimgset->Fields("thumb");
    $thisTime = $thisFile[1];
    $thisTime = date("m/d/y", $thisTime);
?>

	<tr bgcolor="#CCCCCC">
<td><a href="../img/original/<?php echo $thisName ?>"><img src="../img/thumb/<?php echo $thisName ?>" width="<?php echo $tsize ?>" border=0></a></td>
	<td ><b><?php echo $thisName ?></b></td>	
		<td><?php echo $thisTime ?></td>
		<td><?php if ($getgal->Fields("id")) {echo "<a href=\"gallery.php?id=".$getgal->Fields("id")."\">".$getgal->Fields("id")."</a>"; } else {echo "<font size = -2>non gallery (<A href=\"gallery.php?p=".$thisName."\">add</a>)</font>";}   ?></td>
		  <td> <?php echo $getgal->Fields("galleryname")?> </td>
		  <td> <?php echo $getgal->Fields("section")?> </td>
		  	    <?php if (isset($relsection1id)) {?> 
		    <td> <?php echo $getgal->Fields("relsection1")?> </td>
				    <?php } if (isset($relsection2id)) {?> 
			  <td> <?php echo $getgal->Fields("relsection2")?> </td>
			<?php }
			if (isset($MM_season)) {	?>
		<td><?php echo $getgal->Fields("season"); ?></td>	  <?php }?>
		<td><a href="imgdir.php?actdel=<?php echo $thisName ?>">delete</a></td>
		
		</tr>
<?php
}
closedir ($dir);?>
  </table></div>
<?php  include("footer.php");
?>
