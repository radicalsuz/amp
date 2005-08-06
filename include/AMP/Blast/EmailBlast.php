<?php
#to do: parse html email for link rewites


require_once('Net/POP3.php');
require_once('AMP/Blast/BlastBase.php');
include ("AMP/Blast/class.html.mime.mail.inc");


class EmailBlast extends Blast {

	var $type ='Email';

	//a function that does the sending of a message
	function send_email($email,$message,$subject=NULL,$from=NULL,$replyto=NULL) {
		$mail = new html_mime_mail(
      	array("Reply-To: $replyto",
	  		"X-Mailer: AMP v3.4",
            "X-MessageId: $message_ID"));
		
		# $mail->add_html($message, $text = NULL, $images_dir = NULL)
		
		$mail->add_text($textmessage);
				
		$mail->send("", $to_addr, $from_name, $from_addr, $subject);

		
		
		
		if (mail($email,$subject,$message,"From: $from\r\nReply-To: $replyto\r\nX-Sender: $from\r\nReturn-Path: $replyto", "-f$replyto")) {
			return true;
		}
	}
	
		
	#a function to merge user data into message the array $fields as the fields names in userdata to merge 
	function merge_fields_email($message,$message_ID,$fields){
		$sql="select u.* from userdata uwhere id = ";
		$R=$this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());
		for ($x=0; $x<sizeof($fields); $x++) {
			$message .= eregi_replace("\[".$fields[$x]."\]",$R->Fields($fields[$x]),$message);
		}	
		return $message ;
	}
	
	function click_through($message_ID,$click_ID) {
		$sql="Select url from blast_click_links where click_link_ID = $click_ID";
		$R=$this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());
		$this->dbcon->Execute("Insert into blast_clicks (click_link_ID,message_ID,requested) VALUES ('$click_ID','$message_ID',now())")or DIE($this->dbcon->ErrorMsg());
		$redirect = $R->Fields("url");
		return $redirect;
	
	}
	
	function user_tracking($message_ID) {
		$sql ="update messages_to_contacts set viewed = now() where message_ID = $message_ID";
		$this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());
	
	}
	
	function encode_blast_email($htmlmessage=NULL,$textmessage=NULL,$message_ID,$fields=NULL) {
		if ($this->type != 'Email-Admin') {
			if ($htmlmessage) {
				$htmlmessage = eregi_replace("\[USERID\]",$message_ID,$htmlmessage);
				if ($fields) {
					$htmlmessage = merge_fields_email($htmlmessage,$message_ID,$fields);
				}
				$htmlmessage .='<img src="'.$Web_Site.'http://localhost/amp/ut.php?m='.$message_ID.'" width="1" height="1" border="0">';
				$htmlmessage .='<br><p align="center"> To unsubscribe please click <a href="'.$Web_Site.'http://localhost/amp/unsubscribe.php?m='.$message_ID.'">here</a></p>';
				$htmlmessage = ereg_replace("\[[A-Z\. ]+\]","",$htmlmessage);
			
			}
		
			if ($textmessage) {
				//$textmessage = eregi_replace("\[USERID\]",$message_ID,$textmessage,$fields=NULL);
				if ($fields) {
					$textmessage = $this->merge_fields_email($textmessage,$message_ID,$fields);
				}
					$textmessage .='\n_____________________________________________________\n To unsubscribe go to:\n '.$Web_Site.'/unsubscribe.php?m='.$message_ID;
				$textmessage = ereg_replace("\[[A-Z\. ]+\]","",$textmessage);
			}
		}
		else {
			if ($htmlmessage) {
				if ($fields) {
					$htmlmessage = merge_fields_email($htmlmessage,$message_ID,$fields);
				}
				$htmlmessage .='<img src="'.AMP_SITE_URL.'/ut.php?m='.$message_ID.'" width="1" height="1" border="0">';
				$htmlmessage = ereg_replace("\[[A-Z\. ]+\]","",$htmlmessage);
			
			}
		}
		$message = array ('html'=>$htmlmessage, 'text'=>$textmessage);
		return $message; 
	}
	
	function set_click_links($message,$type) {		
			#$this->dbcon->Execute("Insert into blast_click_links (blast_ID,url) VALUES ('$blast_ID','$url')")or DIE($this->dbcon->ErrorMsg());
			#$click_ID = $this->dbcon->Insert_ID();
			#$link = $Web_Site.'clcik.php?c='.$click_ID.'&m='.$message_ID;
		
		#this needs to search for links that do not have click.php in them, insert into the db the link and rewite the url in the message. 	
		return $message;
	
	}
	
	function build_message() {
		$sql = "Select  message_email_html, message_email_text, from_name, reply_to_address, from_email, subject, sendformat, message_template_ID from blast  where  blast_ID = ".$this->blast_ID;
		$R = $this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());
	
		$htmlmessage = $this->set_click_links($R->Fields("message_email_html"),'html');
		$textmessage = $this->set_click_links($R->Fields("message_email_text"),'text');
		$message = array (
			'subject'=>$R->Fields("subject"),
			'from_email'=>$R->Fields("from_email"),
			'from_name'=>$R->Fields("from_name"),
			'reply_to_address'=>$R->Fields("reply_to_address"),
			'htmlmessage'=>$htmlmessage,
			'textmessage'=>$textmessage,
			'sendformat'=>$R->Fields("sendformat")
		);
		return $message;	
	}
	
	
	//function that sends all the messages in the message que of a certian blast
	function send_messages() {
		ignore_user_abort(1);
		set_time_limit(0);
		flush();
		$message = $this->build_message();
		if ($this->type  == 'Email-Admin') {
			$sql = "Select distinct u.Email, m.message_ID  from messages_to_contacts m, blast_system_users u  where m.system_user_ID= u.id and  m.status = 'New' and  m.blast_ID =".$this->blast_ID;
		} else{
			$sql = "Select distinct u.Email, m.message_ID  from messages_to_contacts m, userdata u  where m.user_ID= u.id and  m.status = 'New' and m.blast_ID =".$this->blast_ID;
		}
		$R = $this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());
		$this->set_message_blast_status('Loaded','New');
		$this->set_blast_status("Sending Messages");
		$this->set_start_time();
		
		$good =0;
		$bad =0;
		$total=0;
		while(!$R->EOF) {
			if  (email_is_valid($R->Fields("Email"))) {
				$this->set_message_status($R->Fields("message_ID"),"Sending");
				// cutomize the email message for this user
				$message_output =  $this->encode_blast_email($message['htmlmessage'],$message['textmessage'],$R->Fields("message_ID"));
				$mail = new html_mime_mail(array("Reply-To: ".$message['reply_to_address'],
			  		"X-Mailer: AMP v3.5",
        		    "X-MessageId: ".$R->Fields("message_ID")));
				 
				 // cutomize the email message for this user
				if ($message['sendformat'] == 'HTML' or $message['sendformat'] == 'HTML and Text') {
					$mail->add_html($message_output['html'],$message_output['text']);
				} 
				else if ($message['sendformat'] == 'Text' ) {
					 $mail->add_text($message_output['text']);
				} 
				$mail->build_message();
				if ( $mail->send("", $R->Fields("Email"), $message['from_name'], $message['from_email'], $message['subject'])) 
				{
					$good++;
					$this->set_message_status($R->Fields("message_ID"),"Done");
				} else {
					$bad++;
					$this->set_message_status($R->Fields("message_ID"),"Server Failure");
				}
			} else {
				$bad++;
				$this->set_message_status($R->Fields("message_ID"),"Bad Address");
			}
			$total++;
			$R->MoveNext();		
		}
		$response = "$good messages sent, $bad messages failed to send in $total attempts.<br>";
		$this->set_blast_status("Complete");		
		$this->set_start_time();
		
		return $response;
	}
	
	##SUBSCRIPTION MANEGMENT FUNCTIONS
	
	function email_pop_subscription($host,$port=995,$login,$password,$list_ID){
	
		$pop3 =& new Net_POP3();	
		if (!$pop3->connect('ssl://'.$host, $port)) {
			die('could not log in');
		}	  
		$pop3->login($login, $password);
		
		$size = $pop3->numMsg();
		if ($size >= 1) {
			$x = 1;
			while ($x <= $size) {	
				$headers = $pop3->getParsedHeaders($x);
				add_email_user($list_ID,$headers['From']);
				$pop3->deleteMsg($x);
				$x++;
			}
		}
		$pop3->disconnect(); 
	}
	
	
	//a function that subscribers a user to a list
	function add_email_user($list_ID,$email) {
		$this->dbcon->Execute("insert into userdata (Email,modin) values ('$email','3')") or die($this->dbcon->errorMsg());
		$user= $this->dbcon->Insert_ID();
		//die('user'.$user);
		$this->dbcon->Execute("insert into lists_to_contacts (list_ID,user_ID) values ('$list_ID','$user')") or die($this->dbcon->errorMsg());	
	}

}
?>
