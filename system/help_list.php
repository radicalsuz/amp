<?php
  require("Connections/freedomrising.php");
?><?php
   $Recordset1=$dbcon->Execute("SELECT file1, section, sorder, id from help order by type, sorder asc") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
?><?php include ("header.php");?>

<h2>Help Files</h2>
      <strong><a href="help_edit.php">ADD HELP FILE </a></strong> 
      <form name="form1">
              
        <table align="center" cellpadding="1" cellspacing="1" width="90%">
          <tr> 
            <td><b>File</b></td>
            <td><strong>Section</strong></td>
            <td><strong>Order</strong></td>
            <td><b>ID</b></td>
            <td colspan="2">&nbsp;</td>
          </tr>
          <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
          <tr bordercolor="#333333" bgcolor="#CCCCCC"> 
            <td > <?php echo $Recordset1->Fields("file1")?> 
            <td ><?php echo $Recordset1->Fields("section")?> 
            <td ><?php echo $Recordset1->Fields("sorder")?>
            <td> <?php echo $Recordset1->Fields("id")?> </td>
            <td><a href="help_edit.php?id=<?php echo $Recordset1->Fields("id") ?>">edit</a></td>
          </tr>
          <?php
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>
        </table>
  <p>&nbsp; </p>
</form>
<p> 
  <?php
  $Recordset1->Close();
?><?php include ("footer.php");?>
