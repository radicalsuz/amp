<?php

 /* 	
require("AMP/Blast/EmailBlast.php");
$bc = new EmailBlast($dbcon);
$emails= array('david@riseup,net','austin@radicaldesigns.org');
$message = array('subject'=>'',
				'messagetext'=>'',
				'messagehtml'=>'',
				'from_email'=>'',
				'from_name'=>'',
				'replyto_email'
				);

$response = $bc->new_system_blast($emails,$message);
  */

$mod_name="email";
#require("Connections/freedomrising.php");
require_once("AMP/System/Base.php");

//set the blast type
if ($_REQUEST['type'] == 'Email') {
	require_once("AMP/Blast/EmailBlast.php");
	$bc = new EmailBlast($dbcon);
	$file = 'email';
}

if ($_REQUEST['type'] == 'SMS') {
	require_once("AMP/Blast/SmsBlast.php");
	$bc = new SmsBlast;
	$file = 'sms';
}

if ($_REQUEST['load'] ) {
	$bc->blast_ID = $_REQUEST['load'];
	header ("Location: blast_".$file.".php?action=list&response=".$bc->blast_load());
}
else if ($_REQUEST['process'] ) {
	$response = $bc->load_que();
	//header ("Location: blast_".$file.".php?action=list&response=".$response );
}


//CLEN UP FUNCTIONWS
else if ($_REQUEST['blast_failed'] ) {
	$bc->blast_ID = $_REQUEST['blast_failed'];
	$response = $bc->resend_failed();
	header ("Location: blast_".$file.".php?action=list&response=".$response);
}

else if ($_REQUEST['blast_bounced'] ) {
	$bc->blast_ID = $_REQUEST['blast_bounced'];
	header ("Location: blast_".$file.".php?action=list&response=".$bc->resend_bounced());
}

else if ($_REQUEST['blast_stale'] ) {
	$bc->blast_ID = $_REQUEST['blast_stale'];
	header ("Location: blast_".$file.".php?action=list&response=".$bc->resend_stale());
}


else if ($_REQUEST['blast_new'] ) {
	$bc->blast_ID = $_REQUEST['blast_new'];
	header ("Location: blast_".$file.".php?action=list&response=".$bc->set_blast_status('New'));
}

echo $response;
echo '<br><a href="#" onClick="javascript:window.close();">Close Window</a>';
?>	
