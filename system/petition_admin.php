<?php

//  phpetition v0.2, An easy to use PHP/MySQL Petition Script
//  Copyright (C) 2001,  Mike Gifford, http://openconcept.ca
//
//  This script is free software; you can redistribute it and/or
//  modify it under the terms of the GNU General Public License
//  as published by the Free Software Foundation; either version 2
//  of the License, or (at your option) any later version.
//
//  This program is distributed in the hope that it will be useful,
//  but WITHOUT ANY WARRANTY; without even the implied warranty of
//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
//  GNU General Public License http://www.gnu.org/copyleft/ for more details. 
//
//  If you distribute this code, please maintain this header.
//  Links are always appreciated!
//
// This file is used to either delete signatures or resend verification emails (Security Issues)
$modid=7;
require("Connections/freedomrising.php");
include("header.php");
//require("$base_path"."/petiton/config.php");

$dbhost = $MM_HOSTNAME;  	//  MySQL server hostname
	$dbuser = $MM_USERNAME;  			//  MySQL server username
	$dbpasswd = $MM_PASSWORD;	            	//  MySQL server password
	$db_name=$MM_DATABASE;			//  MySQL database name
	$db=mysql_connect("$dbhost","$dbuser","$dbpasswd");	//  MySQL server connect
	mysql_select_db($db_name,$db);

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
	<!--
	-->
<?php
echo "<h2>Main Admin</h2>";
//echo "<P><a href=\"$base_url/adminSubmit.php\">Admin Submit</a>";
  
		// Count all verified signatures
		$result = mysql_query("SELECT COUNT(*) FROM phpetition WHERE petid=$pid and Verified='yes'",$db);
		$current_sigs  = ($result>0) ? mysql_result($result,0,0) : 0;
		// Count all signatures
		$result2 = mysql_query("SELECT COUNT(*) FROM phpetition where petid=$pid and Verified='yes' ",$db);
		$current_sigs2  = ($result2>0) ? mysql_result($result2,0,0) : 0;
?>
<table align=center border=0 cellpadding=1 cellspacing=1 width="90%">
	<tr valign="top"><td>
		<?php echo "<br><small><B>Verified: $current_sigs </b></small>";  ?>
	</td><td>
		<?php  echo "<br><small><B>Total: $current_sigs2 </b></small>";  ?>
	</td><td>
		<?php  echo "<br><small><B>UnVerified: " . ($current_sigs2 - $current_sigs) . " / ". number_format (($current_sigs/$current_sigs2*100), 2) .   " %</b></small>";  ?>
	</td></tr>
</table>

<table border="0" cellpadding="0" cellspacing="0">
<tr valign="top">
<td>
<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>
<td align="left">

<?php
$end = ($start+$many);
if(isset($start) && isset($many)){
if ($current_sigs < $end) {
$end=$current_sigs;
}
echo "<b>Most Recent: ".$start." - ". $end ."</b>&nbsp;&nbsp;";
} else {
$start=1;
$many=50;
if ($current_sigs < $many) {
$many=$current_sigs;
}
echo "<b>Most Recent: " . $start . " - " . $many . "</b>&nbsp;&nbsp;";
}

if ($GLOBALS["logic"] == "DESC") {
  $GLOBALS["logic"] = "ASC";
} else {
  $GLOBALS["logic"] = "DESC";
}

?>

</td>

<td align="right">
<table align="center" cellpadding="0" cellspacing="0" border="0"><tr><td>
<font size="2" face="<?php echo $font_face;?>"><font <?php echo $signed_font_main; ?>>
<form method=get action="<?php echo $PHP_SELF; ?>">
<input type="hidden" name="pid" value="<?php echo $pid; ?>">
<input type="hidden" name="start" value="<?php echo ($start-$many); ?>">
<input type="hidden" name="many" value="<?php echo $many; ?>">
<input type="hidden" name="firstUpdate" value="<?php echo $firstUpdate; ?>">
<input type="submit" value=" << "></form> 
</font></font>
</td><td>
<font size="2" face="<?php echo $font_face;?>"><font <?php echo $signed_font_main; ?>>
<form method=get action="<?php echo $PHP_SELF; ?>">
<input type="hidden" name="pid" value="<?php echo $pid; ?>">
<input type="hidden" name="firstUpdate" value="<?php echo $firstUpdate; ?>">
Start with: 
<input type="text" name="start" size="2" value="<?php echo $start;?>"> How Many: <input type="text" name="many" size="3" value="<?php echo $many;?>"> <input type="submit" value=" Go "></form> 
</font></font>
</td><td>
<font size="2" face="<?php echo $font_face;?>"><font <?php echo $signed_font_main; ?>>
<form method=get action="<?php echo $PHP_SELF; ?>">
<input type="hidden" name="pid" value="<?php echo $pid; ?>">
<input type="hidden" name="start" value="<?php echo ($start+$many); ?>">
<input type="hidden" name="many" value="<?php echo $many; ?>">
<input type="hidden" name="firstUpdate" value="<?php echo $firstUpdate; ?>">
<input type="submit" value=" >> "></form> 
</font></font>
</td><td>
<font size="2" face="<?php echo $font_face;?>"><font <?php echo $signed_font_main; ?>>
<form method=get action="<?php echo $PHP_SELF; ?>">
<input type="hidden" name="pid" value="<?php echo $pid; ?>">
<input type="hidden" name="start" value="<?php echo $start; ?>">
<input type="hidden" name="many" value="<?php echo $many; ?>">
<input type="hidden" name="firstUpdate" value="yes">
<input type="submit" value=" Update "></form> 
</font></font>
</td><td>
<font size="2" face="<?php echo $font_face;?>"><font <?php echo $signed_font_main; ?>>
<form method=get action="<?php echo $PHP_SELF; ?>">
<input type="hidden" name="pid" value="<?php echo $pid; ?>">
<input type="hidden" name="start" value="<?php echo $start; ?>">
<input type="hidden" name="many" value="<?php echo $many; ?>">
<input type="hidden" name="firstUpdate" value="yes">
<input type="hidden" name="reminderOne" value="yes">
<input type="submit" value=" Reminder One "></form> 
</font></font>
</td></tr></table>
</td></tr></table>

<center><form METHOD="get" ACTION="<?php echo $PHP_SELF; ?>">

<table align="center" width="100%" cellpadding="4" cellspacing="0" border="0">
<input type="hidden" name="pid" value="<?php echo $pid; ?>">
<input type="hidden" name="start" value="<?php echo $start; ?>">
<input type="hidden" name="many" value="<?php echo $many; ?>">

<tr bgcolor="#FF0000">
<td width="1%" align="center">Send Reminder</td>

<td width="1%" align="center"><b><a STYLE="color:white; text-decoration:none" href="<?php echo $PHP_SELF; ?>?pid=<?php echo $pid ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=ID&logic=<?php echo $GLOBALS["logic"]; ?>">#</a></b></td>
<td align="left"><b><a STYLE="color:white; text-decoration:none" href="<?php echo $PHP_SELF; ?>?pid=<?php echo $pid ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=LastName&logic=<?php echo $GLOBALS["logic"]; ?>">Name</a></b></td>
<td align="left"><b><a STYLE="color:white; text-decoration:none" href="<?php echo $PHP_SELF; ?>?pid=<?php echo $pid ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=Email&logic=<?php echo $GLOBALS["logic"]; ?>">Email</a></b></td>
<td align="left"><b><a STYLE="color:white; text-decoration:none" href="<?php echo $PHP_SELF; ?>?pid=<?php echo $pid ?>&start=<?php echo $start; ?>&many=<?php echo $many ?>&orderby=Date&logic=<?php echo $GLOBALS["logic"]; ?>">Date Submitted</a></b></td>
<td width="1%" align="center">Delete</td>
</tr>

		<?php
			if(!$start){
			$start=0;
		} else {
			$start--;
		}
		if(!$many){
			$many=50;
		}
		if(!$orderby){
			$orderby="ID";
		}
		
		$q = " SELECT * FROM phpetition where petid=$pid ";
		if ($reminderOne=='yes') {
			$q .= " and Verified!='yes'   ";
			if(!$days) $days = '7';
			$q .= "AND TO_DAYS(NOW()) - TO_DAYS(Date) <= $days ";
			$q .= " AND TO_DAYS(NOW()) - TO_DAYS(Date) >= 2 ";
			$q .= " AND verifyDate IS NULL ";


		}
		$q .= " ORDER BY $orderby " . $GLOBALS["logic"];
		$q .= " LIMIT $start,$many";
		
		echo $q;
		$get_sites=mysql_query("$q",$db);

while($get_rows=mysql_fetch_array($get_sites)){
	$ID=$get_rows[ID];
	$Name = $get_rows[FirstName] . " " . $get_rows[LastName];
	$Email = $get_rows[Email];
	$Country=$get_rows[Country];
	$Date=$get_rows[Date];
	$cc++;
	$cell_color = "white";
	$cc % 2  ? 0 : $cell_color = "D0D0D0";
	$Verified = $get_rows[Verified];
	if ($Verified!='yes') {
		$cell_color="yellow";
	}
	echo "\n<tr bgcolor=\"".$cell_color."\">";
	if ($Verified!='yes') {
		if ($firstUpdate) {
			echo "<td valign=top align=center><small>&nbsp;<input TYPE=checkbox value=$ID NAME=Remind$ID CHECKED></small></td>";
			//echo "<td valign=top align=center><small>&nbsp;<input TYPE=checkbox value=$ID NAME=Letter$ID></small></td>";
		} else {
			echo "<td valign=top align=center><small>&nbsp;<input TYPE=checkbox value=$ID NAME=Remind$ID></small></td>";
			//echo "<td valign=top align=center><small>&nbsp;<input TYPE=checkbox value=$ID NAME=Letter$ID></small></td>";
		}
	} else {
	echo "<td valign=top align=center><small>&nbsp;<input TYPE=checkbox value=$ID NAME=Remind$ID></small></td>";
	//echo "<td valign=top align=center><small>&nbsp;<input TYPE=checkbox value=$ID NAME=Letter$ID></small></td>";
	}
	
	echo "<td valign=top align=center><small>&nbsp;$ID</small></td>";
	echo "<td valign=top align=left><small>".stripslashes($Name)."</small></td>";
	echo "<td valign=top align=left><small>".stripslashes($Email)."</small></td>";
	echo "<td valign=top align=left><small>".$Date."&nbsp;</small></td>";
	echo "<td valign=top align=center><small>&nbsp;<input TYPE=checkbox value=$ID NAME=Delete$ID></small></td>";
	echo "</tr>";
}		
 echo "</td></tr></table>";

echo "<input TYPE=\"SUBMIT\" VALUE=\"Submit!\"></form></center>";


while(list($k,$v)=each($HTTP_GET_VARS)) {
	$k=ereg_replace( "[0-9]", "", $k);
	if($k=='Remind') {
		$admin2_query = mysql_query("SELECT * FROM phpetition WHERE petid=$pid and ID='$v'",$db);
		$admin2_array = mysql_fetch_array($admin2_query);
		if (!$admin2_array["verifyDate"]) {
			$Email = $admin2_array["Email"];
			$confirm_email_message = "Hello " . stripslashes($admin2_array["FirstName"]) . ",\nOn " . $admin2_array["Date"] . "  someone used this email address: \n\t$Email\nFor the following online petition:\n\t$base_url/\nAccording to our records this email address has not yet been confirmed.  	Clicking on the link below will confirm that your email address is valid and that you endorse the petition regarding $header_title\n\n\t $base_url/verify.php?ID=" . $admin2_array["ID"] . "&Verified=" . $admin2_array["Verified"] . "\n\n";
			echo "<br>Reminder sent to: " . $admin2_array["ID"] . " : " . $Email;
			$headers .= "From: $owner_name <$owner_email>\n"; 
			$headers .= "X-Mailer: PHP\n"; 				// mailer
			$headers .= "X-Priority: 3\n"; 				// Urgent message!
			$headers .= "Return-Path: <$owner_email>\n";  	// Return path for errors
			mail(stripslashes($admin2_array["FirstName"]) . " " . stripslashes($admin2_array["FirstLast"]) . "<" . $Email . ">", "Please Verify Signature for $header_title", $confirm_email_message, $headers); 
			$update=mysql_query("UPDATE phpetition SET verifyDate=SYSDATE() WHERE ID='$v'",$db);
		}
		
	} else if ($k=='Letter') {
		// People should only be notified of an appeal letter once.  
		// These should only be sent to folks who are willing to be contacted.
	
		// The query I'm having expressing in MySQL logic is a selection in which the results will check the phpetition table for ID & Contact info, but also verify that the indID in PETletters table has not been used to send this letterID.  Any suggestions?
	
		$q = "
		SELECT phpetition.*,  PETletters.*
		FROM 
			phpetition, PETletters
		WHERE 
			phpetition.ID='$v' AND phpetition.Contact = 'on' AND phpetition.Verified = 'yes' AND phpetition.petid =$pid (PETletters.indID='$v' AND PETletters.letterID='$letterID')";
		$admin3_query = mysql_query("$q",$db);
		$admin3_array = mysql_fetch_array($admin3_query);
		
		if ($admin3_array["ID"]) {
			$Email = $admin3_array["Email"];
			$randPassword = randomPassword(8);
			$insert=mysql_query("INSERT PETletters SET indID='$v', letterID=$letterID, randPassword=$randPassword, outreachDate=SYSDATE()  ",$db);
			
			$letter_email_message = "Hello " . stripslashes($admin3_array["FirstName"]) . ",\nSomeone signed and verified $header_title on " . $admin3_array["Date"] . "  using this email address: \n\t$Email\nWe are encouraging people who have signed this appeal and who have indicated that they wanted to be contacted in the future to consider sending a letter to individuals we hope to influence and inform about this pressing concern.  Clicking on the link below will confirm that your email address is valid and that you endorse the petition regarding $header_title\n\n\t $base_url/letters.php?indID=$v&letterID=$letterID&randomPassword=$randPassword\n\nTo view the comments of people who have signed online see:\n\t$base_url/signed.php\n";
			echo "<br>Reminder sent to: " . $admin3_array["ID"] . " : " . $Email;
			$headers .= "From: $owner_name <$owner_email>\n"; 
			$headers .= "X-Mailer: PHP\n"; 				// mailer
			$headers .= "X-Priority: 3\n"; 				// Urgent message!
			$headers .= "Return-Path: <$owner_email>\n";  	// Return path for errors
			mail(stripslashes($admin3_array["FirstName"]) . " " . stripslashes($admin3_array["FirstLast"]) . "<" . $Email . ">", "Outreach Letter: $header_title", $confirm_email_message, $headers); 
		}
	} else if ($k=='Delete') {
		$adminDelete = mysql_query("DELETE FROM phpetition WHERE ID='$v'",$db);
		echo "<br>Deleting email: $v";
	} else {
				echo $k . " " . $v;
	}
	// $update=mysql_query("UPDATE dbrss SET superRSS='yes' WHERE ID='$v'",$db)
}
?>
    <P>
      <!-- <?php  echo $script_display; ?> -->
  