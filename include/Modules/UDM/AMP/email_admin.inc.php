<?php

/*****
 *
 * udm_amp_email_admin ()
 *
 * if administrator notification is enabled, this function
 * will notify the administrator that a record has been added,
 * along with a text rendering of the data and a link for approval.
 *
 *****/

function udm_amp_email_admin ( $udm, $options = null ) {

	
	if ( !isset( $udm->mailto ) ) return false;

	//set some default options
	
	$default_options['system_edit_page']='modinput4_view.php';
	$default_options['external_link_to']='id';
	$default_options['external_fields_to_return']='*';


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

	//Text output of the form data
    $message  .= $udm->output( 'text' );
     
	 //edit/publish link
    $message .= "\n\nPlease visit " . $GLOBALS['Web_url'];
    $message .= "system/".$options['system_edit_page']."?modin=" . $udm->instance;
    $message .= "&uid=" . $udm->uid;
    $message .= " to publish.";


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
    
	//footer text for those that just can't be quiet
	if (isset($options['footer_text'])) {
		$sql="Select test from moduletext where id=".$options['footer_text'];
		$rs_text=$udm->dbcon->Execute($sql);
		$message .= $rs_text->Fields("test");
	}

	
	$header = $udm->_make_mail_header();
        
    return mail( $udm->mailto, $udm->subject, $message, $header );

}

?>
