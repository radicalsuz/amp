<?php

class AMPSystem_Email {

    var $_sender;
    var $_senderName;

    var $_mailto;
    var $_message;
    var $_subject;
    var $_format;


    function AMPSystem_Email() {
        $this->init();
    }

    function init() {
        $reg = &AMP_Registry::instance();
        $this->setSenderName( $reg->getEntry( AMP_REGISTRY_SETTING_SITENAME ) ); 
        $this->setSender( $reg->getEntry( AMP_REGISTRY_SETTING_EMAIL_SENDER ) ); 
    }

    function setMessage($message) {
        $this->_message = $message;
    }

    function setRecipient( $address ) {
        $this->_mailto = $address;
    }

    function setSubject( $subject ) {
        $this->_subject = $subject;
    }

    function setFormat( $format ) {
        $this->_format = $format;
    }

    function setSender( $address ) {
        $this->_sender = $address;
    }

    function setSenderName( $name ) {
        $this->_senderName = $name;
    }

    function getSenderName() {
        if (!isset($this->_senderName)) return false;
        return $this->_senderName;
    }

    function getSender() {
        if (!isset($this->_sender)) return false;
        return $this->_sender;
    }

    function getSubject() {
        if (!isset($this->_subject)) return false;
        return $this->_subject;
    }

    function getMessage() {
        if (!isset($this->_message)) return false;
        return $this->_message;
    }

    function getRecipient() {
        if (!isset($this->_mailto)) return false;
        return $this->_mailto;
    }

    function prepareHeader () {

        if (! ($from = $this->getSender())) return false;
        
        if ($sender_name = $this->getSenderName() ) {
            $from = $sender_name . " <" . $from .">";
        }
        $header  = "From: " . $from;
        $header .= "\nX-Mailer: AMP/SystemMail\n";

        return $header;
    }

    function execute() {
        if (method_exists( $this, 'preProcess' )) {
            $result = $this->preProcess();
            if ($rt !== true) return $rt;
        }

        if (!($header   =  $this->prepareHeader() ))return false;
        if (!($message  =  $this->getMessage() ))   return false;
        if (!($mailto   =  $this->getRecipient() ))    return false;

        return mail( $mailto, $this->getSubject(), $message, $header );
    }

}
?>
