<?php

require_once( 'AMP/UserData/Plugin/Authenticate.inc.php' );

class UserData_Plugin_Authenticate_AMP extends UserData_Plugin_Authenticate {

    function __doAuthenticate() {

        if (isset( $_REQUEST['otp'] )) $this->authtok = $_REQUEST['otp'];
                                  else return false;

        $this->authtok = $_REQUEST

        $dbcon =& $this->dbcon;

        $sql  = "SELECT * FROM userdata_auth WHERE";
        $sql .= " uid = " . $dbcon->qstr( $this->user );
        $sql .= " AND otp = " . $dbcon->qstr( $this->authtok );
        $sql .= " ";

}

/*****
 *
 * User Data Authentication Module
 *
 *****/

// Make logins valid for a two hour window.
define( 'UDM_AMP_AUTHENTICATE_VALIDITY', 7200 );

function udm_amp_authenticate ( &$udm, $options = null ) {

    $authStatus = false;

    $uid  = $options[ 'uid' ];
    $pass = $options[ 'pass' ];

    if ( $uid && $pass ) {

        $dbcon =& $udm->dbcon;

        // Check the database for matching rows.
        $sql .= "SELECT * FROM userdata_auth WHERE ";
        $sql .= "uid=" . $dbcon->qstr( $uid );
        $sql .= " AND otp=" . $dbcon->qstr( $pass );
        $sql .= " AND ABS( NOW() - valid ) < " . UDM_AMP_AUTHENTICATE_VALIDITY;

        // Having a cached execute may result in some users not being
        // able to login immediately, but may protect against some DOS attacks.
        $rs = $dbcon->CacheExecute( $sql ) or die( "Couldn't obtain login information: " . $dbcon->ErrorMsg() );

        if ( $rs->RecordCount() >= 1 ) {

            $authStatus = true;
            $udm->authorized = true;
            $udm->uid = $uid;
            $udm->pass = $pass;

        } else {
   
            $authStatus = false;
            $udm->authenticated = false;
            $udm->uid = $uid;
            $udm->pass = null;

        }

    } elseif ( $uid ) {

        $dbcon =& $udm->dbcon;

        $otp = sha1( mt_rand() . $_SERVER['REMOTE_HOST'] . $uid . mt_rand() );

        // Insert the temporary (not really a OTP) password into the database.
        // Validity is set by SQL column DEFAULTS.
        $sql  = "INSERT INTO userdata_auth ( uid, otp ) VALUES ( ";
        $sql .= join( ", ", array( $dbcon->qstr( $uid ), $dbcon->qstr( $otp ) ) ) . " )";

        $rs = $dbcon->Execute( $sql ) or die( "Couldn't obtain authentication information: " . $dbcon->ErrorMsg() );

        if ( $rs ) {

            // This should be changed to use a UDM mail API, rather than delivering
            // the message manuall.y

            $sql  = "SELECT email FROM userdata WHERE id=" . $dbcon->qstr( $uid ) . " AND email != ''";
            $rs = $dbcon->CacheExecute( $sql ) or die( "Error fetching user information: " . $dbcon->ErrorMsg() );

            if ( $row = $rs->FetchRow() ) {

                $mailto = $row['email'];

                $message  = "\n\nUse the following link to edit your data:\n\n";
                $message .= "http://" . $_SERVER['SERVER_NAME'] . $_SERVER['PHP_SELF'] . '?modin=' . $udm->instance;
                $message .= '&uid=' . $uid . '&otp=' . $otp;
                $message .= "\n\nAlternatively, paste the following passphrase ";
                $message .= "into the form in your browser: $otp";
            
                $header = $udm->_make_mail_header();

                $result = mail( $mailto, 'Your Website Login', $message, $header );

                if ( $result ) {
                    $udm->addResult( 'authenticate', "An email was sent to $mailto containing login information so that you can update your information. Please click on the link in the email, or enter the password from the email in the Password field below." );
		    		$fields[ 'otp' ] = array( 'label' => 'Password', 'public' => 1, 'type' => 'text', 'size' => 30 );
					$udm->fields = $fields + $udm->fields;
					$udm->_module_def = join( ",", array( "otp", $udm->_module_def[ 'field_order' ] ) );
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

?>
