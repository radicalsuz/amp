<?php
$modid=1;
  require("Connections/freedomrising.php");
?>

<?php
   $Recordset1=$dbcon->Execute("SELECT * FROM eventtype ORDER BY name ASC") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?>

<?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
?>


<?php include ("header.php");?>

<h2>Edit Calendar Type</h2>
<table width="90%" border="0" cellspacing="2" cellpadding="3" align="center">
              <tr class="intitle"> 
                <td>Name</td>
                <td>ID</td>
                <td>Edit</td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
  <tr bgcolor="#CCCCCC"> 
    <td> 
      <?php echo $Recordset1->Fields("name")?>
    </td>
    <td> 
      <?php echo $Recordset1->Fields("id")?>
    </td>
    <td><A HREF="calendar_type.php?id=<?php echo $Recordset1->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>
</table>

      <p><a href="calendar_type.php">Add Calendar Type</a></p>
<?php include ("footer.php");?>
<?php
  $Recordset1->Close();
?>
