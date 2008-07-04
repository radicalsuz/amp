<?php
/* disabled for security ap 2008-07
 */
/*
# email form processor
# from must post to this file woth the foloowing variables:
# $emailsubject, $eamilto, $emailfrom, $redirect
#

include_once("AMP/System/Email.inc.php");

function email_is_valid($email) {
#	return ereg("[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)+(.[a-zA-Z0-9-]+)", $email);
	return eregi(
		"^" .                               // start of line
		"[_a-z0-9]+([_\\.-][_a-z0-9]+)*" .    // user
		"@" .                               // @
		"([a-z0-9]+([\.-][a-z0-9]+)*)+" .   // domain
		"\\.[a-z]{2,}" .                    // sld, tld
		"$",                                // end of line
		$email
	);
}			

$emailsubject = $_POST["emailsubject"];
$_POST["emailsubject"] =NULL;
$emailto = $_POST["emailto"];
$_POST["emailto"] =NULL;
$emailfrom = $_POST[emailfrom];
$_POST["emailfrom"] =NULL;
$redirect = $_POST["redirect"];
$_POST["redirect"] =NULL;
$_POST["Submit"] = NULL;
#remove above variables from the Post array
//$_POST[]
# parse array with varaible names as subject and vale
$message = "Form Data posted from ".$_SERVER['HTTP_REFERER']." \n\n";

foreach ($_POST as $t=>$v) {
if ($v){
			$message .=$t.": ".$v."\n";
		}
		}

if (email_is_valid($emailto)) { 
		mail($emailto,$emailsubject,$message,"From: ".AMPSystem_Email::sanitize($emailfrom));
		//echo $message;
		}
else {$error = "Error - The email target is not valid";}

if ($error) {echo $error;}
else { header("Location: $redirect"); }
*/
?>
