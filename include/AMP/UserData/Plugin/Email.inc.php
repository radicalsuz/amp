<?php

require_once ('AMP/System/IntroText.inc.php' );

class UserDataPlugin_Email extends UserDataPlugin {

    // Store message.
    var $message = '';
    var $header = '';

    var $options = array(

                'mailto'      => array( 'label' => 'Email Address',
                                        'type'        => 'text',
                                        'required'    => true ,
                                        'available'   => true),

                'from'	      => array( 'label' => 'Email From',
                                        'type'        => 'text',
                                        'available'   => true),

                'subject'     => array( 'label' => 'Email Subject',
                                        'available'   => true,
                                        'type'        => 'text',
                                        'required'    => true ),

                'format'      => array( 'label' => 'Email Format',
                                        'available'   => true,
                                        'type'        => 'select',
                                        'values'      => array( 'Plain Text' => 'Text' ),
                                        'default'     => 'Text' ),

                'intro_text'  => array( 'label' => 'Email Intro Text',
                                        'type'        => 'select',
                                        'available'   => true,
                                        'values'      => '',
                                        'default'     => '' ),

                'footer_text' => array( 'label' => 'Email Footer Text',
                                        'available'   => true,
                                        'type'        => 'select',
                                        'default'     => '', 
                                        'values'      => ''),

                'update_page' => array( 'default' => 'modinput4.php',
                                        'available'=>true,
                                        'type'=>'text',
                                        'label'=>'Edit Page'),
         );

    function execute ( $options = null ) {

        // Allow for pre-processing of message & options. Return anything but
        // true to abort message send.
        if (method_exists( $this, 'preProcess' )) {
            $rt = $this->preProcess();
            if ($rt !== true) return $rt;
        }

        //get options
        $options = array_merge($this->getOptions(), $options);


        // Header text.
        $this->message .= $this->_getBodyHeader ( $options );

        $this->message .= $this->prepareMessage( $options );

        // Footer Text.
        $this->message .= $this->_getBodyFooter ( $options );

        // Construct the header.
        $this->header = $this->prepareHeader( $options );

        // Allow for post-processing. Returrn anything but true to abort
        // message send.
        if (method_exists($this, 'postProcess')) {
            $rt = $this->postProcess();
            if ($rt !== true) return $rt;
        }

        if (!isset( $options['mailto'] )) return false;

        // Send the mail.
        return mail( $options['mailto'], $options['subject'], $this->message, $this->header );

    }

    /*****
     *
     * prepareHeader ()
     *
     * Creates a header appropriate to UserDataModule, common
     * to all UDM mailout functions.
     *
     *****/

    function prepareHeader ( $options = null) {

		if(isset($options['from']) && $options['from']) {
			$header  = "From: " . $options['from'];
		} else {
			$header  = "From: " . $GLOBALS['MM_email_from'];
		}
        $header .= "\nX-Mailer: AMP/UserDataMail\n";

		if('html' == strtolower($options['format'])) {
			$header .= "Content-Type: text/html; charset=utf-8\r\n" .
					   "Content-Transfer-Encoding: 8bit\r\n\r\n";
		}
        return $header;

    }


    function prepareMessage ( $options ) {

        // This function *must* be overridden, or you'll end up with a useless
        // email.

        trigger_error( "There was an error submitting the form: Email message incomplete.", E_USER_WARNING );
    }

    function _getBodyHeader( $options ) {
        if (!(isset($options['intro_text']) && $options['intro_text'])) return false;
        return $this->_readIntroText( $options['intro_text'] );
    }

    function _getBodyFooter ( $options ) {
        if (!(isset($options['footer_text']) && $options['footer_text'])) return false;
        return $this->_readIntroText( $options['footer_text'] );
    }

    function _readIntroText( $id ) {
        $system_texts = AMPSystem_Lookup::instance('introTexts');
        if (!isset($system_texts[ $id ])) return $id;

        $textdata = & new AMPSystem_IntroText( $this->dbcon, $id );
		if($textdata->isHtml()) $this->containsHTML(true);
        return AMPDisplay_HTML::_activateIncludes($textdata->getBody()) . "\n\n";
    }

	function containsHTML( $flag = null ) {
		if(isset($flag)) {
			$this->_containsHTML = $flag;
		}
		return $this->_containsHTML;
	}

}

?>
