<?php
$modid=8;
$mod_name="gallery";

require("Connections/freedomrising.php");
if ($_GET['o']){
$order = "ORDER BY  $_GET[o] , g.id  asc";}
else {
$order  =  " ORDER BY gt.galleryname, g.id asc ";}
  
$Recordset1=$dbcon->Execute("SELECT DISTINCT g.season, g.section, g.relsection1, g.relsection2, g.img, g.id, g.publish,  gt.galleryname  From gallery g, gallerytype gt where   g.galleryid=gt.id $order ") or DIE($dbcon->ErrorMsg());
 
?>
<?php include ("header.php"); ?>

<h2> Photos </h2>
<div class="list_table">
      <table class="list_table">
        <tr class="intitle"> 
		  <td>&nbsp;</td>
          <td><b><a href="photo_list.php?o=img" class="intitle">Image</a></b></td>
          <td><a href="photo_list.php?o=id" class="intitle">ID</a></td>
          <td><b><a href="photo_list.php?o=galleryname" class="intitle">Gallery</a></b></td>
		  <td><b><a href="photo_list.php?o=section" class="intitle">Section</a></b></td>
		    <?php if (isset($relsection1id)) {?> 
			<td><b><a href="photo_list.php?o=relsection1" class="intitle"><?php echo $relsection1label ;?></a></b></td>
			<?php }?>
					    <?php if (isset($relsection2id)) {?> 
			<td><b><a href="photo_list.php?o=relsection2" class="intitle"><?php echo $relsection2label ;?></a></b></td>
			<?php }?>
			 <?php if (isset($MM_season)) {?> 
			<td><b><a href="photo_list.php?o=season" class="intitle">Season</a></b></td>
			<?php }?>
          <td><b><a href="photo_list.php?o=publish" class="intitle">Publish</a></b></td>
          <td><b></b></td>
        </tr>
        <?php while  (!$Recordset1->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
		<td> <?php echo "<img src =\"../img/thumb/".$Recordset1->Fields("img")."\">"?> </td>
          <td> <?php echo $Recordset1->Fields("img")?> </td>
          <td><?php echo $Recordset1->Fields("id")?></td>
          <td> <?php echo $Recordset1->Fields("galleryname")?> </td>
		  <td> <?php echo $Recordset1->Fields("section")?> </td>
		  	    <?php if (isset($relsection1id)) {?> 
		    <td> <?php echo $Recordset1->Fields("relsection1")?> </td>
				    <?php } if (isset($relsection2id)) {?> 
			  <td> <?php echo $Recordset1->Fields("relsection2")?> </td>
		
			    <?php } if (isset($MM_season)) {?> 
			  <td> <?php echo $Recordset1->Fields("season")?> </td>
			  <?php }?>
          <td> <?php If (($Recordset1->Fields("publish")) == "1") { echo "live";} ?>  
          </td>
          <td><A HREF="gallery.php?id=<?php echo $Recordset1->Fields("id") ?>">edit</A></td>
        </tr>
        <?php
 
  $Recordset1->MoveNext();
}
?>
      </table></div>
<?php include ("footer.php"); ?>
