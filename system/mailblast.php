<?php

set_magic_quotes_runtime (1);

/*********************
 mailblast.php
 send out a lot of emails...

 example usage:
	<a target="_blank" href="mailblast.php?list=$listname">Send mail blast</a>

*********************/ 

error_reporting(E_ALL & ~E_NOTICE);

#session_name("mail_blast");
session_start();
$base = dirname(dirname(__FILE__));
//chdir($base);

require_once("Connections/freedomrising.php");

require_once("mailblast/smtp_client.php");
require_once("mailblast/functions.php");
//include_once("glog.php");
//glog_set_level(LOG_DEBUG);
//glog_set_file($base."/blast.log");

##################################################################3

$test             = false;  // if true, we don't actually send mail
$process_delay    = 100;  // the time to sleep between emails  
                           // value of 1000 is one second

# give our own handler a chance to process the request
if (handle_request()) {
	return; # we are done
}

else {
	# otherwise, let processor.php handle the request
	$process_function = 'do_email_batch';
	$count_function    = 'get_count';
	$controls_function = 'print_controls';
	$chunk_size       = 20;     // how many emails to process at a time
	$refresh_delay    = 10;     // the delay between processing chunks
	                             // value of 1000 is one second
	$title            = "Mail Blast";
	# pass off to processor.php
	include("mailblast/processor.php");
	return;
}

#######################################################################
/*
 * process a batch of emails, starting with offset and processing
 * $chunksize number of emails.
 */

function do_email_batch(&$offset, $chunksize) {

	global $dbcon;
	global $Web_url;
	global $process_delay;

 $updatefile = "email.php"; //if set to "none" skips the footer
 
	//glog("------ start chunk ------ offset=$offset -----");

	$list = $_SESSION['list'];

	## get mailing list ##

	$timestart = getmicrotime();

	$sql = "SELECT distinct email.id, email.email ";
	$sql .= "FROM subscription, email ";
	$sql .= "WHERE email.id=subscription.userid ";
if ($list  !=  9000) { 
	$sql .= "AND subscription.listid=$list ";}
	$sql .= "LIMIT $offset,$chunksize ";

	$contact_list=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());

	if ($contact_list->RecordCount() == 0) {
		echo("done");
		return 'Done.'; // we must be done!
	}

	set_time_limit(0);
	$smtp = new smtp_client();
	$smtp->log_file = dirname(__FILE__) . "/smtp_client.log";
	$smtp->do_log = false;

	while(!$contact_list->EOF) {
		$offset++;
		$userid = $contact_list->Fields("id");
		$email = $contact_list->Fields("email");

		if ( email_is_valid($email) ) {
			$ok = send_mail($smtp, $Web_url, $userid, $email, $offset, $updatefile);

			if (!$ok) {
				$return_message = "Sending Halted!<br>Error: could not send email $email, id $userid, record #$offset";
				break;
			}
		}

		echo("\n"); flush();
		usleep($process_delay * 1000);
		$contact_list->MoveNext();
	}

	$smtp->send();
	$elapsedtime = getmicrotime()-$timestart; 
	echo("elapsed time: $elapsedtime");
	return 'success';
}


function get_count() {

	global $dbcon;
	$list = $_SESSION['list'];

	# haven't tried this!!!
	$sql  = "SELECT  COUNT(DISTINCT id) FROM subscription  ";
	$sql .= "WHERE listid=$list";

	$result=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
	return $result->fields[0];

}

function do_list_subscribers() {

	global $dbcon;

	$list = $_SESSION['list'];
	## get mailing list ##
	#$sql  = "SELECT DISTINCT userid, listid FROM subscription ";
	#$sql .= "WHERE listid=$list ";
	$sql = "SELECT DISTINCT email.id, email.email ";
	$sql .= "FROM subscription, email ";
	$sql .= "WHERE email.id=subscription.userid ";
if ($list  !=  9000) { 
	$sql .= "AND subscription.listid=$list ";}

	$result=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());

	if ($result->RecordCount() == 0)
		echo "No records returned";
	set_time_limit(0); 
	echo "order -- email -- id number<p>";
	$i = 0;
	
	while(!$result->EOF) {
		echo $i++ . ' -- ' . 
			$result->Fields("email") . ' -- ' . 
			$result->Fields("id") . 
			(!email_is_valid($result->Fields("email")) ? " -- <font color=red>INVALID INVALID</font>" : '') . 
			"<br>";
		$result->MoveNext();
	}

}


function do_list_invalid_subscribers() {
	global $dbcon;
	$list = $_SESSION['list'];
	## get mailing list ##

	$sql = "SELECT DISTINCT email.id, email.email ";
	$sql .= "FROM subscription, email ";
	$sql .= "WHERE email.id=subscription.userid ";
if ($list  !=  9000) { 
	$sql .= "AND subscription.listid=$list ";}

	$result=$dbcon->Execute("$sql") or DIE($dbcon->ErrorMsg());
	if ($result->RecordCount() == 0)
		echo "No records returned";
	set_time_limit(0); 
	echo "Invalid email addresses:<p>";
	while(!$result->EOF) {
		if (!email_is_valid($result->Fields("email")))
			echo $result->Fields("id") . ' -- "' . $result->Fields("email") . '"<br>';
		$result->MoveNext();
	}

}

?>