<?php 

function send_mail(&$smtp, $website, $id, $email, $order, $updatefile) {

	global $test;

	$hash = id_encode($id);	
	$subject = $_SESSION['subject'];
	$emailfrom = $_SESSION['emailfrom'];
	$htmlemail = $_SESSION['htmlemail'];
	$emailname = $_SESSION['emailname']."<$emailfrom>";
	$bounce =$_SESSION['bounce'];
	$text  = $_SESSION['body'];
	$goto = $website.$updatefile;
	if ($updatefile != "none"){
	 if ($htmlemail == 1 ){
	 	$text .= "<br>_____________________________________________________<br>";
	$text .= "To unsubscribe or update your listing <a href=\"".$goto."?id=$id&token=".$hash."\">click here</a> ";
	 }
	 else {
	$text .= "\n_____________________________________________________\n";
	$text .= "To unsubscribe or update your listing go to:\n ";
	$text .= $goto."?id=$id&token=".$hash;
	}
	}

	if (!$test) {

		//glog("compose $email");
		$headers = "From: {$emailname}\r\n";
		$headers .= "Reply-To: {$emailfrom}\r\n";
				$headers .= "X-Sender: {$emailfrom}\r\n";
			if ($htmlemail == 1){
		$headers .= "Content-Type: text/html\r\n";}
			$headers .= "Return-Path: {$bounce}\r\n";

		

		$ok = $smtp->email($bounce, $email, '', $headers, $subject, $text);
//mail($email, stripslashes($subject), stripslashes($text), $headers);

		if ($ok) {
			//glog("   sent $email id=$id order=$order ok=" . ($ok ? 'TRUE' : 'FALSE') );
			echo "sent $email<br>";
			return true;
		}

		else {
			//glog("error $email id=$id order=$order ok=" . ($ok ? 'TRUE' : 'FALSE') );
			glog("   smtp_client error: " . $smtp->msg);
			echo "error sending $email ($smtp->msg)<br>";
			return false;
		}

	}

	else {
		echo "test $email<br>";
		return true;
	}

}

/* function email_is_valid($email) {
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
} */


function id_encode($id) {
	return $id*3+792875;
}

/* 
 * return true if we processed the request, 
 * false otherwise.
 */


function handle_request() {

	function get($arg) {
		return isset($_REQUEST[$arg]) ? $_REQUEST[$arg] : '';
	}

	$action  = get('mail_blast_action');
    $htmlemail    = get('htmlemail');
	$list    = get('list');
	$subject = get('subject');
	$body    = get('body');
	$id      = get('id');
	$bounce      = get('bounce');
	$emailname    = get('emailname');
	$emailfrom    = get('emailfrom');
	$passedsql    = get('passedsql');
	$sqlp      = get('sqlp');

	if ($id != '' || $sqlp !='') {
	print_form($id,$subject,$body,$emailfrom,$emailname,$sqlp);
			return true;
	}

	elseif ($action == 'save') {
		if ( $bounce == '' || $subject == '' || $body == ''|| $emailfrom == '' || $emailname == '' ) {
			echo "Try again: some fields not filled in<p>";
			print_form($list,$subject,$body,$emailfrom,$emailname,$passedsql);
			return true;
		}

		else {			
			// save the vars to the session
			$_SESSION['subject'] = $_REQUEST['subject'];
			$_SESSION['body'] = $_REQUEST['body'];
			$_SESSION['bounce'] = $_REQUEST['bounce'];
			$_SESSION['emailfrom'] = $_REQUEST['emailfrom'];
			$_SESSION['emailname'] = $_REQUEST['emailname'];
			$_SESSION['htmlemail'] = $_REQUEST['htmlemail'];
			$_SESSION['list'] = $_REQUEST['list'];
			$_SESSION['passedsql'] = stripslashes($_REQUEST['passedsql']);
			return false;
		}
	}

	elseif ($action == 'list_subscribers') {
		do_list_subscribers();
		return true;
	}

	elseif ($action == 'list_invalid') {
		do_list_invalid_subscribers();
		return true;
	}

	elseif ($action == 'test') {
		do_single_test();
		return true;
	}

	elseif (isset($_SESSION['subject']) && isset($_SESSION['body']) && isset($_SESSION['emailfrom']) && isset($_SESSION['emailname']) && isset($_SESSION['list'])  && isset($_SESSION['passedsql'])) {
		return false; // we did not handle the request
	}

}

# these are additional controls added to the left frame

function print_controls()
{	

	$url = $_SERVER['PHP_SELF'];
	echo "mail blast for list:<br><b>$_SESSION[list]</b><br><br>";
	echo "<a target=display href=\"$url?mail_blast_action=list_subscribers\">List subscribers</a><br>";
	echo "<a target=display href=\"$url?mail_blast_action=list_invalid\">List invalid addresses</a>";
	?>
	<p>
	<FORM name="testform" ACTION="<?=$url?>" METHOD=GET target="display">
	<INPUT TYPE="text" NAME="testemail" VALUE="test@domain.org">
	<INPUT TYPE="hidden" NAME="mail_blast_action" VALUE="test">
    <a href="javascript:document.testform.submit()">send single test</a>
	</FORM>

	<?php
	
}

function do_single_test() {
global $dbcon;
global $Web_url;
	
	$smtp = new smtp_client();
	$smtp->log_file = dirname(__FILE__) . "/smtp_client.log";
	send_mail($smtp, $Web_url, 1, $_REQUEST['testemail'],1);
	$smtp->send();
}

function getmicrotime() { 
  list($usec, $sec) = explode(" ",microtime()); 
  return ((float)$usec + (float)$sec); 

} 

function print_form($id,$subject,$body,$emailfrom,$emailname,$passedsqlx) {
global $dbcon;

$setvar=$dbcon->Execute("SELECT emmedia, emailfromname FROM sysvar where id=1") or DIE($dbcon->ErrorMsg());
   $MM_email_list = $setvar->Fields("emmedia");				//E-Mail Blast From
   $MM_email_listname = $setvar->Fields("emailfromname");				//E-Mail Blast From Name

?>	

	<form method=POST action="<?=$_SERVER['PHP_SELF']?>">

	<table>	

	<h2>Compose Mail to List #<?=$id?></h2>

	<input type="hidden" name="list" value="<?=$id?>">
	<input type="hidden" name="passedsql" value="<?= stripslashes($passedsqlx)?>">

	<tr><td>Subject:</td></tr>

	<tr><td><input type="text" name="subject" size="40" value="<?=htmlentities($subject)?>"></td></tr>
		<tr><td>From Name:</td></tr>

	<tr><td><input type="text" name="emailname" size="40" value="<?=htmlentities($MM_email_listname)?>"></td></tr>
		<tr><td>From Email:</td></tr>

	<tr><td><input type="text" name="emailfrom" size="40" value="<?=htmlentities($MM_email_list)?>"></td></tr>
			<tr><td>Bounce Email Address:</td></tr>

	<tr><td><input type="text" name="bounce" size="40" value="<?=htmlentities($MM_email_list)?>"></td></tr>

	<tr><td>Body&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Send 
        as HTML 
        <input name="htmlemail" type="checkbox" id="htmlemail" value="1">:</td></tr>

	<tr><td><textarea name="body" rows="25" cols="55" wrap="VIRTUAL"><?=htmlentities($body)?></textarea></td></tr>

	<tr>

	  <td>

	    <input type=hidden name=mail_blast_action value=save>

		<input name="submit" type="submit" value="Continue...">

	  </td>

	</tr>

    </table>	

    </form>

<?php

} ?>