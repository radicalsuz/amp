<?php

class AMP_Authentication_Handler {

    var $user;
    var $permission;

    var $has_cookie = false;

    var $message_type = 'Error';
    var $message;

    var $timeout = 1800;					// default timeout of 30 minutes.

    var $dbcon;

    var $userid;

    function AMP_Authentication_Handler ( $dbcon, $timeout = null ) {

        $this->dbcon = $dbcon;

        if (is_int($timeout)) {
            $this->timeout = $timeout;
        }

    }

    function is_authenticated () {

        if ( $this->check_authen_credentials() ) {
           return $this->set_authen_tokens();
        } else {
           return false;
        }

    }

    function check_authen_credentials() {

        // First check for an existing authentication token.
        if (isset($_COOKIE['AMPLoginCredentials']))
            return $this->check_cookie($_COOKIE['AMPLoginCredentials']);

        if (isset($_REQUEST['username']) || isset($_SERVER['PHP_AUTH_USER']))
            return $this->check_password();

        return false;

    }

    function set_authen_tokens () {

        // Sanity Check.
        if (isset( $this->user )) {
            $_SERVER['REMOTE_USER'] = $this->user;
            $_SERVER['REMOTE_GROUP'] = $this->permission;

            return $this->set_cookie();
        }

    }

    function set_cookie () {

        $dbcon = $this->dbcon;

        mt_srand((double)microtime()*1000000);

        $now = time();
        $secret = md5(mt_rand());

        $c_user   = $this->user;
        $c_userid = $this->userid;
        $c_perm   = $this->permission;
        $c_domain = preg_replace( "/([^\.]*)\.([^\.]*)$/", "/.\$1.\$2/", $_SERVER['SERVER_NAME'] );

        // Instead of letting the browser control cookie expiry, we'll control
        // it from the database.
        //
        //        $c_time   = $now + $this->timeout;
        $c_time   = null;

        $hash = $this->make_secure_cookie( $c_user, $c_perm, $secret );
        $old_hash = $this->has_cookie;

        if (setcookie( 'AMPLoginCredentials', "$hash:$c_user:$c_perm:$c_userid", $c_time )) {

            // Quick Hack, to be replaced by a more robust databas versioning
            // system.

            $tables = $dbcon->MetaTables();

            if ( !array_search( 'users_sessions', $tables ) ) {

                $sql = 'CREATE TABLE users_sessions ( id INT AUTO_INCREMENT PRIMARY KEY, hash char(40), INDEX(hash), secret char(32), last_time INT, INDEX(last_time), in_time INT )';
                $dbcon->Execute( $sql ) or
                    die( "Couldn't fixup database structure: " . $dbcon->ErrorMsg() );
            }

            // By this time, we've validated the data, so we don't need to
            // escape it.

            if ($old_hash) {
                $sql = "UPDATE users_sessions SET hash='$hash', secret='$secret', last_time='$now' WHERE hash='$old_hash'";
            } else {
                $sql = "INSERT INTO users_sessions (hash, secret, in_time, last_time) VALUES ('$hash', '$secret', '$now', '$now')";
            }

            $dbcon->Execute( $sql );

            return true;
        }

        return false;

    }

    function make_secure_cookie ( $user, $permission, $secret ) {

        return sha1( $user . $permission . $secret . $_SERVER['HTTP_USER_AGENT'] );

    }

    function check_cookie ( $cookie ) {

        $dbcon = $this->dbcon;

        $cookie = explode( ':', $_COOKIE['AMPLoginCredentials'] );

        $cookie['hash'] = $cookie[0];
        $cookie['user'] = $cookie[1];
        $cookie['permission'] = $cookie[2];
        $cookie['userid'] = $cookie[3];

        if ($cookie['hash']=='logout' || $_REQUEST['logout']=='logout') {
            $this->invalidate_cookie('Successfully logged out.', 'OK');
            $this->do_logout();

            return false;
        }


        $cookie_sql = "SELECT hash, secret, last_time FROM users_sessions WHERE hash=" . $dbcon->qstr( $cookie['hash'] );
        $authdata = $dbcon->GetRow( $cookie_sql );

        if ($authdata) {

            if ($this->cookie_still_valid( $authdata['last_time'] )) {

                $hash = $this->make_secure_cookie( $cookie['user'], $cookie['permission'], $authdata['secret'] );

                if (strcmp($hash, $cookie['hash'] === 0)) {
                    $this->user = $cookie['user'];
                    $this->permission = $cookie['permission'];
                    $this->has_cookie = $hash;
                    return true;
                }
 
            } else {

                $this->invalidate_cookie('Please log in first to continue working', 'OK');
                return false;

           }
        }

        $this->invalidate_cookie('Invalid Credentials.', 'Error', true);
        return false;

    }

    function cookie_still_valid ( $last_seen ) {

        $expire_time = time() - $this->timeout;
        
        if ( $expire_time < $last_seen ) return true;

        return false;

    }

    function invalidate_cookie ( $message, $type = 'Error', $attack = false ) {

        $this->message_type = $type;
        $this->message = $message;

        $c_domain = preg_replace( "/([^\.]*)\.([^\.]*)$/", "/.\$1.\$2/", $_SERVER['SERVER_NAME'] );
        return setcookie( 'AMPLoginCredentials', '*', time() - 86400 );

    }

    function check_password () {

        $dbcon = $this->dbcon;

        if ($_REQUEST['logout']=='logout') $this->do_logout();

        if (isset($_REQUEST['username'])) {
            $username = $_REQUEST['username'];
            $password = $_REQUEST['password'];
        } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        }

        $user_sql = "SELECT id, name, password, permission FROM users WHERE name=" . $dbcon->qstr( $username ) . " ORDER BY permission DESC";
        $authdata = $dbcon->GetRow( $user_sql );

        if ($this->validate_password( $password, $authdata['password'] )) {
            $this->user = $username;
            $this->permission = $authdata['permission'];
            $this->userid = $authdata['id'];
            return true;
        }

        $this->message = "Invalid Password.";
        $this->message_type = 'Error';

        return false;

    }

    function validate_password ( $password, $hash ) {

        $hashtype = substr($hash, 0, strpos($hash, "}"));

        if ( strcmp($hashtype, "{SSHA}") == 0 ) {

            $full_hash = base64_decode(substr($hash, 6));

            $pw_hash   = substr($full_hash, 0, 20);
            $salt      = substr($full_hash, 20 );

            $new_hash  = mhash(MHASH_SHA1, $password . $salt);

            if (strcmp($pw_hash, $new_hash) === 0) return true;

        } else {

            // This is a problem, and should be removed at some point.
            if (strcmp($password, $hash) === 0) return true;
        }

        return false;

    }

    function do_login() {

        if ( isset( $_REQUEST['authtype'] ) && (strtolower($_REQUEST['authtype']) == 'basic') ) {

            header( 'WWW-Authenticate: Basic realm="RESTRICTED ACCESS"');
            header( $_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized' );
            exit;

        } else {

            include( 'AMP/Auth/LoginScreen.inc.php' );
            exit;

        }

    }

    function do_logout() {

        if (isset($_SERVER['PHP_AUTH_USER'])) {

            unset($_SERVER['PHP_AUTH_USER']);
            unset($_SERVER['PHP_AUTH_PW']);

            header( 'WWW-Authenticate: Basic realm="RESTRICTED ACCESS"' );
            header( $_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized' );

        }

    }

}
