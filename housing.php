<?php
/*********************
05-06-2003  v3.01
Module:  Housing
Description:  display page for housing
CSS: text, form
To Do: 
*********************/ 
$modid = 3;
$intro_id = 8;

include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  


$housing=$dbcon->CacheExecute("SELECT *  FROM userdata  Where  custom1='Have Housing' and custom19 =1 and modin= 11 ORDER BY id DESC") or DIE($dbcon->ErrorMsg());
$nhousing=$dbcon->CacheExecute("SELECT *  FROM userdata  Where   custom1='Need Housing' and custom19 =1 and modin= 11 ORDER BY id DESC") or DIE($dbcon->ErrorMsg());
 ?>

<p class="text"><a href="#have">View Avalible Housing</a> | <a href="#need">View Requested Housing</a><br><a href="modinput4.php?modin=11">Offer/Request  Housing on the Housing Board </a><br>
<a href="modinput4_login.php?modin=11">Update Your Housing Board Listing </a></p>
<p class="title"><a name="have"></a>Have Housing </p>
<?php while (!$housing->EOF)   { ?>
<br>
<table width="100%" border="0" cellpadding="2" bordercolor="#000000"  class=boardbg>
  <tr > 
    <td colspan="5" class="text"><strong>Contact:&nbsp;</strong><?php echo $housing->Fields("First_Name")?>&nbsp;<?php echo $housing->Fields("Last_Name")?>&nbsp;&nbsp;<?php echo $housing->Fields("Company")?>&nbsp;&nbsp;<a href="mailto:<?php echo $housing->Fields("Email")?>"><?php echo $housing->Fields("Email")?></a>&nbsp;&nbsp;<?php echo $housing->Fields("Phone")?> 
    </td>
  </tr>
  <tr > 
    <td colspan="5" class="text"><b>Available:</b>&nbsp;<?php echo $housing->Fields("custom3")?></td>
  </tr>
  <tr  class=board> 
    <td  class=board><b>Location</b></td>
    <td class=board><b>Bus/Metro</b></td>
    <td class=board><b>Parking</b></td>
    <td class=board><b>Meals</b></td>
    <td class=board><b> Accessibility</b></td>
  </tr>
  <tr > 
    <td valign="top" class="text"><?php echo $housing->Fields("custom8")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("custom9")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("custom10")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("custom11")?></td>
    <td valign="top" class="text"> <?php echo $housing->Fields("custom7")?> </td>
  </tr>
  <tr  class=board> 
    <td class=board><b>Beds</b></td>
    <td class=board><b>Floor</b></td>
    <td class=board><b>Tent</b></td>
    <td class=board><b>Smoking</b></td>
    <td class=board><b>Children</b></td>
  </tr>
  <tr > 
    <td valign="top" class="text"><?php echo $housing->Fields("custom4")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("custom5")?></td>
    <td  valign="top" class="text"><?php echo $housing->Fields("custom6")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("custom14")?></td>
    <td valign="top" class="text"><?php echo $housing->Fields("custom13")?></td>
  </tr>
  <tr  class=board> 
    <td colspan="5" class=board><strong>Other Comments</strong></td>
  </tr>
  <tr > 
    <td colspan="5" class="text"><?php echo nl2br( $housing->Fields("custom18")) ?></td>
  </tr>
</table>
<?php  $housing->MoveNext(); }?>
<p class="title"><a name="need"></a>Needs Housing</p>
<?php while (!$nhousing->EOF)   { ?><br>
<table width="100%" border="0" cellpadding="2" bordercolor="#000000"  class=boardbg>
  <tr > 
    <td class="text"><strong>Contact:&nbsp;</strong><?php echo $nhousing->Fields("First_Name")?>&nbsp;<?php echo $nhousing->Fields("Last_Name")?>&nbsp;&nbsp;<?php echo $housing->Fields("Company")?>&nbsp;&nbsp;<a href="mailto:<?php echo $housing->Fields("Email")?>"><?php echo $nhousing->Fields("Email")?></a>&nbsp;&nbsp;<?php echo $nhousing->Fields("Phone")?> 
    </td>
  </tr>
  <tr > 
    <td class="text"><b>Dates Needed :</b>&nbsp;<?php echo $nhousing->Fields("custom16")?></td>
  </tr>
   <tr > 
    <td class="text"><b>Number of People :</b>&nbsp;<?php echo $nhousing->Fields("custom17")?></td>
  </tr>
  <tr  class=board> 
    <td class=board><strong>Other Comments</strong></td>
  </tr>
  <tr > 
    <td class="text"><?php echo nl2br( $nhousing->Fields("custom18")) ?></td>
  </tr>
</table>
<?php  $nhousing->MoveNext(); }?><br><br><br>
<?php 
include("AMP/BaseFooter.php");?>