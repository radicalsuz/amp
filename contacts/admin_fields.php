<?php
     
  
  require_once("Connections/freedomrising.php");  

?><?php
   $Recordset1=$dbcon->Execute("SELECT id, name FROM contacts_fields ORDER BY camid, fieldorder") or DIE($dbcon->ErrorMsg());
?>
<?php include ("header.php"); ?>
<h2>All Fields </h2>
<table width="75%" border="0" cellspacing="5" cellpadding="0" align="center">
  <tr> 
    <td class="toplinks">ID #</td>
    <td class="toplinks">Name</td>
    <td class="toplinks">edit</td>
  </tr>
  <?php while (!$Recordset1->EOF)
   { 
?>
  <tr> 
    <td class="results"> 
      <?php echo $Recordset1->Fields("id")?>
    </td>
    <td class="title"> 
      <?php echo $Recordset1->Fields("name")?>
    </td>
    <td class="title"><A HREF="admin_fieldsedit.php?id=<?php echo $Recordset1->Fields("id"); ?>">edit</A></td>
  </tr>
  <?php
$Recordset1->MoveNext();
}
?>
</table>

<?php include ("footer.php");?>
