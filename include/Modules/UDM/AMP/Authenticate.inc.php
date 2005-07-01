<?php

require_once( 'AMP/UserData/Plugin.inc.php' );

class UserDataPlugin_Authenticate_AMP extends UserDataPlugin {

    var $short_name  = 'AMPAUTH';
    var $long_name   = 'Simple Authentication Module';
    var $description = 'Authenticates users, enabling them to update their data';

    var $options = array( 'validity' => array( 'description' => 'Session Validity (seconds)',
                                               'label'       => 'Session Validity (seconds)',
                                               'type'        => 'text',
                                               'size'        => '5',
                                               'default'     => '7200',
                                               'available'   => true ),
                           'uid' =>  array( 'label' => 'User ID',
                                            'default'     => null,
                                            'available'   => false ),
                           'pass' => array( 'label' => 'Password',
                                            'available' => false ) 
                           
                           );

    var $available = true;

    function UserDataPlugin_Authenticate_AMP ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function execute ( $options = null ) {

        $options = array_merge($this->getOptions(), $options);

        $udm =& $this->udm;

        $authStatus = false;

        $uid  = $options[ 'uid' ];
        $pass = $options[ 'pass' ];

        if ( $uid && $pass ) {

            $dbcon =& $udm->dbcon;

            // Check the database for matching rows.
            $sql  = "SELECT * FROM userdata_auth WHERE";
            $sql .= " uid=" . $dbcon->qstr( $uid );
            $sql .= " AND otp=" . $dbcon->qstr( $pass );
            $sql .= " AND ABS( NOW() - valid ) < " . $options['validity'];

            // Having a cached execute may result in some users not being
            // able to login immediately, but may protect against some DOS attacks.
            $rs = $dbcon->CacheExecute( $sql ) or die( "Couldn't obtain login information: " . $dbcon->ErrorMsg() );

            if ( $rs->RecordCount() >= 1 ) {

                $authStatus = true;
                $udm->authorized = true;
                $udm->uid = $uid;
                $udm->pass = $pass;
                $this->addAuthFields( $uid, 'hidden' );

            } else {
    
                $authStatus = false;
                $udm->authorized = false;
                $udm->uid = $uid;
                $udm->pass = null;

            }

        } elseif ( $uid ) {

            $dbcon =& $udm->dbcon;

            $host_sha_seed = (isset($_SERVER['REMOTE_HOST'])) ? $_SERVER['REMOTE_HOST'] : $_SERVER['REMOTE_ADDR'];

            $otp = sha1( mt_rand() . $host_sha_seed . $uid . mt_rand() );

            // Insert the temporary (not really a OTP) password into the database.
            // Validity is set by SQL column DEFAULTS.
            $sql  = "INSERT INTO userdata_auth ( uid, otp ) VALUES ( ";
            $sql .= join( ", ", array( $dbcon->qstr( $uid ), $dbcon->qstr( $otp ) ) ) . " )";

            $rs = $dbcon->Execute( $sql ) or die( "Couldn't obtain authentication information: " . $dbcon->ErrorMsg() );

            if ( $rs ) {

                // This should be changed to use a UDM mail API, rather than delivering
                // the message manually

                $sql  = "SELECT email FROM userdata WHERE id=" . $dbcon->qstr( $uid ) . " AND email != ''";
                $rs = $dbcon->CacheExecute( $sql ) or die( "Error fetching user information: " . $dbcon->ErrorMsg() );

                if ( $row = $rs->FetchRow() ) {

                    $mailto = $row['email'];

                    $message  = "\n\nUse the following link to edit your data:\n\n";
                    $message .= "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?modin=' . $udm->instance;
                    $message .= '&uid=' . $uid . '&otp=' . $otp;
                    $message .= "\n\nAlternatively, paste the following passphrase ";
                    $message .= "into the form in your browser: $otp";
                
                    $header = $this->_make_mail_header();

                    $result = mail( $mailto, 'Your Website Login', $message, $header );

                    if ( $result ) {
                        $udm->addResult( 'authenticate', "An email was sent to $mailto containing login information so that you can update your information. Please click on the link in the email, or enter the password from the email in the Password field below." );
                        $this->addAuthFields( $uid );
                        $udm->insertBeforeFieldOrder( array( "otp" ) );
                    } else {
                        $udm->addError( 'authenticate', "There was a problem delivering your login information to the email address you specified. Please contact the system administrator." );
                    }

                } else {

                    $udm->addError( 'missing', 'No valid email address present for that record.' ); 

                }

            } else {

                $udm->addError( 'dberror', 'There was an error creating a new password.' );
            
            }
        }

        return $authStatus;
    }

    function addAuthFields( $uid, $type = 'text' ) {
        $fields = array(
            'otp' => array( 
                'label' => '<B>Password</B>', 
                'public' => 1, 
                'enabled' => 1, 
                'default' => $this->udm->pass,
                'type' => $type, 
                'size' => 30 ),
            'uid' => array(
                'public'=> true,
                'value' => $uid,
                'default' => $uid,
                'constant' => true,
                'type' => 'hidden',
                'enabled'=> true ) 
            );
        if ($this->udm->authorized) $fields['otp']['constant'] = true;
        $this->udm->addFields( $fields );
    }

    function _make_mail_header () {
        $header  = "From: " . $GLOBALS['MM_email_from'];
        $header .= "\nX-Mailer: AMP/UserDataModule\n";

        return $header;
    }
}

?>
