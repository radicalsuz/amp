<?php
$modid=4;
  require("Connections/freedomrising.php");
?>
<?php
   $hero=$dbcon->Execute("SELECT * FROM heros") or DIE($dbcon->ErrorMsg());
  ?>
<?php include("header.php"); ?>
<h2>Land Use Heros</h2>
      <table width="90%" border="0" cellspacing="0" cellpadding="0" align="center">
        <tr> 
          <td><b>ID</b></td>
          <td><b>Name</b></td>
          <td><b>publish</b></td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$hero->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td><?php echo $hero->Fields("id")?> </td>
          <td> <?php echo $hero->Fields("name")?> </td>
          <td><?php if ($hero->Fields("publish") == 1) {echo "live";} ?> </td>
          <td><A HREF="faq_edit.php?<?php echo $hero->Fields("id") ?>">edit</A></td>
        </tr>
        <?php
   $hero->MoveNext();
}
?>
      </table>
<?php include("footer.php"); ?>
