<?php

class UserDataPlugin_Email extends UserDataPlugin {

    // Store message.
    var $message = '';
    var $header = '';

    var $option_defs = array(

                'mailto'      => array( 'description' => 'Email Address',
                                        'type'        => 'text',
                                        'required'    => true ),

                'subject'     => array( 'description' => 'Email Subject',
                                        'type'        => 'text',
                                        'required'    => true ),

                'format'      => array( 'description' => 'Email Format',
                                        'type'        => 'select',
                                        'values'      => array( 'Plain Text' => 'text' ),
                                        'default'     => 'text' ),

                'intro_text'  => array( 'description' => 'Email Intro Text',
                                        'type'        => 'select',
                                        'values'      => '',
                                        'default'     => '' ),

                'footer_text' => array( 'description' => 'Email Footer Text',
                                        'type'        => 'select',
                                        'values'      => '',
                                        'default'     => '' )
         );

    function execute ( $options = null ) {

        if (!isset($options)) {
            $options =& $this->options;
        }

        // Allow for pre-processing of message & options. Return anything but
        // true to abort message send.
        if (method_exists( $this, 'preProcess' )) {
            $rt = $this->preProcess();
            if ($rt !== true) return $rt;
        }

        // Header text.
        if (isset($options['intro_text'])) {
            $sql      = "SELECT text FROM moduletext WHERE id=" . $dbcon->qstr( $options['intro_text'] );
            $rs       = $dbcon->CacheExecute($sql);
            $message .= $rs->Fields('text') . "\n\n";
        }

        $this->message .= $this->prepareMessage( $options );

        // Footer Text.
        if (isset($options['footer_text'])) {
            if (is_int($options['footer_text'])) {
                $sql            = "SELECT text FROM moduletext WHERE id=" . $dbcon->qstr( $options['footer_text'] );
                $rs             = $dbcon->CacheExecute($sql);
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
