<?php

/*
require_once( 'AMP/Base/DB.php' );
//server config file
if ( file_exists_incpath( 'AMP/HostConfig.inc.php')) {
    include_once( 'AMP/HostConfig.inc.php');
}
require_once( 'AMP/System/Config.inc.php' );

require_once( 'AMP/System/Language/Config.php');
require_once( 'AMP/Base/Debug.php');
require_once( 'AMP/Base/Lookups.php');
require_once( 'AMP/Base/Setup.php');
require_once( 'AMP/System/Permission/Config.inc.php');
*/
$no_legacy = 1;
require_once( 'AMP/Base/Config.php' );
$no_legacy = false;

//require_once( 'AMP/Base/ConfigSystem.php' );

class AMP_Authentication_Handler {

    var $user;
    var $permission;

    var $has_cookie = false;

    var $message_type = 'Error';
    var $message;

    var $dbcon;
    var $_loginType;

    var $userid;
    var $_require_redirect = false;

    function AMP_Authentication_Handler ( $dbcon, $logintype_name = 'admin', $timeout = null ) {
		$this->notice('creating new handler');

        $this->dbcon = $dbcon;

        $loginType = 'AMP_Authentication_LoginType_' . ucfirst( $logintype_name );
        include_once( 'AMP/Auth/LoginType/' . ucfirst( $logintype_name) . '.inc.php' );

        if ( class_exists( $loginType )) {
            $this->_loginType = &new $loginType( $this );
            $this->_loginType->setTimeout( $timeout );
            if( method_exists( $this->_loginType, 'initLoginState')) $this->_loginType->initLoginState( $this );
        }


    }

    function is_authenticated () {
		$this->notice( 'in is_authenticated' );
        if ( !isset( $this->_loginType )) {
			$this->error( 'login type not set' );
			return false;
		}

        if ( $this->_loginType->check_authen_credentials() ) {
			$this->notice('authen creds check out, setting tokens');
            return $this->set_authen_tokens();
        } else {
			$this->error('authen creds did not check out, returning false');
           return false;
        }

    }

	function error($message, $level = E_USER_WARNING) {
		if(defined('AMP_AUTHENTICATION_DEBUG') && AMP_AUTHENTICATION_DEBUG ) {
			trigger_error($message, $level);
		}
		$this->errors[] = $message;
	}

	function notice($message) {
		return $this->error($message, E_USER_NOTICE);
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
        $secret = $this->get_seed();

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

        if (setcookie(  $this->_loginType->getCookieName( ), 
                        $this->build_cookie_value( $c_userid, $c_perm, $secret ),
                        ( time( ) + $this->_loginType->getTimeout( ) ))) {
			$this->notice('handler set cookie');
            $this->save_session( $hash, $secret );
            return true;
        }

		$this->error('could not setcookie, no session saved');
        return false;

    }

    function build_cookie_value( $c_userid, $c_perm, $secret ) {
        $users = AMP_lookup( 'admins');

        $c_user   = $users[ $c_userid ];
        $hash = $this->make_secure_cookie( $c_user, $c_perm, $secret );
        return "$hash:$c_user:$c_perm:$c_userid";
    }

    function get_seed ( ) {
        mt_srand((double)microtime()*1000000);
        return md5(mt_rand());

    }

    function save_session(  $hash, $secret ) {
        // Quick Hack, to be replaced by a more robust database versioning
        // system.
        $now = time();

        $tables = $this->dbcon->MetaTables();

        if ( !array_search( 'users_sessions', $tables ) ) {

            $sql = 'CREATE TABLE users_sessions ( id INT AUTO_INCREMENT PRIMARY KEY, hash char(40), INDEX(hash), secret char(32), last_time INT, INDEX(last_time), in_time INT, userid INT( 11 ) )';
            $this->dbcon->Execute( $sql ) or
                die( "Couldn't fixup database structure: " . $this->dbcon->ErrorMsg() );
        }

        $userid = $this->userid;
        $sql = "INSERT INTO users_sessions (hash, secret, in_time, last_time, userid ) VALUES ('$hash', '$secret', '$now', '$now', '$userid')";

        $this->dbcon->Execute( $sql );
    }

    function make_secure_cookie ( $user, $permission, $secret ) {

        return sha1( $user . $permission . $secret . $_SERVER['HTTP_USER_AGENT'] );

    }

    function set_message( $message, $type = 'Error') {
        $this->message_type = $type;
        $this->message = $message;

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
        $this->userid = $cookie['userid'] = $cookie[3];

        $cookie_sql = "SELECT hash, secret, last_time, userid FROM users_sessions WHERE hash=" . $this->dbcon->qstr( $cookie['hash'] );
        $authdata = $this->dbcon->GetRow( $cookie_sql );

        if ($authdata) {
            if ($this->cookie_still_valid( $authdata['last_time'] )) {

                $hash = $this->make_secure_cookie( $cookie['user'], $cookie['permission'], $authdata['secret'] );
                if ( $this->userid && $authdata['userid'] && $this->userid != $authdata['userid']) return false;

                if (strcmp($hash, $cookie['hash'] === 0)) {
                    $this->user = $cookie['user'];
                    $this->permission = $cookie['permission'];
                    $this->has_cookie = $hash;
                    if ( $this->userid ) {
                        define( 'AMP_USER_PROFILE_ID', $this->userid );
                    }
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

        $this->set_message( $message, $type );

        $c_domain = preg_replace( "/([^\.]*)\.([^\.]*)$/", "/.\$1.\$2/", $_SERVER['SERVER_NAME'] );
        return setcookie( $this->_loginType->getCookieName(), '*', time() - 86400 );

    }

    function check_password () {

        if (isset($_REQUEST['logout']) && $_REQUEST['logout']=='logout') $this->do_logout();
        if ( $username = $this->_loginType->submittedUser() ) {
			$this->notice('getting user info from form');
            $password = $this->_loginType->submittedPassword();
            $this->_require_redirect = true;
        } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
			$this->notice('getting user info from server vars');
            $username = $_SERVER['PHP_AUTH_USER'];
            $password = $_SERVER['PHP_AUTH_PW'];
        }

        if ( $this->_loginType->validateUser( $username, $password )) {
			$this->notice('user has been validated');
            $this->user = $username;
            $this->_loginType->clearAuthFields( );
            return true;
        }
        if ( $message = $this->_loginType->getInvalidMessage( )) {
            $this->set_message( $message, 'Error');
        }

        return false;

    }

    function redirect_page( ){
        if ( !$this->_require_redirect ) return false;
        require_once( 'AMP/System/Page/Urls.inc.php');
        ampredirect( AMP_Url_AddVars( AMP_SYSTEM_URL_REDIRECT, 'url='.$_SERVER['REQUEST_URI'] )) ;
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
		$this->notice('setting handler userid to '.$id);
        define( 'AMP_SYSTEM_USER_ID', $id );
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
