<?php
$modid=9;
  require("Connections/freedomrising.php");
  
  if (isset($callemail)) {$emailsql ="where email = '$callemail'";}
?><?php
 if (isset($listid)){
   $Recordset1=$dbcon->Execute("SELECT email.* FROM email, subscription where  email.id=subscription.userid and subscription.listid=$listid  ORDER BY email asc") or DIE($dbcon->ErrorMsg());
   $listname=$dbcon->Execute("SELECT name FROM lists where id= $listid") or DIE($dbcon->ErrorMsg()); 
   }
   else{
   $Recordset1=$dbcon->Execute("SELECT * FROM email $emailsql ORDER BY email asc") or DIE($dbcon->ErrorMsg());
   }
  

?><?php

?><?php include("header.php") ?>
<table width="100%" border="0">
        <tr class="banner"> 
          <td colspan="3">List Subscribers &nbsp;&nbsp;&nbsp;&nbsp;<?php  if (isset($listid)){echo $listname->Fields("name");}?></td>
        </tr>
        <tr class="intitle"> 
          <td>Name </td>
          <td>email</td>
          <td>&nbsp;</td>
        </tr>
        <?php while (!$Recordset1->EOF)
   { 
?>
        <tr bgcolor="#CCCCCC"> 
          <td> <?php echo $Recordset1->Fields("firstname")?> &nbsp; <?php echo $Recordset1->Fields("lastname")?> 
          </td>
          <td> <?php echo $Recordset1->Fields("email")?> </td>
          <td><A HREF="<?php echo "emailedit.php?id=".$Recordset1->Fields("id") ?>">view</A></td>
        </tr>
        <?php

  $Recordset1->MoveNext();
}
?>
      </table>
<?php include("footer.php") ?>
