<?php
$modid=7; 
$EmailConfirm = 0;
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
//
// This file is where participants sign up


require_once("config.php");
require_once("functions.php");

progressBox();

include_once ("petitiontext.php"); 



// recentSignatures();

$error=0;
if($inputSubmit){
	// Validate the Comments
	$Email = trim($Email);
	//validEmail($Email);
	$same_Email_query = mysql_numrows(mysql_query("SELECT Email FROM phpetition WHERE petid=$id and Email='" . $Email  . "'"));
	if($same_Email_query!=0){
		$error = 1;
		$error_html .= "This email \"" . $Email . "\" has already been used to sign the petition.<br><br>\n";
	}	
		
	if(strlen(trim($Comments)) > 255){
		$error = 1;
		$error_html .= "Comment cannot exceed 255 characters in length.<br><br>\n";
	}

	if($FirstName==""){
		$error = 1;
		$error_html .= "You have not provided us with your first name.<br><br>\n";
	}

	if($LastName==""){
		$error = 1;
		$error_html .= "You have not provided us with your last name.<br><br>\n";
	}

	if($Email==""){
		$error = 1;
		$error_html .= "You have not provided us with your email address.<br><br>\n";
	}

	if($City==""){
		$error = 1;
		$error_html .= "You have not provided us with your city.<br><br>\n";
	}
	
	if($State==""){
		$error = 1;
		//$error_html .= "You have not provided us with your $province.<br><br>\n";
		 $error_html .= "You have not provided us with your $state.<br><br>\n";
	}

	if($PostalCode==""){
		$error = 1;
		$error_html .= "You have not provided us with your zip code.<br><br>\n";
	}
		
	if($error=="1"){
		echo "<br><b>For your submission to be effective it is important that you complete all of the required fields.  Your submission was rejected for the following reasons:</b><blockquote>" . $error_html . "</blockquote>\nPlease <a href=\"javascript:history.go(-1)\">go back</a> and try again.";

	} else {
		// Make sure that all urls begin with http://
		if($WebSite){
			$WebSite = eregi_replace( "http://", "", $WebSite ); 
			$WebSite = "http://" . $WebSite; 
		}
		// Prepare text to be inserted into the database
		$FirstName=addslashes(htmlspecialchars(trim($FirstName)));
		$LastName=addslashes(htmlspecialchars(trim($LastName))); 
		$Organization=addslashes(htmlspecialchars(trim($Organization)));
		$Address=addslashes(htmlspecialchars(trim($Address)));
		$City=addslashes(htmlspecialchars(trim($City)));
		$State=addslashes(htmlspecialchars($State));	
		$Country=addslashes(htmlspecialchars($Country));
		$PostalCode=addslashes(htmlspecialchars(trim($PostalCode)));
		$Comments=addslashes(htmlspecialchars(trim($Comments)));
		$IPAddress = getenv('REMOTE_ADDR');
		$Browser = getenv('HTTP_USER_AGENT');
		if ($EmailConfirm != 1)
		{
		$Verified = "yes";
		//$confirmDate = date("l, d F Y h:i a");
		}
		$petid= $id;
		// $Date = date("l, d F Y h:i a");

	$count = 0;
	do { /* loop until a valid result or failure count exceeds threshold. */
		$insert = mysql_query("INSERT INTO phpetition (petid,FirstName,LastName,Organization,Email,WebSite,Address,City,State,Country,PostalCode,PGP,Comments,Verified,Contact,Public,IPAddress,Browser,confirmDate,Date) VALUES ('$petid','$FirstName','$LastName','$Organization','$Email','$WebSite','$Address','$City','$State','$Country','$PostalCode','$PGP','$Comments','$Verified','$Contact','$Public','$IPAddress','$Browser',SYSDATE(), SYSDATE())",$db);	
		
		$insert2 = mysql_query("INSERT INTO contacts2 (FirstName,LastName,Company,EmailAddress,WebPage,BusinessStreet,BusinessCity,BusinessState,BusinessCountry,BusinessPostalCode,notes,source,enteredby) VALUES ('$FirstName','$LastName','$Organization','$Email','$WebSite','$Address','$City','$State','$Country','$PostalCode','$Comments','$source','$enteredby')",$db);
		
if (($list1 == 1) or ($list2 == 1) or ($list3 == 1)){		
	$mailinsert = mysql_query("INSERT INTO email (firstname,lastname,email,state,zip) VALUES ('$FirstName','$LastName','$Email','$State','$PostalCode')",$db);
		 $newrec=$dbcon->CacheExecute("SELECT id FROM email ORDER BY id desc LIMIT 1") or DIE($dbcon->ErrorMsg());  
$recid=$newrec->Fields("id");
$newrec->Close();
		if ($list1 == 1) {
		$maillist1 = mysql_query("INSERT INTO subscription (userid,listid) VALUES ('$recid','$listid1')",$db); }
		if ($list2 == 1) {
		$maillist1 = mysql_query("INSERT INTO subscription (userid,listid) VALUES ('$recid','$listid2')",$db); }
		if ($list3 == 1) {
		$maillist1 = mysql_query("INSERT INTO subscription (userid,listid) VALUES ('$recid','$listid3')",$db); }
		} //end list check
		
  
  
  
		if (!$insert) {
			error_log("$PHP_SELF::insert Email: $Email, attempt no. $count error: " . mysql_error(), 0);
		} else {
			break;
		}
		$count++;
	} 
	
	while (!$insert and $count < 10);
	if ($insert) {
		echo "<!-- Successful -->";
	} else {
		echo "could not verify, server likely too busy, please try again later.";
		error_log("failure threshold exceeded Email: $Email", 0);
	}

			$count = 0;
			do { /* loop until a valid result or failure count exceeds threshold. */
				$getID = mysql_query("select ID FROM phpetition WHERE Email='$Email'",$db);
				if (!$getID) {
					error_log("$PHP_SELF::getID Email: $Email, attempt no. $count error: " . mysql_error(), 0);
				} else {
					break;
				}
				$count++;
			} while (!$getID and $count < 10);
			if ($getID) {
				$PID = mysql_result($getID,0); 
			} else {
				echo "could not verify, server likely too busy, please try again later.";
				error_log("failure threshold exceeded Email: $Email", 0);
			}
		// $PID = mysql_result(mysql_query("select ID FROM phpetition WHERE Email='$Email'",$db),0); 
		
		// The below code should be faster, but doesn't work on all servers.
		// $PID = mysql_insert_id();
		// Confirmation Email address sent to signer

		if (file_exists("lang/emails.$lang.php")) include( "lang/emails.$lang.php" );  
		else include( "lang/emails.eng.php" );  

		$headers .= "From: " . stripslashes($FirstName) . " " . stripslashes($LastName) . "<$Email>\n"; 
		$headers .= "X-Mailer: PHP\n"; 				// mailer
		$headers .= "X-Priority: 1\n"; 				// Urgent message!
		$headers .= "Return-Path: <$Email>\n";  	// Return path for errors
if ($EmailConfirm == "1") {	
		mail($Email, "Please Verify Signature for $header_title", $confirm_email_message, $headers); 
		
}// end email confirm
		echo "<a style=\"text-decoration:none\" name=\"namelist\"></a><center>$success: $PID<br>$successMessage";

		// Alert Friends
		// include('alert.php');
	}
	echo "<center><p><a href=\"signed.php?id=$id&lang=$lang#namelist\">$view_only</a></center>";
} 


else  {  //main form


 $peitondata=$dbcon->CacheExecute("SELECT uselists, list1, list2, list3, sourceid, enteredby FROM petition where id = $id") or DIE($dbcon->ErrorMsg());
//population the list information 
 if ($peitondata->Fields("list1") ==1) {
 $uselist=1;
 
  if ($peitondata->Fields("list1") != $null) {
 $list1r=$dbcon->CacheExecute("SELECT name, id from lists where id =".$peitondata->Fields("list1")." and  publish =1 ") or DIE($dbcon->ErrorMsg()); 
 $list1name = $list1r->Fields("name");
 $list1r->Close(); } 
   if ($peitondata->Fields("list2") != $null) {
 $list2r=$dbcon->CacheExecute("SELECT name, id from lists where id =".$peitondata->Fields("list2")." and  publish =1 ") or DIE($dbcon->ErrorMsg()); 
  $list2name = $list2r->Fields("name");
 $list2r->Close();} 
   if ($peitondata->Fields("list3") != $null) {
 $list3r=$dbcon->CacheExecute("SELECT name, id from lists where id =".$peitondata->Fields("list3")." and  publish =1 ") or DIE($dbcon->ErrorMsg()); 
  $list3name = $list3r->Fields("name");
 $list3r->Close();} 
 }//end list population
 
if (file_exists("lang/dropdown.$lang.php")) include( "lang/dropdown.$lang.php" );  
else include( "lang/dropdown.eng.php" );  ?>

<form method=post action="<?php echo $PHP_SELF  ?>?id=<?php echo $id ?>#namelist">

<a name="namelist"></a>

<!-- Outer Box - Line -->
<table cellpadding=0 cellspacing=0 border=1 align=center class="form"l width="100%"><tr><td>
<!-- Outer Box-->
<table cellpadding=9 cellspacing=0 border=0 align=center class="form"l width="100%"><tr><td>
<!-- Organizing input fields-->
<table cellpadding=1 cellspacing=1 border=0 align=center class="form"l>
<tr>
                <td valign=middle align=RIGHT><B><font color="#FF0000"><?php echo $first_name ?></font></B> 
                  &nbsp; </td>
                <td valign=middle align=left><input type=text size=40 name="FirstName" value="" maxlength=30></td></tr>
<tr>
                <td valign=middle align=RIGHT><B><font color="#FF0000"><?php echo $last_name ?></font></B> 
                  &nbsp; </td>
                <td valign=middle align=left><input type=text size=40 name="LastName" value="" maxlength=30></td></tr>
<tr><td valign=top align=RIGHT><?php echo $group_affiliation ?> &nbsp; <br><small><?php echo $not_endorsement ?></small> &nbsp; </td><td valign=top align=left><input type=text size=40 name="Organization" value="" maxlength=30></td></tr>
<tr>
                <td valign=middle align=RIGHT><B><font color="#FF0000"><?php echo $signor_email ?></font></B> 
                  &nbsp; </td>
                <td valign=middle align=left><input type=text size=40 name="Email" value="" maxlength=100></td></tr>

 <tr><td valign=middle align=RIGHT><B><?php echo $address ?></B> &nbsp; </td><td valign=middle align=left><input type=text size=40 name="Address" value="" maxlength=255></td></tr>


<tr>
                <td valign=middle align=RIGHT><B><font color="#FF0000"><?php echo $city ?></font></B> 
                  &nbsp; </td>
                <td valign=middle align=left><input type=text size=40 name="City" value="" maxlength=100></td></tr>

<!--<tr><td valign=middle align=RIGHT><B>$province</B> &nbsp; </td><td valign=middle align=LEFT><?php //echo $cdnProvDropDown ?></td></tr> -->
 <tr>
                <td valign=middle align=RIGHT><B><font color="#FF0000"><?php echo $state ?></font></B> 
                  &nbsp; </td>
                <td valign=middle align=LEFT><?php echo statelist('State') ?></td></tr>
 <tr>
                <td valign=middle align=RIGHT><B><font color="#FF0000"><?php echo $country ?></font></B> 
                  &nbsp; </td>
                <td valign=middle align=LEFT><?php echo $countryDropDown ?></td></tr>

 

<tr>
                <td valign=middle align=RIGHT><B><font color="#FF0000"><?php echo $postal_code ?></font></B> 
                  &nbsp; </td>
                <td valign=middle align=left><input type=text size=40 name="PostalCode" value="" maxlength=20></td></tr>

<tr><td valign=top align=left><B><?php echo $comments ?></B> &nbsp; <br><small><?php echo $max255 ?></small> &nbsp; </td><td valign=top align=left><textarea name="Comments" maxlength=255 rows=4 cols=38></textarea></td></tr>
<tr><td valign=middle COLSPAN=2 align=LEFT><small><?php echo $required_fields ?></small></td></tr>
</table><br>



<!-- Check Boxes -->
            <table cellpadding=0 cellspacing=2 border=0 align=center class="form"l>
              <?php if ($uselist == 1) {?>
              <tr> 
                <td colspan="2" valign=top><small><?php echo $updates_ok ?></small></td>
              </tr>
              <?php if  ($peitondata->Fields("list1") != $null) {?>
              <tr> 
                <td valign=top><div align="right"><small><?php echo $list1r->Fields("name") ?></small></div></td>
                <td valign=top><small>&nbsp; 
                  <input type=checkbox name="list1" value="1" checked>
                  <input name="listid1" type="hidden" value="<?php echo $list1r->Fields("id") ?>">
                  </small></td>
              </tr>
              <?php }?>
              <?php if  ($peitondata->Fields("list2") != $null) {?>
              <tr> 
                <td valign=top><div align="right"><small><?php echo $list2r->Fields("name") ?></small></div></td>
                <td valign=top><small>&nbsp; 
                  <input name="list2" type=checkbox value="1" checked>
                  <input name="listid2" type="hidden" value="<?php echo $list2r->Fields("id") ?>">
                  </small></td>
              </tr>
              <?php }?>
              <?php if  ($peitondata->Fields("list3") != $null) {?>
              <tr> 
                <td valign=top><div align="right"><small><?php echo $list3r->Fields("name") ?></small></div></td>
                <td valign=top><small>&nbsp; 
                  <input name="list3" type=checkbox value="1" checked>
                  <input name="listid3" type="hidden" value="<?php echo $list3r->Fields("id") ?>">
                  </small></td>
              </tr>
              <?php }?>
              <?php }?>
              <tr> 
                <td valign=top><small><?php echo $display_ok ?></small></td>
                <td valign=top><small>&nbsp; 
                  <input type=checkbox name="Public" CHECKED>
                  <?php echo $yes ?></small></td>
              </tr>
            </table>


<input type="hidden" name="IPAddress">
<input type="hidden" name="source" value="<?php echo $peitondata->Fields("sourceid"); ?>">
<input type="hidden" name="enteredby" value="<?php echo $peitondata->Fields("enteredby"); ?>">
<input type="hidden" name="id" value="<?php echo $id ?>">
<input type="hidden" name="Date">
<?php mt_srand((double)microtime() * 1000000);?>
<?php if ($EmailConfirm == 1){?>
<input type="hidden" name="Verified" value="<?php echo randomPassword(8) ?>">
<?php } ?>
<input type="hidden" name="inputSubmit"  value="inputSubmit">

<!-- Submit Box -->
<table cellpadding=0 cellspacing=10 border=0 align=center class="form"l>
<tr><td ALIGN=CENTER VALIGN=middle><input name=submit type=submit value="<?php echo $submit ?>"></td><td VALIGN=middle align=left><b><?php echo $acknowledgement ?></b></td></tr>
<tr><td colspan=2><?php echo $important ?><P><small><?php echo $privacy_note ?></small>
</td></tr></table>

</td></tr></table>
</td></tr></table>
</td></tr></table><P>




<p><a href="signed.php?lang=<?php echo $lang ?>&id=<?php echo $id ?>#namelist"><?php echo $view_only ?></a> 

<?php }
echo "<P><br>"; 
include("$base_path"."footer.php");

echo "<!-- $script_display -->";
?>