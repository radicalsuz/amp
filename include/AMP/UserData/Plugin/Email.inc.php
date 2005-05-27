<?php

class UserDataPlugin_Email extends UserDataPlugin {

    // Store message.
    var $message = '';
    var $header = '';

    var $options = array(

                'mailto'      => array( 'label' => 'Email Address',
                                        'type'        => 'text',
                                        'required'    => true ,
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
        if (isset($options['intro_text']) && $options['intro_text']) {
            $sql      = "SELECT text FROM moduletext WHERE id=" . $this->dbcon->qstr( $options['intro_text'] );
            $rs       = $this->dbcon->CacheExecute($sql);
            $message .= $rs->Fields('text') . "\n\n";
        }

        $this->message .= $this->prepareMessage( $options );

        // Footer Text.
        if (isset($options['footer_text']) && $options['footer_text']) {
            if (is_int($options['footer_text'])) {
                $sql            = "SELECT text FROM moduletext WHERE id=" . $this->dbcon->qstr( $options['footer_text'] );
                $rs             = $this->dbcon->CacheExecute($sql);
                $this->message .= $rs->Fields('text');
            } else {
                $this->message .= $options['footer_text'];
            }
        }

        // Construct the header.
        $this->header = $this->prepareHeader();

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

    function prepareHeader () {

        $header  = "From: " . $GLOBALS['MM_email_from'];
        $header .= "\nX-Mailer: AMP/UserDataMail\n";

        return $header;

    }


    function prepareMessage ( $options ) {

        // This function *must* be overridden, or you'll end up with a useless
        // email.

        trigger_error( "There was an error submitting the form: Email message incomplete.", E_USER_WARNING );
    }
}

?>
