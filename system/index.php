<?php
$modid=38;
require("Connections/freedomrising.php");

if (!$MM_Message) {header ("Location: amp_alerts.php");}

else {
$new=$dbcon->Execute("SELECT * from message where toid = $ID order by date desc") or DIE($dbcon->ErrorMsg());
  ?>
  
<?php  include ("header.php");?>
<h2>Your Messages</h2>
      <table width="90%" border="0" cellspacing="2" cellpadding="3" align="center">
        <tr class="intitle"> 
          <td>From</td>
          <td>Message</td>
          <td>Phone</td>
          <td>Email</td>
          <td>Date</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$new->EOF)
   { 

?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $new->Fields("messagefrom")?> </td>
          <td><?php echo $new->Fields("message")?> </td>
          <td><?php echo $new->Fields("phone")?></td>
          <td><?php echo $new->Fields("email")?></td>
          <td><?php echo dodatetime($new->Fields("date"),("n/j/y h:m"))?></td>
          <td><A HREF="message.php?id=<?php echo $new->Fields("id") ?>">mark as read/delete</A></td>
        </tr>
        <?php
  
  $new->MoveNext();
}
?>
      </table>
 <?php  include ("footer.php"); }
?>