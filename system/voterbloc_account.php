<?php
/* Sets up a new user account for voter blocs (or other UDMs, with some tweaking)
  * Emails account info out to users
  */

require( 'Connections/freedomrising.php' );

global $dbcon, $MM_email_from;
$user_action=$_REQUEST['action'];


if ($user_action=='create'&&($userper[51]==1)) {
	$bloc_id=$_REQUEST['block'];
	
	$newuser_login=$_REQUEST['login'];

	srand(time());
	$pwd=rand(2000, 4000)."Nov2";
	$email=$_REQUEST['email'];
	$login_type=$_REQUEST['login_type'];
	$uid=$_REQUEST['uid'];
	if (!isset($_REQUEST['permission'])) {
		$newuser_permission=7;
	} else {
		$newuser_permission=$_REQUEST['permission'];
	}

	if ($login_type=="voterbloc") {
		$sys_home="voterbloc_data.php?modin=".$bloc_id;
		$allow_only="voterbloc_data.php?modin=".$bloc_id.",modinput4_view.php?modin=".$bloc_id.",voterguide.php?uid=".$uid.",index.php";
	}
	
	$message=amp_createUser($newuser_login, $pwd, $email, $newuser_permission, $sys_home, $allow_only);

	if (substr($message, 0, 15)=='Created Account'&&$login_type=='voterbloc') {
		//Rewrite the nav for that module
		
		$sql="UPDATE modules set navhtml = \"<A class=side href='voterbloc_data.php?modin=".$bloc_id."'>View/Edit Voter Bloc Members</A><br><A class=side href='modinput4_view.php?modin=".$bloc_id."'>Add Voter Bloc Members</A><br><A class=side href='voterguide.php?uid=".$uid."'>Edit Your Voter Guide</a><BR>\" where userdatamodid=".$bloc_id;
		
		#print $sql."<P>";

		if($rs=$dbcon->Execute($sql)) {
			$message.="\\nVoterBloc Nav revision for user # $uid  bloc #$bloc_id\\n";
			$message.=$dbcon->Affected_Rows()." navs were changed";
		} else {
			$message.="\\please check the Nav for this bloc";
		}

		

	}


} elseif ($user_action=='sendAccount') {
	$email=$_REQUEST['email'];
	$template=$_REQUEST['template_id'];
	$message=amp_emailUser($email, $template);
}

print "<HTML><body><script type=\"text/javascript\"> alert ('".$message."'); 
top.location=history.back(1); 
</script></body></html>";

	
function amp_createUser($login, $pwd, $email, $permission, $new_home='', $allow_only='') {
	global $dbcon;
	$email=$dbcon->qstr($email, $MM_sysvar_mq);
	$pwd=$dbcon->qstr($pwd, $MM_sysvar_mq);
	$login=$dbcon->qstr($login, $MM_sysvar_mq);
	$new_home=$dbcon->qstr($new_home);
	$allow_only=$dbcon->qstr($allow_only);

	$sql="SELECT name, email FROM users where email = $email or name = $login";
	if ($rs=$dbcon->Execute($sql)) {
		if ($rs->RecordCount()>0) {
			return 'an account with that e-mail or login already exists - sorry, one per customer';
		}
	}

	//Create the user account
	$sql="INSERT INTO users (name, password, email, permission, system_home, system_allow_only) VALUES ($login, $pwd, $email, $permission, $new_home, $allow_only)";
	if ($rs=$dbcon->Execute($sql)) { 
		return 'Created Account for '.addslashes($login);
	} else {
		return 'Account Creation Failed';
	}
}

function amp_emailUser($email, $template_id=NULL){
	global $dbcon;
	global $Web_url, $admEmail, $MM_email_from;
	$qemail=$dbcon->qstr($email);
	$sql="SELECT * from users where email=$qemail  Limit 0, 1";

	$founduser=$dbcon->execute($sql);
	if ($founduser->RecordCount()>0) {
		$from=$MM_email_from;
		if (!$from>'') { $from=$admEmail;}
		$site=$Web_url;
		$header  = "From: " . $from;
        #$header .= "\nX-Mailer: AMP/System\n";

		$instructions="Go to ".$site."system\nYour login is: ".$founduser->Fields('name')."\n";
		$instructions.="Your password is: ".$founduser->Fields('password')."\n";
		
		if ($template_id!=NULL) {
			$sql='select title, test from moduletext where id='.$template_id;
			if ($rs=$dbcon->Execute($sql)) {
				$body=$rs->Fields('test');
				$body=str_replace("[-Insert instructions here-]", $instructions, $body);
				$subject=$rs->Fields('title');
			} else {
				$subject="Your ".$site." Account";
				$body=$instructions;
			}
		} else {
			$subject="Your ".$site." Account";
			$body=$instructions;
		}

		if(mail($email, $subject, $body, $header)) {
			$message="Sent account info for ".$founduser->Fields('name')."\\n to ".$email;
		} else {
			$message="Email didn't work - sorry";
		}
	} else {
		$message = "No user was found with that email";
	}
	return $message;
}

?>