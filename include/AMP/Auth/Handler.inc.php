<?php

class AMP_Authentication_Handler {

    var $user;
    var $permission;

    var $has_cookie = false;

    var $message_type = 'Error';
    var $message;

    var $dbcon;
    var $_loginType;

    var $userid;

    function AMP_Authentication_Handler ( $dbcon, $logintype_name = 'admin', $timeout = null ) {

        $this->dbcon = $dbcon;

        $loginType = 'AMP_Authentication_LoginType_' . ucfirst( $logintype_name );
        include_once( 'AMP/Auth/LoginType/' . ucfirst( $logintype_name) . '.inc.php' );

        if ( class_exists( $loginType )) {
            $this->_loginType = &new $loginType( $this );
            $this->_loginType->setTimeout( $timeout );
        }


    }

    function is_authenticated () {
        if ( !isset( $this->_loginType )) return false;

        if ( $this->_loginType->check_authen_credentials() ) {
           return $this->set_authen_tokens();
        } else {
           return false;
        }

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

        if (setcookie( $this->_loginType->getCookieName( ), "$hash:$c_user:$c_perm:$c_userid" )) {

            // Quick Hack, to be replaced by a more robust database versioning
            // system.

            $tables = $this->dbcon->MetaTables();

            if ( !array_search( 'users_sessions', $tables ) ) {

                $sql = 'CREATE TABLE users_sessions ( id INT AUTO_INCREMENT PRIMARY KEY, hash char(40), INDEX(hash), secret char(32), last_time INT, INDEX(last_time), in_time INT )';
                $this->dbcon->Execute( $sql ) or
                    die( "Couldn't fixup database structure: " . $this->dbcon->ErrorMsg() );
            }

            // By this time, we've validated the data, so we don't need to
            // escape it.

/*
            if ($old_hash) {
                $sql = "UPDATE users_sessions SET hash='$hash', secret='$secret', last_time='$now' WHERE hash='$old_hash'";
            } else { */
                $sql = "INSERT INTO users_sessions (hash, secret, in_time, last_time) VALUES ('$hash', '$secret', '$now', '$now')";
//            }

            $this->dbcon->Execute( $sql );

            return true;
        }

        return false;

    }

    function make_secure_cookie ( $user, $permission, $secret ) {

        return sha1( $user . $permission . $secret . $_SERVER['HTTP_USER_AGENT'] );

    }

    function check_cookie ( $raw_cookie ) {

        $cookie = explode( ':', $raw_cookie );

        $cookie['hash'] = $cookie[0];

        if ($cookie['hash']=='logout' || (isset($_REQUEST['logout']) && $_REQUEST['logout']=='logout')) {
            $this->invalidate_cookie('Successfully logged out.', 'OK');
            $this->do_logout();

            return false;
        }

        $cookie['user'] = $cookie[1];
        $cookie['permission'] = $cookie[2];
        $cookie['userid'] = $cookie[3];

        $cookie_sql = "SELECT hash, secret, last_time FROM users_sessions WHERE hash=" . $this->dbcon->qstr( $cookie['hash'] );
        $authdata = $this->dbcon->GetRow( $cookie_sql );

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

        $expire_time = time() - $this->_loginType->getTimeout( );
        
        if ( $expire_time < $last_seen ) return true;

        return false;

    }

    function invalidate_cookie ( $message, $type = 'Error', $attack = false ) {

        $this->message_type = $type;
        $this->message = $message;

        $c_domain = preg_replace( "/([^\.]*)\.([^\.]*)$/", "/.\$1.\$2/", $_SERVER['SERVER_NAME'] );
        return setcookie( $this->_loginType->getCookieName(), '*', time() - 86400 );

    }

    function check_password () {

        if (isset($_REQUEST['logout']) && $_REQUEST['logout']=='logout') $this->do_logout();
        if ( $username = $this->_loginType->submittedUser() ) {
             $password = $this->_loginType->submittedPassword();
        } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        }
        /*
        if ($this->validate_password( $password, $authdata['password'] )) {
            $this->user = $username;
            $this->permission = $authdata['permission'];
            $this->userid = $authdata['id'];
            return true;
        }
        */
        if ( $this->_loginType->validateUser( $username, $password )) {
            $this->user = $username;
            return true;
        }

        $this->message = "Invalid Password.";
        $this->message_type = 'Error';

        return false;

    }

    function validate_password ( $password, $hash ) {
        if (!$password) return false;

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

            $this->_loginType->showLoginScreen( );
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

    function setPermissionLevel( $level ){
        $this->permission = $level;
    }

    function setUserId( $id ) {
        $this->userid = $id;
    }
    /*
    function hidden_post_vars() {
        if ($post_vars=$this->collect_post_vars()) {
            foreach ($post_vars as $key=>$value) {
                $output='<div id="hidden_post" style="display: none;">';
                if (strpos($value, '\'')===FALSE) {
                    $output .= '<input type="hidden" name="'.$key.'" value="'.$value."\">\n";
                } else {
                    $output .= '<textarea name="'.$key.'">'.$value.'</textarea>';
               
                }
                $output.="</div>";
            }
            return $output;
        }
    }

    function collect_post_vars() {
        if (is_array($_POST)) {
            $post_vars = array();
            foreach ($_POST as $key=>$value) {
                if ($key!=$this->_login_username_field && $key!=$this->_login_password_field ) 
                    $post_vars[$key]=$value;
            }
            return $post_vars;
        }
        return false;
    }
    */
}
?>
