<?php

class AMPSystem_Email {

    var $_sender = AMP_SITE_EMAIL_SENDER;
    var $_senderName = AMP_SITE_NAME;

    var $_mailto;
    var $_message;
    var $_subject;
    var $_format;

    var $_parameters = array( );


    function AMPSystem_Email() {
		$this->init();
    }

	function init() {
		if(!defined('AMP_MAIL_ENVELOPE_SENDER')) {
			define('AMP_MAIL_ENVELOPE_SENDER', 'automated@radicaldesigns.org');
		}
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

    function setTarget( $address ){
        return $this->setRecipient( $address );
    }

    function getTarget( ){
        return $this->getRecipient( );
    }

    function prepareHeader () {

        if (! ($from = $this->getSender())){
            trigger_error( AMP_TEXT_ERROR_EMAIL_SENDER_NOT_SET );
            return false;
        }
        
        if ($sender_name = $this->getSenderName() ) {
            $from = $sender_name . " <" . $from .">";
        }
        $header  = "From: " . $this->sanitize($from) . "\n";
        $header .= "X-Mailer: AMP/SystemMail\n";

        return $header;
    }

	function sanitize($content) {
		if (eregi("\r",$content) || eregi("\n",$content)){
			trigger_error("Possible Spam at ".time()." :(".$content.")");
			die("Possible Spam at ".time()." :(".$content.")");
		}
		return $content;
	}

    function execute() {
        if (method_exists( $this, 'preProcess' )) {
            $result = $this->preProcess();
            if ($rt !== true) return $rt;
        }

        if (!($header   =  $this->prepareHeader() ))return false;
        if (!($message  =  $this->getMessage() ))   {
            trigger_error( AMP_TEXT_ERROR_EMAIL_MESSAGE_NOT_SET );
            return false;
        }
        if (!($mailto   =  $this->getRecipient() ))    {
            trigger_error( AMP_TEXT_ERROR_EMAIL_TARGET_NOT_SET );
            return false;

        }

        return mail( $mailto, $this->getSubject(), $message, $header, $this->getAdditionalParameters() );
    }


	function getAdditionalParameters() {
		if(defined('AMP_MAIL_ENVELOPE_SENDER') && AMP_MAIL_ENVELOPE_SENDER) {
			$this->addParameter( '-f '.AMP_MAIL_ENVELOPE_SENDER );
		}

		if(empty($this->_parameters)) {
			return null;
		}

		return join(' ', $this->_parameters);
	}

    function addParameter( $value ){
        return $this->_parameters[] = $value;
    }
}
?>
