<?php

define ( 'AMP_BLAST_STATUS_NEW', 'New' );
define( 'AMP_BLAST_NO_RECORD_ID', 8000000);

require_once( 'AMP/System/Data/Item.inc.php');
require_once ('AMP/Blast/EmailBlast.php');
require_once ('AMP/System/Blast/EmailSet.inc.php');

class AMPSystem_Blast extends AMPSystem_Data_Item {

    var $datatable = "blast";
    var $id_fields = "blast_ID";

    function AMPSystem_Blast( &$dbcon, $blast_ID = null ) {
        $this->init( $dbcon, $blast_ID );
        $this->blast = & new EmailBlast( $this->dbcon );
    }

    function getStatus() {
        if (!($status = $this->getData( 'status' ))) return AMP_BLAST_STATUS_NEW;
    }

    function getEmails( $sql ) {
        $emails = array();
        $emailset = &new BlastEmailSet( $this->dbcon );
        if (!$emailset->doStraightSQL( "Select distinct Email ".$sql )) return false;
        while( $row = $emailset->getData() ) {
            $emails[] = $row['Email'];
        }
        return ($this->emails = $emails);
    }

    function send( $message ) {
        if (!isset($this->emails)) return false;
        return $this->blast->new_system_blast( $this->emails, $message );
    }

    function returnPOSTmessage() {
        $message_elements = array( 'subject', 'messagetext', 'messagehtml', 'from_email', 'from_name', 'replyto_email' );
        return array_key_combine( $message_elements, $_POST );
    }
}

?>
