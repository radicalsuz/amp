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
//  Links are always appreciated!]
//
// This file is used to alert your friends

//require("config.php");

$alert_text .= "<P><center>";
$alert_text .= "<form method=post action=\"". $PHP_SELF . "?id=$id\">";

$alert_text .= "<TABLE bgcolor=\"$lightBoxColor\" CELLPADDING=1 CELLSPACING=0 BORDER=1>";
// $alert_text .= "<TR><TD valign=top>";
//	if (file_exists("lang/support.$lang.txt")) readfile( "lang/support.$lang.txt" );  
//	else readfile( "lang/support.eng.txt" );  
// $alert_text .= "</TD>";

$alert_text .= "<TD><div align=center><strong>Invite others to join the appeal</strong></div>";
$alert_text .= "<TABLE CELLPADDING=1 CELLSPACING=0 BORDER=0><TR><TD ALIGN=CENTER colspan=2><strong>Who should this be sent from?</strong></TD></TR>";
$alert_text .= "<TR><TD ALIGN=CENTER colspan=2><INPUT TYPE=text NAME=SenderName VALUE=\"";
if ($Email) {
  if (!$SenderName) {
  $alert_text .= stripslashes($FirstName) . " " . stripslashes($LastName); 
  }
} elseif ($SenderEmail) { 
$alert_text .=  stripslashes($SenderName);
} else {
$alert_text .=  "Your_Name";
}
$alert_text .= "\"size=23 MAXLENGTH=60><br>";
$alert_text .= "<INPUT TYPE=text NAME=SenderEmail VALUE=\"";
if ($Email) {
$alert_text .= $Email;
} elseif ($SenderEmail) { 
$alert_text .= $SenderEmail;
} else {
$alert_text .= "Your_Email_Address";
}
$alert_text .= "\"size=23 MAXLENGTH=60></TD></TR>";


$alert_text .= "<TR><TD ALIGN=CENTER colspan=2><strong>Send to the following:</strong></TD></TR>";

$alert_text .= "<TR><TD ALIGN=CENTER COLSPAN=2>";
$alert_text .= "<INPUT TYPE=text NAME=EMAIL1 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL2 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL3 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL4 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL5 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL6 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL7 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL8 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL9 size=23 MAXLENGTH=60>";
$alert_text .= "<br><INPUT TYPE=text NAME=EMAIL10 size=23 MAXLENGTH=60>";
$alert_text .= "<br><input name=submit type=submit value=\" Invite Others \">";
$alert_text .= "</TD></TR></TABLE>";
$alert_text .= "</TD></TR></TABLE>";
$alert_text .= "<input type=hidden name=alert value=alert>";
$alert_text .= "</form><P>";

echo $alert_text;

if($alert) {
	// added elsewhere.
	// require("functions.php");
	$total_email_array = array ($EMAIL1, $EMAIL2, $EMAIL3, $EMAIL4, $EMAIL5, $EMAIL6, $EMAIL7, $EMAIL8, $EMAIL9, $EMAIL10);
	if (($SenderEmail=="Your_Email_Address") OR ($SenderEmail=="")) {
		echo "Error, you have not provided your email address";
	} else {
		if(!validEmail($SenderEmail)) {
			if (file_exists("lang/emails.$lang.php")) include( "lang/emails.$lang.php" );  
			else include( "lang/emails.eng.php" );  
			$sentFrom = "From:" . stripslashes($SenderName) . " <" . $SenderEmail. ">\n"; 
			$headers .= "X-Mailer: PHP\n"; 				// mailer
			$headers .= "X-Priority: 1\n"; 				// Urgent message!
			$headers .= "Return-Path: <$SenderEmail>\n";  	// Return path for errors
			echo "<P><strong>Thanks for sending this message to these friends:</strong><br>";
		
			function print_email ($item) {
				global $db;
				if($item) { 
					global $header_title, $email_message, $headers, $sentFrom;
					if(!validEmail($item)) {
						$petitionID="$id";
						$sql = "SELECT reciever FROM petalert WHERE reciever='$item' AND petitionID='$petitionID'";
						$same_Email_query = mysql_numrows(mysql_query($sql));
						if($same_Email_query!=0){
						echo  "We have already sent an alert to this email:  \"$item\"<br>\n";
						} else {
							echo $item . " &nbsp; ";
							$sender=$SenderEmail;
							$petitionID="$id";
							$reciever=$item;
							$sql = "INSERT INTO petalert (petitionID,sender,reciever,date) VALUES ('$petitionID', '$sender', '$reciever', SYSDATE())";
							$insert = mysql_query($sql,$db);
							// Uncomment for  internal SMTP client
							$headers = $sentFrom . $headers;
							mail($item , $header_title, $email_message, $headers); 
						
							// Uncomment for External SMTP Client 
							//externalSMTP($item, $sentFrom, $header_title, $headers, $email_message);
						}
					}
				}
			}
		}
	
		array_walk ($total_email_array, 'print_email');
		reset ($total_email_array);
	}
}
?>

</center>