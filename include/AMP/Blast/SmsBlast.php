<?php
## TO DO GET CARREIR AND DOMAIN INFOR MATION FROM A WEB SERVICE

require_once('Net/POP3.php');
require_once "HTTP/Request.php";
require_once('AMP/Blast/BlastBase.php');

class SmsBlast extends Blast {

	var $dbcon;
	var $type ='SMS';
	
	#generic sms functions
	function cleannumber($number) {
		$remove = array("-","(",")"," ","'","\"");
		$number = trim(str_replace($remove,'',$number));
		if (substr($number, 0, 1)=='1') {$number=substr($number, 1);}
		$ct = strlen($number);
		if ($ct == 10) {
			return $number;
		}
	}
	
	function check_phone($str)
	{
		//returns 1 if valid phone number (only numeric string), 0 if not
		
		if (ereg('^[[:digit:]]+$', $str)) 
			return 1;
		else 
			return 0;
	}
	
	function carrier_lookup($number) {
		$number = $this->cleannumber($number);
		ereg("([0-9]{3})([0-9]{3})", $number, $numar);
		$areacode = $numar[1];
		$prefix = $numar[2] ;
	
		$sql = "SELECT c.domain, c.name, c.type, o.name fullname, o.ocn 
				FROM nxx, ocn o, carrier c
				WHERE nxx.ocn = o.ocn AND o.carrier = c.id AND npa = $areacode AND nxx = $prefix ";
		if ($number) {
			$R=$this->$dbcon->Execute($sql)or DIE($sql.$this->$dbcon->ErrorMsg());
			$carrier = $R->Fields('name');
		}
		return $carrier;
	}
	
	function domain_lookup($carrier) {
		$sql = "SELECT domain from carreir where carrier ='$carreir'"; 
		$R=$this->$dbcon->Execute($sql)or DIE($sql.$this->$dbcon->ErrorMsg());
		return $R->Fields("domain");
	}
	
	// a function that creats an email ouit of the number and carrier 
	function get_carrier($carrier,$number){
		$sql = "select  domain from carrier where name = '$carrier'"; 
		$R=$this->$dbcon->Execute($sql) or DIE($this->$dbcon->ErrorMsg());
		$email = $R->Fields("domain");
		$email = $this->cleannumber($number).'@'.$email;
		return $email;	
	
	}
	
	
	####  BLAST FUNCTIONS ####
	
	//a function that does the sending of a message
	function send_sms($email,$message,$subject=NULL,$from=NULL) {
		if (mail($email,$subject,$message,"From: $from\r\nReply-To: $from\r\nX-Sender: $from\r\nReturn-Path: $from", "-f$from")) {
			return true;
		}
	}
	
	
	//function that sends all the messages in the message que of a certian blast
	function send_messages() {
		$sql = "Select u.Phone_Provider, u.Cell_Phone, b.message_sms ,b.from_email, b.subject, m.message_ID, m.user_ID from messages_to_contacts m, userdata u, blast b  where m.user_ID= u.id and m.blast_ID = b.blast_ID  and status = 'New' and blast_ID = ".$this->$blast_ID;
		$R = $this->$dbcon->Execute($sql)or DIE($sql.$this->$dbcon->ErrorMsg());
		$this->set_blast_status("Sending Messages");
		$this->set_start_time();
		$good =0;
		$bad =0;
		$total=0;
		while (!$R->EOF) {
			$email = $this->get_carrier($R->Fields("Phone_Provider"),$R->Fields("Cell_Phone"));
			if  ($email) {
				$this->set_message_status($R->Fields("message_ID"),"Sending");
				if ($this->send_sms($email,$R->Fields("message_sms"),$R->Fields("subject"),$R->Fields("from_email"))) {
					$i++;
					$this->set_message_status($R->Fields("message_ID"),"Done");
				} else {
					$bad++;
					$this->set_message_status($R->Fields("message_ID"),"Server Failure");
				}
			} else {
				$bad++;
				$this->set_message_status($R->Fields("message_ID"),"Bad Address");
			}
			$total;
			$R->MoveNext();		
		}
		$response = "$good messages sent, $bad messages failed to send in $total attempts.<br>";
		$this->set_blast_status($blast_ID,"Complete");		
		$this->set_start_time($blast_ID);
		
		return $response;
	}
	
	## Functions for email subscription 
	
	function sms_pop_subscription($host,$port=995,$login,$password,$list_ID){
	
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
				$number = $this->parse_email($headers['From']);
				echo $number['number'].$number['carrier'];
				$this->add_user_sms($list_ID,$number['number'],$number['carrier']);
				$pop3->deleteMsg($x);
				$x++;
			}
		}
		$pop3->disconnect(); 
	}
	
	//a function that subscribers a user to a list
	function add_user_sms($list_ID,$Cell_Phone,$Carrier) {
		$this->$dbcon->Execute("insert into userdata (Cell_Phone,Phone_Provider,modin) values ('$Cell_Phone','$Carrier','3')") or die($this->$dbcon->errorMsg());
		$user= $this->$dbcon->Insert_ID();
		$this->$dbcon->Execute("insert into lists_to_contacts (list_ID,user_ID) values ('$list_ID','$user')") or die($this->$dbcon->errorMsg());	
	}
	
	// a function that parses a sms email into carrier and number
	###NOT DONE #####
	function parse_email($email) {
		
		$niceemail = preg_replace( "/[^\d]*(\d*)@([\w\.]*)[^\w]*/", "\$1:\$2", $email );
		$parsed_email = explode( ":",$niceemail );
		//print $niceemail;
		$user = $parsed_email[0];
		$domain = $parsed_email[1];
		
		//die($user.'<br>'.$domain);
		if ($domain) {
			$sql = 'select name from carrier where domain = "'.$domain.'"'; 
			$R=$this->$dbcon->Execute($sql) or DIE($this->$dbcon->ErrorMsg());
			if ($R->fields("name")) {
				$var = array('number'=>$user,'domain'=>$domain,'carrier'=>$R->fields("name"));
				return $var;
			}
		}
	}
	
	
}
?>