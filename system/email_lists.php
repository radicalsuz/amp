<?php
$modid=9;
  require("Connections/freedomrising.php");
?><?php
 
   $Recordset1=$dbcon->Execute("SELECT * FROM lists Order by name asc") or DIE($dbcon->ErrorMsg());
 
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
?><?php $MM_paramName = ""; ?><?php include("header.php") ?>
<table width="100%" border="0">
        <tr class="banner"> 
          <td colspan="6">Email Lists</td>
        </tr>
        <tr class="intitle"> 
          <td>List</td>
          <td>subscribers</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   {
    $listnum=$Recordset1->Fields("id");
$Recordset2=$dbcon->Execute("SELECT id FROM subscription where listid = $listnum") or DIE($dbcon->ErrorMsg());
?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $Recordset1->Fields("name")?></td>
          <td><?php echo $Recordset2->RecordCount(); ?> </td>
          <td><a href="email_listsedit.php?id=<?php echo $Recordset1->Fields("id"); ?>">edit</a></td>
          <td><a href="mailblast.php?id=<?php echo $Recordset1->Fields("id"); ?> ">send email</a></td>
          <td><A HREF="<?php echo "email_list.php?listid=".$Recordset1->Fields("id") ?>">view 
            subscribers</A></td>
          <td><a href="<?php echo "email_export.php?id=".$Recordset1->Fields("id") ?>">export</a></td>
        </tr>
        <?php
		$Recordset2->Close();
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>
      </table>
	  <br>
      <a href="mailblast.php?id=9000">Send to all Subscribers</a> 
      <form action="email_list.php" method="post" class="name">
        Search for Email<br>
        <input name="callemail" type="text" class="name" >
        <input type="submit" value="Search" class="name">
      </form>
      <?php include("footer.php") ?><?php
  $Recordset1->Close();
?>
