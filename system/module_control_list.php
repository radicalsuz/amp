<?php
  require("Connections/freedomrising.php");
?><?php
if (isset($modid)){
   $catagory=$dbcon->Execute("SELECT module_control.*, modules.name  FROM module_control,  modules where module_control.modid = modules.id and module_control.modid=$modid  order by name asc") or DIE($dbcon->ErrorMsg());
  
   $text=$dbcon->Execute("SELECT name, title, id  FROM moduletext where modid = $modid order by name asc") or DIE($dbcon->ErrorMsg());
   
      $nav=$dbcon->Execute("SELECT name,  id  FROM navtbl where modid = $modid order by name asc") or DIE($dbcon->ErrorMsg());
   $name=$dbcon->Execute("SELECT name, userdatamod, userdatamodid  FROM modules where id = $modid") or DIE($dbcon->ErrorMsg());
   }
   else   {
   $catagory=$dbcon->Execute("SELECT module_control.*, modules.name  FROM module_control,  modules where module_control.modid = modules.id  order by name asc") or DIE($dbcon->ErrorMsg());}

?><?php
include ("header.php");
?>
<h2><b><?php echo $name->Fields("name")?>&nbsp;Settings</b></h2>
      <?php if ((isset($modid)) && $name->Fields("userdatamod") == 1 ){ ?>
      <strong>&nbsp;<a href="modfields2.php?id=<?php echo $name->Fields("userdatamodid") ?>" class="header">Edit 
      Fields and Settings</a></strong><br>
      <br> 
      <?php  }  ?>


      <?php	  if (isset($modid)){?>
      <table width="100%" border="0" cellspacing="3" cellpadding="0" align="center">
        <tr class="intitle">
          <td colspan="5"><strong><font size="3">Module Pages</font></strong></td>
        </tr>
		<tr class="intitle"> 
          <td><b>Module Pages</b></td>
          <td>Title</td>
          <td>id</td>
          <td><b>Navigation</b></td>
          <td><b>Header <br>
            Text</b></td>
        </tr>
        <?php while (!$text->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td><?php echo $text->Fields("name")?> </td>
          <td><?php echo $text->Fields("title")?></td>
          <td><?php echo $text->Fields("id")?></td>
          <td><A HREF="module_nav_edit.php?id=<?php echo $text->Fields("id") ?>">edit</A> </td>
          <td><A HREF="moduletext_edit.php?id=<?php echo $text->Fields("id") ?>">edit</A></td>
        </tr>
        <?php

  $text->MoveNext();
}
?>
      </table>
	  <?php } ?>
      <p>
        <?php	  if (isset($modid)){?>
      </p>
      <table width="100%" border="0" cellspacing="3" cellpadding="0" align="center">
        <tr class="intitle"> 
          <td colspan="3"><strong><font size="3">Related Navigation Files</font></strong></td>
        </tr>
        <tr class="intitle"> 
          <td><b>Navigation File</b></td>
          <td>id</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$nav->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td><?php echo $nav->Fields("name")?> </td>
          <td><?php echo $nav->Fields("id")?></td>
          <td><A HREF="nav_edit.php?id=<?php echo $nav->Fields("id") ?>">edit</A></td>
        </tr>
        <?php

  $nav->MoveNext();
}
?>
      </table>&nbsp;&nbsp;<a href="nav_minedit.php" class="header">Add Basic Navigation File</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a href="nav_edit.php" class="header">Add Dynamic Navigation File</a><br>
      <?php } ?>
      <br>
      <table width="100%" border="0" cellspacing="3" cellpadding="0" align="center">
        <tr class="intitle">
          <td colspan="5"><strong><font size="3">Module Controls</font></strong></td>
        </tr>
		<tr class="intitle"> 
          <td><b>Module</b></td>
          <td>Control Name</td>
          <td>value</td>
          <td><b>id</b></td>
          <td><b></b></td>
        </tr>
        <?php while (!$catagory->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td><?php echo $catagory->Fields("name")?> </td>
          <td><?php echo $catagory->Fields("description")?></td>
          <td><?php echo  htmlspecialchars($catagory->Fields("setting"))?></td>
          <td> <?php echo $catagory->Fields("id")?> </td>
          <td><A HREF="module_control_edit.php?id=<?php echo $catagory->Fields("id") ?>">edit</A></td>
        </tr>
        <?php

  $catagory->MoveNext();
}
?>
      </table>
	  &nbsp;&nbsp;<a href="module_control_edit.php" class="header">Add Module 
      Control</a><br>
      <br>
      <?php include ("footer.php"); ?>
