<?php

require_once ('AMP/System/IntroText.inc.php' );
require_once ('AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Email extends UserDataPlugin {

    // Store message.
    var $message = '';
    var $header = '';
    var $emailer;

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
                                        'values'      => array( 'Plain Text' => 'Text', 'HTML' => 'HTML' ),
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

                'form_data_intro' => array( 'available'=>true,
						                    'type'=>'text',
						                    'label'=>'Introduction to form data' )
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

        //invoke system email
        require_once( 'AMP/System/Email.inc.php');
        $emailer = &new AMPSystem_Email( );
        $this->emailer = & $emailer;

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

        $email_target = $this->getEmailTarget( $options );
        if (!$email_target) return false;

        // Send the mail.
        $emailer->setTarget( $email_target );
        $emailer->setSubject( $options['subject'] );
        $emailer->setMessage( $this->message );
        return $emailer->execute( );

        #return mail( $options['mailto'], $options['subject'], $this->message, $this->header );

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

        $safe_sender = $this->sanitize( AMP_SITE_EMAIL_SENDER );
		if(isset($options['from']) && $options['from']) {
            $safe_sender  = $this->sanitize( $options['from'] );
		} 

        $header  = "From: " . $safe_sender;
        if ( isset( $this->emailer )) {
            $this->emailer->setSender( $safe_sender );
        }

        $header .= "\nX-Mailer: AMP/UserDataMail\n";

		if('html' == strtolower($options['format'])) {
            $content_header =  "Content-Type: text/html; charset=utf-8\r\n" .
			           		   "Content-Transfer-Encoding: 8bit\r\n\r\n";
			$header .=  $content_header;

            if ( isset( $this->emailer )) {
                $this->emailer->addParameter( $content_header );
            }
		}
        return $header;

    }

	function sanitize($content) {
		if (eregi("\r",$content) || eregi("\n",$content)){
			trigger_error("Possible Spam at ".time()." :(".$content.")");
			die("Possible Spam at ".time()." :(".$content.")");
		}
		return $content;
	}

    function getEmailTarget( $options = null ){
        if ( isset( $options ) && isset( $options['mailto'])){
            return $options['mailto'];
        }
        return false;
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

    function _registerIntroTextOptions( ){
        require_once( 'AMP/UserData/Lookups.inc.php');
        $introtexts_blank_row[ '' ] = '--';
        #$modules = $modules_blank_row + FormLookup_IntroTexts::instance( $this->udm->instance );
        $introtexts = $introtexts_blank_row + AMPSystem_Lookup::instance( 'introtexts');
        $this->options['intro_text']['values'] = $introtexts;
        $this->options['footer_text']['values'] = $introtexts;
    }

    function _register_options_dynamic() {
        $this->_registerIntroTextOptions( );
    }

}

?>
