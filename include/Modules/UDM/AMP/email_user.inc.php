<?php

/*****
 *
 * udm_amp_email_user ()
 *
 * Email a given user with information about how to update
 * their submitted info.
 *
 *****/

function udm_AMP_email_user ( &$udm, $options = null ) {

	//set some default options
	
	$default_options['edit_page']='modinput4.php';
	$default_options['external_link_to']='id';
	$default_options['external_fields_to_return']='*';
	$default_options['subject']="Update Your Posting";


	//pass default options into standard options array when no value exists
	foreach ($default_options as $key=>$this_option) {
		if (!isset($options[$key])) { $options[$key]=$this_option;}
	}
	
	//Custom format flag allows for alternate mailout formats
	if (isset($options['custom_format'])){
		return $udm->doAction("Output", $options['custom_format'], $options);
	}

	//Intro text can be pulled from moduletext table
	if (isset($options['intro_text'])) {
		$sql="Select test from moduletext where id=".$options['intro_text'];
		$rs_text=$udm->dbcon->Execute($sql);
		$message .= $rs_text->Fields("test");
	}

	//Update Link
	$message  = "Please go to " . $GLOBALS['Web_url'];
    $message .= $options['edit_page']."?modin=" . $udm->instance;
    $message .= "&uid=" . $udm->uid;
    $message .= " to update your information.\n\n";

	//Text output of the form data
 	$message .= $udm->output( 'text' );
    
	//footer text for those that just can't be quiet
	if (isset($options['footer_text'])) {
		$sql="Select test from moduletext where id=".$options['footer_text'];
		$rs_text=$udm->dbcon->Execute($sql);
		$message .= $rs_text->Fields("test");
	}

	//external info for linked data
	if (isset($options['external_table'])&&isset($options['external_linkfield'])) {
		$sql="SELECT ".$options['external_fields_to_return']." from ".$options['external_table']." WHERE ".$options['external_linkfield']." = ".$options['external_link_to'];
		$message.= $sql;
		if($rs=$udm->dbcon->GetArray($sql)) {
			$message.="\n\n";
			foreach($rs as $current_row) {
				foreach ($current_row as $fieldname=>$fieldvalue) {
					$message.= $fieldname." : ".$fieldvalue."   , ";
				}
				$message.="\n";
			}
		}
	} 




    $subject = $options['subject'];

    $mailto  = $udm->form->exportValue( 'Email' );
    $header  = $udm->_make_mail_header();
    
    return mail( $mailto, $subject, $message, $header );
 
}

?>
