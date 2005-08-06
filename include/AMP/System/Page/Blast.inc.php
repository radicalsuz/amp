<?php

define ( 'AMP_ERROR_BLAST_NO_EMAILS', 'Email Blast Failed: No Recipients Selected' );
define ( 'AMP_ERROR_BLAST_NO_MESSAGE', 'Email Blast Failed: Message not set' );

require_once( 'AMP/System/Page.inc.php' );

class AMPSystemPage_Blast extends AMPSystem_Page {

    var $action = "Send";

    function AMPSystemPage_Blast( &$dbcon, &$map ) {
        $this->init( $dbcon, $map );
    }

    function execute() {
        $this->_initForm();
        $action = $this->form->submitted();
        if (!$action) $action = "read";
        return $this->doAction( $action );
    }

    function commitSend() {
        $this->addComponent('form');
        if (!$this->form->validate()) return false;

        $messageData_keys = array( 'subject', 'messagetext', 'messagehtml', 'from_email', 'from_name', 'replyto_email' );
        $messageData = $this->form->getValues( $messageData_keys );
        
        $emailsql = "";
        $emailsData = $this->form->getValues( array('passedsql', 'modin') );
        if (isset($emailsData['passedsql']) && $emailsData['passedsql']) $emailsql = $emailsData['passedsql'];
        if (isset($emailsData['modin']) && $emailsData['modin'] && is_numeric( $emailsData['modin'] )) 
            $emailsql = " from userdata where modin=" . $emailsData['modin'] . ' and !isnull(Email) and Email!=""';

        if (!($emailsql && count($messageData))) {
            if (!$emailsql) $error = AMP_ERROR_BLAST_NO_EMAILS;
            if (!count($messageData)) $error = AMP_ERROR_BLAST_NO_MESSAGE;
            $this->setMessage ( $error, true );
            return false;
        }

        $this->_initComponents( 'source' );
        $this->source->getEmails( $emailsql );
        $response = $this->source->send( $messageData );

        $this->setMessage( $response );

        return true;
    }
}

?>
