<?php
$modid=2;
  require("Connections/freedomrising.php");
?><?php
   $rise=$dbcon->Execute("SELECT * FROM ride ORDER BY lastname ASC") or DIE($dbcon->ErrorMsg()); ?>
<?php include("header.php"); ?>
<h2>Ride Board</h2>
<table border="0" cellspacing="1" cellpadding="0" width="100%">
  <tr class="intitle"> 
    <td><b>name</b></td>
    <td><b>email</b></td>
    <td><b>id</b></td>
    <td><b>publish</b></td>
    <td><b></b></td>
  </tr>
  <?php while (!$rise->EOF)
   { 
?>
  <tr> 
          <td><?php echo $rise->Fields("firstname")?>&nbsp;<?php echo $rise->Fields("lastname")?></td>
    <td> 
      <?php echo $rise->Fields("email")?>
    </td>
    <td> 
      <?php echo $rise->Fields("id")?>
    </td>
    <td> 
      <input <?php If (($rise->Fields("publish")) == "1") { echo "CHECKED";} ?> type="checkbox" name="checkbox" value="checkbox">
    </td>
    <td><A HREF="ride_edit.php?id=<?php echo $rise->Fields("id") ?>">edit</A></td>
  </tr>
  <?php
  
  $rise->MoveNext();
}
?>
</table>

<?php
  $rise->Close();
?>

<?php include("footer.php"); ?>