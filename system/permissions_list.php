<?php
  require("Connections/freedomrising.php");
?>
<?php
   $username=$dbcon->Execute("SELECT * FROM per_group") or DIE($dbcon->ErrorMsg());
   $username_numRows=0;
   $username__totalRows=$username->RecordCount();

   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $username_numRows = $username_numRows + $Repeat1__numRows;
?>
  <?php include ("header.php"); ?>

<h2><?php echo helpme(""); ?>Permission Groups</h2>
            <p></p> <p><a href="permissions.php">Add a Permission Group </a></p>
            <table border="0" cellspacing="0" cellpadding="0" width="90%" align="center">
              <tr> 
                <td><b>permission</b></td>
                <td>&nbsp;</td>
              </tr>
              <?php while (($Repeat1__numRows-- != 0) && (!$username->EOF)) 
   { 
?>
              <tr> 
                <td> <?php echo $username->Fields("name")?> </td>
                <td><A HREF="permissions.php?<?php echo "id=".$username->Fields("id") ?>">edit</A></td>
              </tr>
              <?php
  $Repeat1__index++;
  $username->MoveNext();
}
?>
            </table>
    <p><a href="permissiondetail.php?action=list">View Permission Details</a></p>
<?php include("footer.php"); ?>
