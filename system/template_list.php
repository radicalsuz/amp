<?php
  require("Connections/freedomrising.php");
?><?php

$sql="SELECT id, name FROM template ORDER BY name ASC"; 
   $Recordset1=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
?>

<?php include("header.php"); ?>
      <table width="98%" border="0" align="center">
        <tr class="banner"> 
          <td colspan="3"><b>Design Templates</b></td>
        </tr>
        <tr> 
          <td><b>ID</b></td>
          <td><b>Template</b></td>
          <td>&nbsp;</td>
        </tr>
        <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $Recordset1->Fields("id")?> </td>
          <td> <?php echo $Recordset1->Fields("name")?> </td>
          <td><A HREF="<?php if (isset($nons)){ echo "nav_minedit.php?goto=1&id=".$Recordset1->Fields("id")."";}
		   else { echo "template_edit3.php?id=".$Recordset1->Fields("id")."";}?>">edit</A></td>
        </tr>
        <?php
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>
      </table>
            <p>
              <?php
  $Recordset1->Close();
?>
            </p>
<?php include("footer.php"); ?>
