<?php

//  phpetition v0.3, An easy to use PHP/MySQL Petition Script
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

// This file is used to verify email addresses.
// If individuals have not recieved or are not sure if they have signed they can
// also enter in their email address for confirmation.

require("config.php");
require("functions.php");
if (isset($ID)){
$id=$ID;}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html>
<head>
	<meta name="author" content="<?php echo $author; ?>"> 
	<meta name="KeyWords" content="<?php echo $header_keywords; ?>"> 
	<meta name="Description" content="<?php echo $header_description; ?>"> 
	<meta name="GENERATOR" content="Bluefish 0.6 (X11; U; Linux 2.2.14 i686)">
	<title>Verification: <?php echo $title; ?></title>
	<style type="text/css">
	<!--
	-->
	</style>
</head>
<body bgcolor="#FFFFFF" onload="">
<a name="top"></a> 

<?php
if($PID){

	$count = 0;
	do { /* loop until a valid result or failure count exceeds threshold. */
		$verify_query = mysql_query("SELECT * FROM phpetition WHERE ID=PID");
		if (!$verify_query) {
			error_log("ID: $PID, attempt no. $count error: " . mysql_error(), 0);
		} else {
			break;
		}
		$count++;
	} while (!$verify_query and $count < 10);
	if ($verify_query) {
		$verify_array = mysql_fetch_array($verify_query);
	} else {
		echo "could not verify, server likely too busy, please try again later.";
		error_log("failure threshold exceeded ID: $PID", 0);
	}

	// $verify_query = mysql_query("SELECT * FROM phpetition WHERE ID=$PID");
	// $verify_array = mysql_fetch_array($verify_query);
	$db_Verify = $verify_array["Verified"];
	if ($db_Verify=='yes') {
		echo "This email has already been verified.  this can happen if you have double-clicked on the URL that was sent to you.  <P><a href=\"$base_url/signed.php?id=$id\">To see other verified signatures.</a>";
	} else {
		if ($db_Verify -= $Verified) {
			echo "This is an invalid confirmation.  Please go <a href=\"$base_url\">back</a> and try again.";
		} else {
			$FirstName  = $verify_array["FirstName"];
			$LastName = $verify_array["LastName"];
			$Email = $verify_array["Email"];	
			$Address = $verify_array["Address"];	
			$CityState = $verify_array["CityState"];	
			$PostalCode = $verify_array["PostalCode"];	
			$Comments = $verify_array["Comments"];	
			$Contact = $verify_array["Contact"];	
			$Public = $verify_array["Public"];	
			
			$count = 0;
			do { /* loop until a valid result or failure count exceeds threshold. */
				$update=mysql_query("UPDATE phpetition SET Verified='yes', confirmDate = SYSDATE() WHERE ID='$PID'",$db);
				if (!$update) {
					error_log("ID: $PID, attempt no. $count error: " . mysql_error(), 0);
				} else {
					break;
				}
				$count++;
			} while (!$update and $count < 10);
			if ($update) {
				echo "<!-- Successful -->";
			} else {
				echo "could not verify, server likely too busy, please try again later.";
				error_log("failure threshold exceeded ID: $PID", 0);
			}
			// $update=mysql_query("UPDATE phpetition SET Verified='yes', confirmDate = SYSDATE() WHERE ID='$PID'",$db);
?>

<table BORDER=0 CELLSPACING=2 CELLPADDING=2 WIDTH="100%" NOSAVE>
<tr>
<td ALIGN=RIGHT ><strong>First Name:</strong></td>
<td><?php echo $FirstName; ?></td>
</tr>

<tr>
<td align=right><strong>Last Name:</strong></td>
<td><?php echo $LastName; ?></td>
</tr>

<tr>
<td ALIGN=RIGHT><strong>Email Address:</strong></td>
<td><?php echo $Email; ?></td>
</tr>

<?php if ($Address) { ?>
<tr>
<td ALIGN=RIGHT ><strong>Address:</strong> </td>
<td><?php echo $Address; ?></td>
</tr>
<?php 
} // End of if ($Address)
if ($CityState) { ?>
<tr>
<td ALIGN=RIGHT ><strong>City, Province/State:</strong></td>
<td><?php echo $CityState; ?></td>
</tr>
<?php 
}  // End of if ($CityState)
if ($PostalCode) { ?>
<tr>
<td ALIGN=RIGHT><strong>Postal Code:</strong></td>
<td><?php echo $PostalCode; ?></td>
</tr>
<?php 
} // End of if ($PostalCode)
if ($Comments) { ?>
<tr>
<td align=right><strong>Comments:</strong></td>
<td><?php echo $Comments; ?></td>
</tr>
<?php  } // End of if ($Comments)   ?>

<tr>
<td colspan=2 align=center>
<?php
				echo  "<br><strong>Confirmation: Your name has been added to $header_title.</strong>";
			if ($Contact) { 
				echo "<p>We also appreciate that you have given us permission to contact you in the future.";
			} // End of if ($Contact)
			if ($Public) { 
				echo "<br>Listing your name in public helps to boost this appeal's credibility.";
			} // End of if ($Public)
			echo "</td></tr></table>";
		} // End of else ($db_Verify -= $Verified)
	} // End of if ($db_Verify=='yes')
} else {  // No ID
	visitorVerification ($Email);
} 
echo "<center><p>To go back to the <a href=\"$base_url/index.php?id=$id\">main page</a>.</center> ";
echo "<center><P><a href=\"signed.php?id=$id#namelist\">View other signatures</a>.</center>";
?>

</body>
</html>

<?php  include("$base_path"."footer.php");

echo "<!-- $script_display -->";
?>