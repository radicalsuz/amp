<?php

$modid=38;
require_once("Connections/freedomrising.php");

if (!$MM_Message && AMP_HOSTED) {
	#$index_usr_sql="Select id, system_home from users where name = 'me'";#.$dbcon->qstr($_SERVER['REMOTE_USER']);
	$index_user_settings = $dbcon->GetAssoc("Select id, system_home from users where name = ".$dbcon->qstr($_SERVER['REMOTE_USER']));
	if (isset($index_user_settings['system_home'])&&$index_user_settings['system_home']!='') {
		header('Location: '.$index_user_settings['system_home']);
	} else {
		header('Location: articlelist.php');		
		
	}
    
} else {

    $new=$dbcon->Execute("SELECT * from message, users where message.toid = users.id and users.name=".$dbcon->qstr($_SERVER['REMOTE_USER'])." order by date desc")
            or die("Couldn't find any messages: " . $dbcon->ErrorMsg());
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
