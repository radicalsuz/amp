<?php
$mod_id = 7;
$modid = 2;
include("sysfiles.php");
include("header.php"); 
include("dropdown.php"); 


   $have=$dbcon->CacheExecute("SELECT *  FROM ride  Where need='have'and  publish='1' and board='2' ORDER BY depatingfrom ASC") or DIE($dbcon->ErrorMsg());
   $have_numRows=0;
   $have__totalRows=$have->RecordCount();

   $need=$dbcon->CacheExecute("SELECT *  FROM ride  WHERE need = 'need' and publish=1 and board=2 ORDER BY depatingfrom ASC") or DIE($dbcon->ErrorMsg());
   $need_numRows=0;
   $need__totalRows=$need->RecordCount();

   $Repeat1__numRows = -1;
   $Repeat1__index= 0;
   $have_numRows = $have_numRows + $Repeat1__numRows;

   $Repeat2__numRows = -1;
   $Repeat2__index= 0;
   $need_numRows = $need_numRows + $Repeat2__numRows;
?> 
<p class="text"><a href="#need">Need a Ride</a> | <a href="#have">Have a Ride 
  to Offer</a><a href="ride_add.php"><br>
  Add a Listing on the Ride Board</a> <br>
  <a href="ride_remove.php">Remove Posting From Ride Board</a></p>
<p class="title"><a name="have"></a>Have a Ride to Offer</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr bgcolor="#006666"> 
    <td class="form"><font color="#FFFFFF"><b>Departing From</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Departing Date</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Returning to</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Return Date</b></font></td>
    <td class="form"><font color="#FFFFFF"><b># of people</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Contact</b></font></td>
    <td class="form"><font color="#FFFFFF">&nbsp;</font></td>
  </tr>
  <?php while (($Repeat1__numRows-- != 0) && (!$have->EOF)) 
   { 
?>
  <tr> 
    <td valign="top" class="text"> <?php echo $have->Fields("depatingfrom")?> 
    </td>
    <td valign="top" class="text"> <?php echo $have->Fields("depaturedate")?> 
    </td>
    <td valign="top" class="text"> <?php echo $have->Fields("returningto")?> </td>
    <td valign="top" class="text"> <?php echo $have->Fields("returndate")?> </td>
    <td valign="top" class="text"> <?php echo $have->Fields("numpeople")?> </td>
    <td valign="top" class="text"> <p> <?php echo $have->Fields("firstname")?> 
        &nbsp; <?php echo $have->Fields("lastname")?> <br>
        <a href="mailto:<?php echo $have->Fields("email")?>"> 
        <?php echo $have->Fields("email")?> </a>&nbsp;<br>
        <?php echo $have->Fields("phone")?> </p></td>
    <td valign="top" class="text">&nbsp; </td>
  </tr>
  <tr> 
    <td valign="top" class="text">&nbsp;</td>
    <td valign="top" class="text" colspan="6"><b>Comments: </b> <?php echo nl2br( $have->Fields("commets")) ?> 
    </td>
  </tr>
  <tr> 
    <td colspan="7" valign="top" bgcolor="#006666" class="text"><img src="img/spacer.gif" height="4"></td>
  </tr>
  <?php
  $Repeat1__index++;
  $have->MoveNext();
}
?>
</table>
<p class="title"><a name="need"></a>Need a Ride</p>
<table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr bgcolor="#006666"> 
    <td class="form"><font color="#FFFFFF"><b>Departing From</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Departing Date</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Returning to</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Return Date</b></font></td>
    <td class="form"><font color="#FFFFFF"><b># of people</b></font></td>
    <td class="form"><font color="#FFFFFF"><b>Contact</b></font></td>
    <td class="form"><font color="#FFFFFF">&nbsp;</font></td>
  </tr>
  <?php while (($Repeat2__numRows-- != 0) && (!$need->EOF)) 
   { 
?>
  <tr> 
    <td valign="top" class="text"> <?php echo $need->Fields("depatingfrom")?> 
    </td>
    <td valign="top" class="text"> <?php echo $need->Fields("depaturedate")?> 
    </td>
    <td valign="top" class="text"> <?php echo $need->Fields("returningto")?> </td>
    <td valign="top" class="text"> <?php echo $need->Fields("returndate")?> </td>
    <td valign="top" class="text"> <?php echo $need->Fields("numpeople")?> </td>
    <td valign="top" class="text"> <p> <?php echo $need->Fields("firstname")?> 
        &nbsp; <?php echo $need->Fields("lastname")?> <br>
        <a href="mailto:<?php echo $need->Fields("email")?>"> 
        <?php echo $need->Fields("email")?> </a>&nbsp; <?php echo $need->Fields("phone")?> 
        <br>
      </p></td>
    <td valign="top" class="text">&nbsp; </td>
  </tr>
  <tr> 
    <td valign="top" class="text">&nbsp;</td>
    <td valign="top" class="text" colspan="6"><b>Comments: </b> <?php echo nl2br( $need->Fields("commets")) ?></td>
  </tr>
  <tr bgcolor="#006666"> 
    <td colspan="7" valign="top" class="text"><img src="img/spacer.gif" width="1" height="4"></td>
  </tr>
  <?php
  $Repeat2__index++;
  $need->MoveNext();
}
?>
</table>
<p>&nbsp;</p>
<?php
  $have->Close();

  $need->Close();
 include("footer.php"); ?>