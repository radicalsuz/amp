<?php
  require("Connections/freedomrising.php");
?><?php
   $Recordset1=$dbcon->Execute("SELECT name, id from modfields order by name asc") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
?>
<?php include ("header.php");?>

<h2>Input Module Fields</h2>
<form name="form1">
              <table align="center" cellpadding="1" cellspacing="1" width="90%">
                <tr> 
                  <td><b>Title </b></td>
                  <td><b>ID</b></td>
                  <td ><b>edit fields</b></td>
                  <td ><strong>view list</strong></td>
                  <td >add</td>
                </tr>
                <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
                <tr bordercolor="#333333" bgcolor="#CCCCCC"> 
                  <td > <?php echo $Recordset1->Fields("name")?> 
                  <td> <?php echo $Recordset1->Fields("id")?> </td>
                  <td ><a href="modfields2.php?<?php echo "id=".$Recordset1->Fields("id") ?>">edit fields</a></td>
                  <td><a href="moddata_list.php?modin=<?php echo $Recordset1->Fields("id") ?>">view list</a></td>
                  <td><a href="moddata.php?modin=<?php echo $Recordset1->Fields("id") ?>">add</a></td>
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
