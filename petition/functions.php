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

echo "<!-- START: functions.php -->\n";

function validEmail($Email) { 
	global $error,$error_html;
//$error = 0; return;
	// Decides if the email address is valid. Checks syntax and MX records,
	// for total smartass value. Returns "valid", "invalid-mx" or 
	// "invalid-form".
   
	// Validates the email address. I guess it works. *shrug*
	if (eregi("^[0-9a-z]([-_.]?[0-9a-z])*@[0-9a-z]([-.]?[0-9a-z])*\\.[a-z]{2,3}$",$Email, $check)) {
		if ( checkdnsrr(substr(strstr($check[0], '@'), 1), "ANY") ) { 
			$error = 0; 
			// echo "This email format and dns is correct";
		} else { 
			$error = 1;
			$error_html .= "This email address is invalid " . $Email . ", because it has an inacurate DNS Record.<br>";
        } 
     } else {
		$error = 1;
		$error_html .= "This email address is invalid " . $Email . "<br>";
     }
}

function visitorVerification ($Email) {
	global $db, $base_url, $header_title,$verifySubmit, $verify_box_text, $verify_status,$boxBGcolor,$boxFontColor,$PHP_SELF;
		$vreturn =  "<!-- functions.php - Visitor Verification -->\n";
		$vreturn .=  "<a name=verify></a>\n";
		$vreturn .= "<form method=post action=\"" . $PHP_SELF . "#verify\">\n";
		$vreturn .=  "<TABLE BGCOLOR=\"$boxBGcolor \" CELLPADDING=1 CELLSPACING=0 BORDER=1><TR><TD>\n";
		$vreturn .= "<TABLE BGCOLOR=\"$boxBGcolor \" CELLPADDING=1 CELLSPACING=0 BORDER=0><TR>\n";
	if (!$Email) {
		$vreturn .= "<TD COLSPAN=2 ALIGN=CENTER><B><font color=\"$boxFontColor\">$verify_box_text</font></B></TD>\n";
		$vreturn .= "</TR><TR>\n";
		$vreturn .= "<TD ALIGN=CENTER><INPUT TYPE=text NAME=Email size=23 MAXLENGTH=60></TD>\n";
		$vreturn .= "</TR><TR>\n";
		$vreturn .= "<TD ALIGN=CENTER><input type=hidden name=verifySubmit  value=\"verifySubmit\"><input name=submit type=submit value=\" $verify_status \"></TD>\n";
	} else { // There is an Email
		if ($verifySubmit) {
			$verify2_query = mysql_query("SELECT * FROM phpetition WHERE Email='$Email'",$db);
			$same_Email_query = mysql_numrows($verify2_query);
			if($same_Email_query != '1') { 
				$vreturn .= "<TD ALIGN=CENTER><font color=\"$boxFontColor\">This email \"" . $Email . "\" has not yet been used to sign this petition.</font>\n</TD>\n";
			} else {
				$verify2_array = mysql_fetch_array($verify2_query);
				$db_Verify = $verify2_array["Verified"];
				if($db_Verify=='yes') {
					$vreturn .= "<TD ALIGN=CENTER><font color=\"$boxFontColor\">This email \"" . $Email . "\" has been used and verified to sign this petition.</font>\n</TD>\n";
				} else {
					$vreturn .= "<TD ALIGN=CENTER><font color=\"$boxFontColor\">This email \"" . $Email . "\" has been submitted but not verified.  An verification email is being re-sent now.</font>\n</TD>\n";
					$Verified = $db_Verify;
					$ID = $verify2_array["ID"];
					$confirm_email_message = "Hello,\nTo finish signing the appeal regarding $title, please point your Web browser to the URL below. This will confirm that your email address is valid and that you endorse the petition.\n\n\t $base_url/verify.php?ID=$ID&Verified=$Verified\n\n If you did not sign this appeal, but would like more information, please go to this URL:\n\n\t $base_url/";
					$headers .= "From: " . stripslashes($FirstName) . stripslashes($LastName) . "<$Email>\n"; 
					$headers .= "X-Mailer: PHP\n"; 				// mailer
					$headers .= "X-Priority: 1\n"; 				// Urgent message!
					$headers .= "Return-Path: <$Email>\n";  	// Return path for errors
					mail($Email, "Please Verify Signature for $header_title", $confirm_email_message, $headers); 
					$update=mysql_query("UPDATE phpetition SET verifyDate=SYSDATE() WHERE Email='$Email'",$db);
				}
			}
		}	
	} 
	$vreturn .= "</TR></TABLE>\n";
	$vreturn .= "</TD></TR></TABLE>\n";
	$vreturn .= "</form>\n";
	return $vreturn;
} // End of function visitorVerification ($Email)

function randomPassword($length) {
	$possible = '0123456789' .
              'abcdefghjiklmnopqrstuvwxyz' .
              'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$str = "";
	while (strlen($str) < $length) {
		$str .= substr($possible, mt_rand(0, strlen($possible) - 1), 1);
	}
   	return($str);
}

function externalSMTP($sendTo, $sentFrom, $subject, $headers, $body) {
	/***************************************
	** Project........: SMTP Class
	** Last Modified..: 30 September 2001
	***************************************/
	
	/***************************************
	** Include the class. The header() makes
	** the output look lovely.
	***************************************/
	
	include('class.smtp.inc');
	header('Content-Type: text/plain');
	
	/***************************************
	** Setup some parameters which will be 
	** passed to the smtp::connect() call.
	***************************************/

	$params['host'] = 'bangalore.flora.ca';				// The smtp server host/ip
	$params['port'] = 25;							  // The smtp server port
	$params['helo'] = exec('hostname');		// What to use when sending the helo command. Typically, your domain/hostname
	$params['auth'] = FALSE;					// Whether to use basic authentication or not
	$params['user'] = 'testuser';				// Username for authentication
	$params['pass'] = 'testuser';				// Password for authentication

	/***************************************
	** These parameters get passed to the 
	** smtp->send() call.
	***************************************/
	
	// $headers .= "X-Mailer: PHP\n"; 				// mailer
	// $headers .= "X-Priority: 1\n"; 				// Urgent message!
	// $headers .= "Return-Path: <$SenderEmail>\n";  	// Return path for errors
		
	$send_params['recipients'] = array('$sendTo');		// The recipients (can be multiple)
	$send_params['headers'] = array('From: $sentFrom',	'To: $sendTo', 'Subject: $subject');
	$send_params['from'] = '$sentFrom';	// This is used as in the MAIL FROM: cmd
																		// It should end up as the Return-Path: header
	 $send_params['body'] = '$body';			// The body of the email


	/***************************************	
	** The code that creates the object and
	** sends the email.
	***************************************/

	// if(is_object($smtp = smtp::connect($params)) AND $smtp->send($send_params)){
	if(is_object($smtp = connect($params)) AND $smtp->send($send_params)){
		echo 'Email sent successfully!'."\r\n\r\n";
	
		// Any recipients that failed (relaying denied for example) will be logged in the errors variable.
		print_r($smtp->errors);
	}else{
		echo 'Error sending mail'."\r\n\r\n";
		// The reason for failure should be in the errors variable
		print_r($smtp->errors);
	}
}

function navigation() {
	global $lang, $openNewWindow, $sign_now, $view_only, $daily_stats, $country_stats, $faq, $donate, $lang_avail, $Email;
	$navigation = "<!-- functions.php - Navigation -->";
	$navigation .= "<table border=0 cellspacing=0 cellpadding=1 width=200 cols=4>\n\t<tr><td align=left>\n\t\t<a href=\".?lang=$lang#namelist\" 	$openNewWindow><small>$sign_now</small></a>";
	$navigation .= "\n\t</td></tr><tr><td align=left><a href=\"signed.php?lang=$lang#namelist\" 	$openNewWindow><small>$view_only</small></a>";
	// $navigation .= "\n\t</td></tr><tr><td align=left>\n\t\t<a href=\"dailyStats.php?lang=$lang\"  target=\"_blank\"><small>$daily_stats</small></a>";
	// $navigation .= "\n\t</td></tr><tr><td align=left>\n\t\t<a href=\"stats.php?lang=$lang\"  $openNewWindow><small>$country_stats</small></a>";
	// $navigation .= "\n\t</td></tr><tr><td align=left>\n\t\t<a href=\"faq.php?lang=$lang\" $openNewWindow><small>$faq</small></a>";
	// $navigation .= "\n\t</td></tr><tr><td align=left><a href=\"support.php\" $openNewWindow><small>$donate</strong></a>";
	// Languages
	$navigation .= "<P><table border=0 cellspacing=0 cellpadding=1 width=200 	cols=4><tr><td><small>";
	while (list($key,$val) = each($lang_avail)) {
		if ($lang != $key) {
			$navigation .= "<a href=\"${PHP_SELF}?lang=$key\">$val</a><br>";
		}
	}
	$navigation .= "</small></td></tr></table>";

	$navigation .= "\n\t</td></tr>\n</table><P>";
	$navigation .= visitorVerification ($Email);
	return $navigation;
}

function progressBox() {
	global $db, $boxBGcolor, $petition_started, $verified_signatures, $petition_ends,$boxFontColor, $current_sigs, $id;
	echo "<!-- functions.php - Progress Box -->";
	echo "<table cellpadding=0 cellspacing=0 border=1 align=center bgcolor=\"$boxBGcolor\" width=\"100%\"><tr><td>";
	echo "\n\t<table border=0 cellspacing=0 cellpadding=0 width=\"100%\"><tr>";
	echo "\n\t\t<td align=center><small><B><font color=\"$boxFontColor\">$petition_started</font></B></small></td>";
	$result = mysql_query("SELECT COUNT(*) FROM phpetition WHERE confirmDate IS NOT NULL and petid = $id",$db);
	$current_sigs  = ($result>0) ? mysql_result($result,0,0) : 0;
	echo "\n\t\t<td align=center><small><B><font color=\"$boxFontColor\">$verified_signatures &nbsp; $current_sigs</b></font></small></td>";
	echo "\n\t\t<td align=center><B><small><font color=\"$boxFontColor\">$petition_ends</font></small></B></td>";
	echo "\n\t</tr></table>";
	echo "</td></tr></table>";
}

function recentSignatures() {
	global $recent_signatories, $individuals_query;
	echo "<strong>$recent_signatories</strong>\n<br><UL><em>";
	$sql = "SELECT FirstName,LastName,City,State,Country FROM phpetition WHERE Public='on' AND confirmDate IS NOT NULL ORDER BY confirmDate DESC LIMIT 0,15";
	$individuals_query = mysql_query($sql);
	while ($individuals_array = mysql_fetch_array($individuals_query)) {
		echo "<LI>" . stripslashes($individuals_array["FirstName"]) . " " 
		. stripslashes($individuals_array["LastName"]) 
		. " $from ";
		echo stripslashes($individuals_array["City"]) . " "; 
		echo stripslashes($individuals_array["State"]) . " ";
		// echo "stripslashes($individuals_array["Country"]);
	}
	echo "</em></UL>";
}

// switchLogic($GLOBALS["logic"]);
function switchLogic ($logic)
{
	if ($logic == "DESC") {
	  $logic = "ASC";
	} else {
	  $logic = "DESC";
	}
	return $logic;
}
echo "<!-- END: functions.php -->\n";
?>
