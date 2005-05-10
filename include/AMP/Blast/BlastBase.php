<?php

## to do : figure out better way to load the emails into to message table

class Blast {

	var $dbcon;
	var $blast_ID;
	var $type ;

	function Blast($dbcon){
		$this->dbcon =$dbcon;
	}


	function set_message_status($message_ID,$status){
		$R=$this->dbcon->Execute("update messages_to_contacts set status = '$status' where message_ID=$message_ID ")or DIE($this->dbcon->ErrorMsg());
	}
	
	function set_message_blast_status($status,$old_status=NULL){
		if ($old_status) {
			$old= " and status= '$old_status' ";
		}
		$sql ="update messages_to_contacts set status = '$status' where blast_ID=".$this->blast_ID." $old ";
		
		$R=$this->dbcon->Execute($sql)or DIE($this->dbcon->ErrorMsg());
	}
	
	function set_start_time() {
		$R=$this->dbcon->Execute("update blast set send_start_time = NOW() where blast_ID=".$this->blast_ID)or DIE($this->dbcon->ErrorMsg());
	}
	
	function set_end_time() {
		$R=$this->dbcon->Execute("update blast set send_end_time = NOW() where blast_ID=".$this->blast_ID)or DIE($this->dbcon->ErrorMsg());
	}
	
	function set_blast_status($status){
		$R=$this->dbcon->Execute("update blast set status = '$status' where blast_ID=".$this->blast_ID)or DIE($this->dbcon->ErrorMsg());
	}
	
	## Functions for email  bounce manegment
	
	function set_bounce_status($message_ID,$bounce_type="hard") {
		$R=$this->dbcon->Execute("update messages_to_contacts set status = 'Bounced' and bounce_type = '$bounce_type' where message_ID=$message_ID ")or DIE($this->dbcon->ErrorMsg());
	
	}
	
	# A FUNCTION THAT CHECKS A POP BOX AND SETS THE BOUNCE STATUS
	function pop_bounce($host,$port=995,$login,$password,$list_ID){
	
		$pop3 =& new Net_POP3();	
		if (!$pop3->connect('ssl://'.$host, $port)) {
			die('could not log in');
		}	  
		$pop3->login($login, $password);
		
		$size = $pop3->numMsg();
		if ($size >= 1) {
			$x = 1;
			while ($x <= $size) {	
				#parse the email for bounce information (return message_ID and bounce level) 
				#set the bounce status and level of the  message 	
				set_bounce_status($message_ID);
				$pop3->deleteMsg($x);
				$x++;
			}
		}
		$pop3->disconnect(); 
	}
	
	//a function that verifies if an email is subscribed to the list
	/* function verify_email_sub($email,$list) {
		if (parse_email($email)) {
			$number = parse_email($email);
			$sql = "select s.user_ID from userdata u , voip_list_sub s where u.id=s.user_ID and s.list_ID = $list and u.Cell_Phone = '".$number['number']."'"; 
			$R=$this->dbcon->Execute($sql) or DIE($this->dbcon->ErrorMsg());
			if ($R->Fields("user_ID")){
				return TRUE;
			}
		else {
			$sql = "select s.user_ID from userdata u , voip_list_sub s where u.id=s.user_ID and s.list_ID = $list and u.Email = '$email'"; 
			$R=$this->dbcon->Execute($sql) or DIE($this->dbcon->ErrorMsg());
			if ($R->Fields("user_ID")){
				return TRUE;
			}
		} 
	}
	 */
	
	##CONTROL FUNCTIONS
	
	## A FUNCTION THAT LOADS MESSAGE IT THE DB FROM THE BLAST
	
	
	//loads messages into the que  and starts the send (run a cron job);
	function load_que() {
		$sql = "Select blast_ID from blast where blast_type = '".$this->type."' and status = 'New' and embargo < NOW() ";	
		$R = $this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());
		while (!$R->EOF) {
			$this->blast_ID = $R->Fields("blast_ID");
			$this->blast_load();
			$R->MoveNext();	
		}
		//sends the loaded messages
		$response  = $this->process_que();
		return $response;
	}

	
	//prcesses and send email that are loaded into the que
	function process_que() {
		
		#expand the below sql to load entries with stale "loaded"
		
		$sql = "Select distinct b.blast_ID from messages_to_contacts m, blast b where m.blast_ID =b.blast_ID and   m.message_type = '".$this->type."' and m.status = 'New'  "	;
		$R = $this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());
		while  (!$R->EOF) {
				$this->blast_ID = $R->Fields("blast_ID");
				$response  .= $this->send_messages();
				$R->MoveNext();
		}
	return $response;
	}
	
	function blast_load(){
		if ($this->type == 'Email-Admin') {
			$sql = "insert into messages_to_contacts (blast_ID,system_user_ID,status,message_type)
			select b.blast_ID,u.id,'New','".$this->type."'    
			from blast b, blast_system_users u  
			where  b.blast_ID=u.blast_ID and b.blast_ID = ".$this->blast_ID;
		} else {
			$sql = "insert into messages_to_contacts (blast_ID,user_ID,status,message_type)
			select b.blast_ID,u.id,'New','".$this->type."'    
			from blast b, userdata u, lists_to_contacts s 
			where u.id=s.user_ID and b.status ='New' and b.list_ID=s.list_ID and s.active = 1 and b.blast_ID = ".$this->blast_ID;		
		}
		$this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());		
		$this->set_blast_status('Loaded')		;
		$response = $this->type." Blast Loaded For Sending.";
		return $response;
	}
	
	function resend_failed() {
		$this->set_message_blast_status('New','Server Failure');
		$this->set_message_blast_status('New','Bad Address');
		$this->set_message_blast_status('New','Failed');
		$response = "Failed ".$this->type." Messages Reloaded For Sending.";
		return $response;
	}

	function resend_stale() {
		$this->set_message_blast_status('New','Loaded');
		$this->set_message_blast_status('New','Sending');
		$response = "Stale ".$this->type." Messages Reloaded For Sending.";
		return $response;
	}

	
	function resend_bounced() {
		$this->set_message_blast_status('New','Bounced');
		$response = "Bounced ".$this->type." Messages Reloaded For Sending.";
		return $response;
	}

	function pause_blast() {
		$this->set_message_blast_status('Paused','New');
 		$this->set_message_blast_status('Paused','Loaded');
		$response = $this->type." Paused.";
		return $response;
	}	

	function new_system_blast($emails,$m) {
		if ($m['messagetext']){
			$format ="Text";
		} if ($m['messagehtml']){
			$format ="HTML";
		} if (($m['messagehtml']) && ($m['messagetext'])){
			$format ="HTML and Text";
		}
		
		$sql = "Insert into blast (subject,message_email_html,message_email_text,from_email,from_name,reply_to_address,blast_type,sendformat)	VALUES ('".addslashes($m['subject'])."','".addslashes($m['messagehtml'])."','".addslashes($m['messagetext'])."','".$m['from_email']."','".addslashes($m['from_name'])."','".$m['replyto_email']."','Email-Admin','".$format."')";
		
		$this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());		
		$this->blast_ID = $this->dbcon->Insert_ID();
		for ($x=0; $x<sizeof($emails); $x++) {
			$sql = "insert into blast_system_users (blast_ID,Email) VALUES ('".$this->blast_ID."','".$emails[$x]."')";
			$this->dbcon->Execute($sql)or DIE($sql.$this->dbcon->ErrorMsg());		
		}
		
		$this->type ='Email-Admin';
		$this->blast_load();
		$response .= $this->process_que();
		return $response;
	}
		
}
?>