<?php

require_once( 'AMP/System/Cache/Cache.php');

class AMP_System_Cache_Memcache extends AMP_System_Cache {

    var $_memcache_connection;

    function AMP_System_Cache_Memcache( ){
        $this->__construct( );
    }

    function __construct( ){
        //ensure memcache is set
        $connected = $this->_init_connection( );
        if ( !$connected ) {
            return;
        }
        $cache_version = $this->cache_version();
        $this->_unique_site_key = AMP_SYSTEM_UNIQUE_ID . "@$cache_version";
    }

    //empty.  just here so as not to call _save_index from the parent.
    function __destroy( ) {
    }

    function _init_connection( ){
        if (!class_exists( 'Memcache' )) return false;
        $memcache_connection = &new Memcache;

        $server_list = explode( ',', AMP_SYSTEM_MEMCACHE_SERVER );
        $primary_server = array_shift( $server_list );
        $result = $memcache_connection->pconnect( $primary_server, AMP_SYSTEM_MEMCACHE_PORT );
        if ( count( $server_list ) ) {
            foreach( $server_list as $additional_server ) {
                $result = ( $memcache_connection->addServer( $additional_server, AMP_SYSTEM_MEMCACHE_PORT ) || $result );
            }
        }

        if ( $result ) {
            $this->set_connection( $memcache_connection );
        } else {
            trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, 'Memcache', 'connect', 'Request: '.$_SERVER['REQUEST_URI'] ) );
            $result = $this->_restart_memcached();

            //try again
            if ( $result ) {
                $this->set_connection( $memcache_connection );
            }
        }
        return $result;
    }
    
    function set_connection( &$connection ) {
        $this->_memcache_connection = &$connection;
    }

    //necessary?
    function has_connection( ){
        return isset( $this->_memcache_connection );
    }

    function add( &$item, $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        $result = $this->_memcache_connection->set( $authorized_key, $item, MEMCACHE_COMPRESSED );
        #timeout? : cache->set( $authorized_key, $item, MEMCACHE_COMPRESSED, AMP_SYSTEM_CACHE_TIMEOUT );
        if ( !$result ) {
            //try, try again
            $result = $this->_memcache_connection->set( $authorized_key, $item, MEMCACHE_COMPRESSED );
            $this->log_memcache_failure();
        }
        if ( !$result && AMP_DISPLAYMODE_DEBUG_CACHE ) {
            trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, get_class( $this ), __FUNCTION__, $key ) );
        }
        return $result;
    }

	  function refresh( $key ) {
        return; //memcache no refreshy
    }
    function age( $key ) {
        return; //memcache no age
    }

    #XXX: unnecessary, delete me
    function contains( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        return $this->_memcache_connection->get( $authorized_key );
    }

    function &retrieve( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        $result = $this->_memcache_connection->get( $authorized_key );
        if ( !$result && AMP_DISPLAYMODE_DEBUG_CACHE ) {
                trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, get_class( $this ), __FUNCTION__, $key ));
        }
        return $result;
    }

    function delete( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        return $this->_memcache_connection->delete( $authorized_key );
    }

    # $key_token is ignored for now - no internal namespaces at this point
    function clear( $key_token = null ){
        $cache_version = $this->_memcache_connection->increment( $this->cache_version_key() );
        $this->_unique_site_key = AMP_SYSTEM_UNIQUE_ID . "@$cache_version";
    }

    function cache_version_key() {
        return AMP_SYSTEM_UNIQUE_ID.'_cache_version';
    }

    function cache_version() {
        $cache_version = $this->_memcache_connection->get( $this->cache_version_key() );
        if($cache_version===false) {
            $cache_version = rand(1, 10000);
            $this->_memcache_connection->set($this->cache_version_key(), $cache_version);
        }
        return $cache_version;
    }
    
    function shutdown( ){
        return $this->_memcache_connection->close( );
    }

    function failover( ){
        return 'file';
    }

    function _restart_memcached() {
        if(!defined('MEMCACHED_RC_SCRIPT')) define('MEMCACHED_RC_SCRIPT', '/usr/local/etc/rc.d/memcached.sh');
        if(!file_exists(MEMCACHED_RC_SCRIPT)) return false;

        $memcache_connection = &new Memcache;

        //just try again
        $result = $memcache_connection->connect( AMP_SYSTEM_MEMCACHE_SERVER, AMP_SYSTEM_MEMCACHE_PORT );
        if ( $result ) {
            $this->set_connection( $memcache_connection );
            return true;
        }

        $lock = fopen("/tmp/restart-memcached.lock", "w");
        flock($lock, LOCK_EX|LOCK_NB, $currently_restarting);
        if ($currently_restarting) {
            //if $currently_restarting, then another process has the lock
            trigger_error(getmypid()." - could not get lock, hopefully someone else is restarting memcached");
            return false;
        } else {
            //we're the first one to try to restart
            trigger_error('connection to memcached failed, restarting');
            $stats = $memcache_connection->getStats();
            $pid = $stats['pid'];
            trigger_error(getmypid()." - acquired lock, restarting memcached with pid $pid");

            //use the rc.d script to force a restart
            $ret = exec(MEMCACHED_RC_SCRIPT.' forcerestart',$message,$code);
            if($code) {
                trigger_error("memcached restart script ".MEMCACHED_RC_SCRIPT." returned error code: $code");
                return false;
            }

            //give it 30 seconds to reconnect
            $start = 0; 
            while(!($result = $memcache_connection->connect( AMP_SYSTEM_MEMCACHE_SERVER, AMP_SYSTEM_MEMCACHE_PORT ))) {
                if(++$start > 30) {
                    break;
                }
                sleep(1);
            }
            if ( $result ) {
                $new_stats = $memcache_connection->getStats();
                $new_pid = $new_stats['pid'];
                trigger_error("memcached restarted successfully, new pid is $new_pid");
            } else {
                trigger_error('could not reestablish connection after 30 seconds');
            }
   
            //give up the lock
            flock($lock, LOCK_UN);
            mail('seth@radicaldesigns.org, austin@radicaldesigns.org', '[AMP] memcached restarted', "stats before restart:\n".print_r($stats, true), 'From: amp@radicaldesigns.org', '-fautomated@radicaldesigns.org');
        }
        return $result;
    }

    function log_memcache_failure() {
        trigger_error('logging memcache failure');
        $log = '/tmp/amp-memcache-fails';
        $memcache_fail_limit = 100;   // 100 failures in
        $memcache_fail_window = 60*5; // 5 minutes
        //try to create the file if it doesn't exist
        if($fh = file_exists($log) ? fopen($log, 'r+') : fopen($log, 'x+')) {
            if (flock($fh, LOCK_EX|LOCK_NB)) { // do an exclusive lock
                $AMP_MEMCACHE_FAILS = explode("\n",file_get_contents($log));
                $AMP_MEMCACHE_FAILS[] = time();

                if(count($AMP_MEMCACHE_FAILS) > $memcache_fail_limit) {
                    //if there's more than the limit of failures, restart and reset the log
                    trigger_error( 'requested memcache restart');
                    $this->_restart_memcached();
                    $AMP_MEMCACHE_FAILS = array();
                } else {
                    //only keep failures that have happened within the window
                    $current_failures = $AMP_MEMCACHE_FAILS;
                    $AMP_MEMCACHE_FAILS = array();
                    foreach( $current_failures as $fail_time ) {
                        if ( $fail_time < ( time( ) - $memcache_fail_window ) ) {
                            continue;
                        }
                        $AMP_MEMCACHE_FAILS[] = $fail_time;
                    }
                    trigger_error( 'returning ' . count( $AMP_MEMCACHE_FAILS ) . ' of ' . count( $current_failures ) . ' recent failures to the log');
                    //$AMP_MEMCACHE_FAILS = array_filter($AMP_MEMCACHE_FAILS, create_function('$var', 'return $var > time() - '.$memcache_fail_window.';'));
                }

                //write out the log
                ftruncate($fh, 0);
                fwrite($fh, implode("\n",$AMP_MEMCACHE_FAILS));
                flock($fh, LOCK_UN); // release the lock
            } else {
                trigger_error("Couldn't lock memcache fail log at " . $log ); 
            }

            fclose($fh);
        } else {
            trigger_error('could not open memcache fail log at '.$log.' for reading and writing');
        }
    }
}

require_once( 'AMP/System/Cache/File.php');
?>
