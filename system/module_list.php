<?php
  require("Connections/freedomrising.php");
?><?php
   $catagory=$dbcon->Execute("SELECT * FROM modules order by name asc") or DIE($dbcon->ErrorMsg());
   $catagory_numRows=0;
   $catagory__totalRows=$catagory->RecordCount();
?><?php
include ("header.php");
?>

<h2><b>Modules</b></h2>
      <table width="100%" border="0" cellspacing="3" cellpadding="0" align="center">
        <tr class="intitle"> 
          <td><b>Module Name</b></td>
          <td><b>id</b></td>
          <td><b>Config</b></td>
          <td>Settings</td>
        </tr>
        <?php while (!$catagory->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $catagory->Fields("name")?> </td>
          <td> <?php echo $catagory->Fields("id")?> </td>
          <td><A HREF="module_edit.php?id=<?php echo $catagory->Fields("id") ?>">edit</A></td>
          <td><A HREF="module_control_list.php?modid=<?php echo $catagory->Fields("id") ?>">edit</A></td>
        </tr>
        <?php

  $catagory->MoveNext();
}
?>
      </table>
<?php
include ("footer.php");
?><?php
  $catagory->Close();
?>
