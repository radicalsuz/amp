<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  display page for housing
CSS: text, form
To Do: 
*********************/ 
$modid = 3;
$mod_id = 8;
include("sysfiles.php");
include("header.php"); 
include("dropdown.php"); 
?><?php
   $housing=$dbcon->CacheExecute("SELECT *  FROM housing  Where publish='1'and board='2' ORDER BY timestamp DESC") or DIE($dbcon->ErrorMsg());
   $housing_numRows=0;
   $housing__totalRows=$housing->RecordCount();
?><?php
   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $housing_numRows = $housing_numRows + $Repeat1__numRows;
?>
 

<p class="text"><a href="housing_add.php">Add Listing on Housing Board</a> <br>
  <a href="housing_remove.php">Remove Your Listing from the Housing Board</a></p>

<?php while (($Repeat1__numRows-- != 0) && (!$housing->EOF)) 
   { 
?>
<br>
<table width="100%" border="0" cellpadding="2" bordercolor="#000000" bgcolor="#CCCCCC">
  <tr bgcolor="#CCCCCC"> 
    <td colspan="5" class="text"><strong>Contact:&nbsp;</strong><?php echo $housing->Fields("firstname")?>&nbsp;<?php echo $housing->Fields("lastname")?>&nbsp;&nbsp;<?php echo $housing->Fields("org")?>&nbsp;&nbsp;<a href="mailto:<?php echo $housing->Fields("email")?>"><?php echo $housing->Fields("email")?></a>&nbsp;&nbsp;<?php echo $housing->Fields("phone")?> 
    </td>
  </tr>
  <tr bgcolor="#CCCCCC"> 
    <td colspan="5" class="text"><b>Available:</b>&nbsp;<?php echo $housing->Fields("avalible")?></td>
  </tr>
  <tr bgcolor="#006666"> 
    <td class="form"><font color="#FFFFFF"><b>Location</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Bus/Metro</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Parking</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Meals</b></font></td>
    <td class="form"><font color="#FFFFFF"><b> Accessibility</b></font></td>
  </tr>
  <tr bgcolor="#CCCCCC"> 
    <td valign="top" class="text"><?php echo $housing->Fields("location")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("transport")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("parking")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("cooking")?></td>
    <td valign="top" class="text"> <?php echo $housing->Fields("access")?> </td>
  </tr>
  <tr bgcolor="#006666"> 
    <td class="form"><font color="#FFFFFF"><b>Beds</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Floor</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Tent</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Smoking</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Children</b></font></td>
  </tr>
  <tr bgcolor="#CCCCCC"> 
    <td valign="top" class="text"><?php echo $housing->Fields("beds")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("floor")?></td>
    <td  valign="top" class="text"><?php echo $housing->Fields("tents")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("smoking")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("children")?></td>
  </tr>
  <tr bgcolor="#006666"> 
    <td colspan="5" class="text"><strong><font color="#FFFFFF">Other Comments</font></strong></td>
  </tr>
  <tr bgcolor="#CCCCCC"> 
    <td colspan="5" class="text"><?php echo nl2br( $housing->Fields("info")) ?></td>
  </tr>
</table>
<?php
  $Repeat1__index++;
  $housing->MoveNext();
}
?>
<p>&nbsp;</p>
<?php
  $housing->Close();
?><?php 
include("footer.php");?>

