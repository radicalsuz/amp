<?php
     
  
  require_once("Connections/freedomrising.php");  

?><?php
   $Recordset1=$dbcon->Execute("SELECT id, title FROM source ORDER BY title ASC") or DIE($dbcon->ErrorMsg());
   $Recordset1_numRows=0;
   $Recordset1__totalRows=$Recordset1->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $Recordset1_numRows = $Recordset1_numRows + $Repeat1__numRows;
?>
<?php include ("header.php"); ?>

<h2>All Sources</h2>
<table width="75%" border="0" cellspacing="5" cellpadding="0" align="center">
  <tr> 
    <td class="toplinks">ID #</td>
    <td class="toplinks">Name</td>
    <td class="toplinks">edit</td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$Recordset1->EOF)) 
   { 
?>
  <tr class="results"> 
    <td> 
      <?php echo $Recordset1->Fields("id")?>
    </td>
    <td> 
      <?php echo $Recordset1->Fields("title")?>
    </td>
    <td><A HREF="admin_source.php?<?php echo $MM_keepNone.(($MM_keepNone!="")?"&":"")."id=".$Recordset1->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  $Repeat1__index++;
  $Recordset1->MoveNext();
}
?>
</table>
</body>
</html>
<?php
  $Recordset1->Close();
?>
<?php include ("footer.php");?>
