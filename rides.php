<?php
#$modinid=52;
$mod_id = 63;
$modid = 100;
ob_start();
include("AMP/BaseDB.php"); 
include("AMP/BaseTemplate.php"); 
include("AMP/BaseModuleIntro.php");
include("dropdown.php"); 
$board="ride";
$confirmcolor="#F389A9";
$unconfirmcolor="#FFFFBA";

?>
<?php
	if ($uid) {  
		$user=$dbcon->Execute("SELECT * from $board where uniqueid=".$dbcon->qstr($uid)) or DIE($dbcon->ErrorMsg());
		$thestate = $user->Fields("state");
		$theuser = $user->Fields("uniqueid");
	}
	

if (!($uid)) {

header ("Location: ".$website."ride_signin.php");

} else { ?>

<div id="showrides">
<p class="text"><a href="ride_add_offer.php">
  Add a Listing on the Ride Board</a> <br>
  <a href="ride_signin.php?deluid=<?php echo $theuser;?>">Remove My Posting From Ride Board</a><br>
  <a href="ride_add_offer.php?uid=<?php echo $theuser;?>">Edit My Posting</a><P>

<?php 
	if (($user->Fields("need")=="have") || ($user->Fields("need")=="org")) {
	$message1= "Below is a listing of riders who have requested a ride with you.  You may confirm their place in your vehicle by selecting the appropriate rider(s) and clicking confirm.  You will receive an e-mail notification and must respond to it for final confirmation.  Please notify riders who you are not able to accomodate.";
	$message2= "No riders have requested a ride at this time";
	$sql = "SELECT *  FROM $board  Where (need='need') and  publish='1' and DriverID='$uid' ORDER BY confirmed DESC, depatingfrom ASC";
	#echo $sql;
	$button_name="Confirm Riders";
	$radio_name="confirmed";
	$list_action="confirmrides";
	$title_text="Riders who have Requested a Ride";
	} else {
		if ($user->fields("Confirmed")>0) {
		$message1= "Your driver information is as follows:<P>";
		$sql = "SELECT * FROM $board WHERE uniqueid='".$theuser."'";
		$button_name="";
		$radio_name="havedriver";
		$list_action="none";
	} else {
		$message1= "You may now review rides available from your area.  To request a ride, just select one of the drivers listed below and click 'Request Ride'.";
		$message2 = "No rides are currently available from your state - please check back again soon";
		$sql = "SELECT *  FROM $board  Where (need='have' or need='org') and  publish='1' and state='$thestate' ORDER BY depatingfrom ASC";
		$button_name="Request Ride";
		$radio_name="setdriver";
		$list_action="requestride";
		$title_text="Available Rides from ".$thestate;
	}
}
$have=$dbcon->CacheExecute($sql) or DIE($dbcon->ErrorMsg());
$have__totalRows=$have->RecordCount();
#echo $have__totalRows;
#echo "/".$have->RecordCount();
if ($have__totalRows>0) {
	echo $message1."<P>";
?>
	
	<p class="title">
	<a name="have"></a><?php echo $title_text;?></p>
	<form name="selectride" action="ride_signin.php" method="GET">
	<table width="100%" border="0" cellspacing="0" cellpadding="2" align="center" class="boardbg">
		<tr class=board> 
		<td class=board valign=top><b>Select</b></td>
		<td class=board valign=top bgcolor=<?php echo $unconfirmcolor;?>><b>Departing From</b></td>
		<td class=board valign=top bgcolor=<?php echo $unconfirmcolor;?>><b>Departing Date</b></td>
	    <td class=board valign=top bgcolor=<?php echo $unconfirmcolor;?>><b>Returning to</b></td>
		 <td class=board valign=top bgcolor=<?php echo $unconfirmcolor;?>><b>Return Date</b></td>
		 <td class=board valign=top bgcolor=<?php echo $unconfirmcolor;?>><b># of people</b></td>
		<td class=board valign=top bgcolor=<?php echo $unconfirmcolor;?>><b>Contact</b></td>
		</tr>
<?php while ((!$have->EOF)) 
	 { 
?>
  <tr> 
     <td valign="top" class="text" align=center style="border-right:1px solid silver;"> <?php 
		switch ($radio_name) {
			case "confirmed":
				echo "<input type=\"radio\" name=\"".$radio_name.$have->Fields("uniqueid")."\"";
				if ($have->Fields("confirmed")) { 
					echo " value=\"1\" CHECKED>";
					$myrowcolor=$confirmcolor;	
				} else {
					echo ">";
					$myrowcolor=$unconfirmcolor;
				}
				
				break;
			case "setdriver" :
				echo "<input type=\"radio\" name= \"".$radio_name."\" value=\"".$have->Fields("uniqueid")."\">";
				$myrowcolor=$unconfirmcolor;
				break;
			case "havedriver" :
				echo "Ride Confirmed";
				$myrowcolor=$confirmcolor;
				break;
		}?></td>
    <td valign="top" class="text" bgcolor=<?php echo $myrowcolor;?>> <?php echo $have->Fields("depatingfrom")?> 
    </td>
    <td valign="top" class="text" bgcolor=<?php echo $myrowcolor;?>> <?php echo $have->Fields("depaturedate")?> 
    </td>
    <td valign="top" class="text"  bgcolor=<?php echo $myrowcolor;?>><?php echo $have->Fields("returningto")?> </td>
    <td valign="top" class="text" bgcolor=<?php echo $myrowcolor;?>><?php echo $have->Fields("returndate")?> </td>
    <td valign="top" class="text" bgcolor=<?php echo $myrowcolor;?>><?php echo $have->Fields("numpeople")?> </td>
    <td valign="top" class="text" bgcolor=<?php echo $myrowcolor;?>> <p> <?php echo $have->Fields("firstname")?> 
        &nbsp; <?php echo $have->Fields("lastname")?> <br>
        <a href="mailto:<?php echo $have->Fields("email")?>"> 
        <?php echo $have->Fields("email")?> </a>&nbsp;<br>
        </td>
    <td valign="top" class="text"  bgcolor=<?php echo $myrowcolor;?>>&nbsp; </td>
  </tr>
  <tr> 
    <td valign="top" class="text" style="border-right: 1px solid silver;">&nbsp;</td>
    <td valign="top" class="text" colspan="7" bgcolor=<?php echo $myrowcolor;?>><b>Comments: </b> <?php echo nl2br( $have->Fields("commets")) ?> 
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
<?php
	if ($button_name<>"") {
	?>
	<center><input type="submit" name="submit" value="<?php echo $button_name;?>">
	<input type="hidden" name="uid" value="<?php echo $theuser;?>">
	<input type="hidden" name ="action" value="<?php echo $list_action;?>">
	<p>&nbsp;</p>
<?php } ?>
</div>
<?php
}else{
	echo $message2; 
}  $have->Close();
 } 
 ob_end_flush();
 include("AMP/BaseFooter.php"); ?>