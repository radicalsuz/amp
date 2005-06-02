<?php

/* * * * * * *
 *  PhpList Plugin
 *  Automatically publishes a record when it is saved
 *  only works from the user side
 *
 *  Author: david@radicaldesigns.org
 *  6/1/2005
 */
 
require_once ('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Save_PHPlist extends UserDataPlugin {
    
    var $name = 'PHPlist';
	var $long_name   = 'PHP List Subscription ';
    var $description = 'Subscribes users to PHPlist Lists';
    var $available = true;

     function UserdataPlugin_Save_PHPlist ( &$udm , $plugin_instance=null){
        $this->init( $udm, $plugin_instance );
		 
    }
	 
    function execute ( $options = null ) {
		$options = $this->getOptions();
		$this->_field_prefix='';
		$data = $this->getData();
		
		if ( $this->udm->uselists ) {
			$emailid = $this->check_email($data['Email']);
			if (!$emailid) {
				$rndVal = md5(uniqid(mt_rand()));
				$sql  = "INSERT INTO phplist_user_user (";
				$sql .= " email, confirmed, uniqid, htmlemail, entered,  foreignkey ) VALUES ('";
				$sql .= $data['Email'];
				$sql .= "', 1, '" . $rndVal . "', ".$data['custom1'].", NOW(), ".$this->udm->uid." )";
				$rs = $this->dbcon->Execute( $sql )or DIE("add to phplist error ".$sql.$this->dbcon->ErrorMsg()); ;
				
				$emailid = $this->dbcon->Insert_ID( );
				$this->add_att($emailid,$data);
			}
			
			foreach ( array_keys( $this->udm->lists ) as $list_id ) {
				$list_fields[] = 'list_' . $list_id;
			}
			$listValues = $this->udm->form->exportValues( $list_fields );
			foreach ( $listValues as $listField => $value ) {
	
				$listid = substr( $listField, 5 );
				if ( $value ) {
					if (!$this->check_sub($emailid,$listid)) {
						$sql ="INSERT INTO phplist_listuser (userid, listid, entered) VALUES ('".$emailid."', '".$listid."', NOW())";
						$rs = $this->dbcon->Execute( $sql )or DIE("add to phplist error ".$sql.$this->dbcon->ErrorMsg());
					}
				}
			
			}

	    }
	}

	function check_email($email) {
		$rs = $this->dbcon->Execute( "select id from phplist_user_user where email = '".$email."'" );
		if ($rs->Fields("id")) {
			return $rs->Fields("id");
		} else  {
			return FALSE;
		}
	}

	function check_sub($email,$list) {
		$rs = $this->dbcon->Execute( "select userid from phplist_listuser where userid = '".$email."' and listid = '".$list."' " );
		if ($rs->Fields("userid")) {
			return TRUE;
		} else  {
			return FALSE;
		}
	}
	function add_att($emailid,$data) {
		$d = array('1'=>'First_Name',
					'2'=>'Country',
					'20'=>'Zip',
					'19'=>'Company',
					'13'=>'Street',
					'14'=>'City',
					'12'=>'Last_Name',
					'22'=>'State',
					'23'=>'notes',
					'24'=>'Fax',
					'25'=>'Phone',
					'26'=>'Street_2',
					'27'=>'Web_Page');
		foreach ( $d as $att_id => $field ) {	
			$sql ="Insert into phplist_user_user_attribute (attributeid,userid,value) VALUES('".$att_id."','".$emailid."','".$data[$field]."') ";
			$rs = $this->dbcon->Execute( $sql )or DIE("add to phplist error ".$sql.$this->dbcon->ErrorMsg());
		
		}
	}

}


