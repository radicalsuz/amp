<?php
$mod_id = 7;
$modid = 2;
include("AMP/BaseDB.php");
include("AMP/BaseTemplate.php");
include("AMP/BaseModuleIntro.php");  

$have=$dbcon->CacheExecute("SELECT *  FROM userdata  Where custom6 = 'Have a Ride to Offer' and  custom9='1' and modin=10  ORDER BY custom1 ASC");

$need=$dbcon->CacheExecute("SELECT *  FROM userdata  WHERE custom6 = 'Need a Ride' and custom9=1 and modin=10  ORDER BY custom1 ASC");

?> 
<p class="text"><a href="#need">Need a Ride</a> | <a href="#have">Have a Ride 
  to Offer</a><a href="modinput4.php?modin=10"><br>
  Add a Listing on the Ride Board</a> <br>
  <a href="modinput4_login.php?modin=10">Edit Your Ride Board Posting</a></p>
<p class="title"><a name="have"></a>Have a Ride to Offer</p>
<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="boardbg">
  <tr class=board> 
    <td class=board ><b>Departing From</b></td>
    <td class=board><b>Departing Date</b></td>
    <td class=board><b>Returning to</b></td>
    <td class=board><b>Return Date</b></td>
    <td class=board><b># of people</b></td>
    <td class=board ><b>Contact</b></td>
    <td class=board>&nbsp;</td>
  </tr>
<?php
while  ($have && !$have->EOF)    { 
?>
  <tr> 
    <td valign="top" class="text"> <?php echo $have->Fields("custom1")?> 
    </td>
    <td valign="top" class="text"> <?php echo $have->Fields("custom2")?> 
    </td>
    <td valign="top" class="text"> <?php echo $have->Fields("custom3")?> </td>
    <td valign="top" class="text"> <?php echo $have->Fields("custom4")?> </td>
    <td valign="top" class="text"> <?php echo $have->Fields("custom5")?> </td>
    <td valign="top" class="text"> <p> <?php echo $have->Fields("First_Name")?> 
        &nbsp; <?php echo $have->Fields("Last_Name")?> <br>
        <a href="mailto:<?php echo $have->Fields("Email")?>"> 
        <?php echo $have->Fields("Email")?> </a>&nbsp;<br>
        <?php echo $have->Fields("Phone")?> </p></td>
    <td valign="top" class="text">&nbsp; </td>
  </tr>
  <tr> 
    <td valign="top" class="text">&nbsp;</td>
    <td valign="top" class="text" colspan="6"><b>Comments: </b> <?php echo nl2br( $have->Fields("custom8")) ?> 
    </td>
  </tr>
  <tr> 
    <td colspan="7" valign="top" class="board"><img src="img/spacer.gif" height="4"></td>
  </tr>
  <?php
	$have->MoveNext();
}
?>
</table>
<p class="title"><a name="need"></a>Need a Ride</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center" class="boardbg">
  <tr class=board> 
    <td class=board><b>Departing From</b></td>
    <td class=board><b>Departing Date</b></td>
    <td class=board><b>Returning to</b></td>
    <td class=board ><b>Return Date</b></td>
    <td class=board><b># of people</b></td>
    <td class=board><b>Contact</b></td>
    <td class=board>&nbsp;</td>
  </tr>
  <?php
while ($need && !$need->EOF) { 
?>
  <tr> 
    <td valign="top" class="text"> <?php echo $need->Fields("custom1")?> 
    </td>
    <td valign="top" class="text"> <?php echo $need->Fields("custom2")?> 
    </td>
    <td valign="top" class="text"> <?php echo $need->Fields("custom3")?> </td>
    <td valign="top" class="text"> <?php echo $need->Fields("custom4")?> </td>
    <td valign="top" class="text"> <?php echo $need->Fields("custom5")?> </td>
    <td valign="top" class="text"> <p> <?php echo $need->Fields("First_Name")?> 
        &nbsp; <?php echo $need->Fields("Last_Name")?> <br>
        <a href="mailto:<?php echo $need->Fields("Email")?>"> 
        <?php echo $need->Fields("Email")?> </a>&nbsp; <?php echo $need->Fields("Phone")?> 
        <br>
      </p></td>
    <td valign="top" class="text">&nbsp; </td>
  </tr>
  <tr> 
    <td valign="top" class="text">&nbsp;</td>
    <td valign="top" class="text" colspan="6"><b>Comments: </b> <?php echo nl2br( $need->Fields("custom8")) ?></td>
  </tr>
  <tr class=board> 
    <td colspan="7" valign="top" ><img src="img/spacer.gif" width="1" height="4"></td>
  </tr>
  <?php
	$need->MoveNext();
}
?>
</table>
<p>&nbsp;</p>
<?php
include("AMP/BaseFooter.php");?>
