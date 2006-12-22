<?php

require_once( 'AMP/System/Cache/Cache.php');

class AMP_System_Cache_Memcache extends AMP_System_Cache {

    var $_memcache_connection;
    var $_retrieved_items = array( );

    function AMP_System_Cache_Memcache( ){
        $this->__construct( );
    }

    function __construct( ){
        //ensure memcache is set
        $connected = $this->_init_connection( );
        if ( !$connected ) {
            return;
        }
        $this->_unique_site_key = AMP_SYSTEM_UNIQUE_ID;
        $this->_load_index( );

    }

    function _init_connection( ){
        if (!class_exists( 'Memcache' )) return false;
        $memcache_connection = &new Memcache;
        $result = $memcache_connection->connect( AMP_SYSTEM_MEMCACHE_SERVER, AMP_SYSTEM_MEMCACHE_PORT );
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

    function has_connection( ){
        return isset( $this->_memcache_connection );
    }

    /*
    function &instance( ){
        static $cache = false;
        if ( $cache) return $cache;
		
		//creating the cache
		$cache = new AMP_System_Cache_Memcache;
        if ( !$cache->has_connection( )) {
			trigger_error('MEMCACHE FAILED, attempting file cacheing for ' . $_SERVER['REQUEST_URI']);
            $cache = AMP_System_Cache_File::instance();
        } 

        return $cache;
    }
    */

    function log_memcache_failure() {
        $log = '/tmp/amp-memcache-fails';
        $memcache_fail_limit = 500;   // 500 failures in
        $memcache_fail_window = 60*5; // 5 minutes
        //try to create the file if it doesn't exist
        if($fh = file_exists($log) ? fopen($log, 'r+') : fopen($log, 'x+')) {
            if (flock($fh, LOCK_EX|LOCK_NB)) { // do an exclusive lock
                $AMP_MEMCACHE_FAILS = explode("\n",file_get_contents($log));
                $AMP_MEMCACHE_FAILS[] = time();

                if(count($AMP_MEMCACHE_FAILS) > $memcache_fail_limit) {
                    //if there's more than the limit of failures, restart and reset the log
                    $this->_restart_memcached();
                    $AMP_MEMCACHE_FAILS = array();
                } else {
                    //only keep failures that have happened within the window
                    $AMP_MEMCACHE_FAILS = array_filter($AMP_MEMCACHE_FAILS, create_function('$var', 'return $var > time() - '.$memcache_fail_window.';'));
                }

                //write out the log
                ftruncate($fh, 0);
                fwrite($fh, implode("\n",$AMP_MEMCACHE_FAILS));
                flock($fh, LOCK_UN); // release the lock
            } else {
                trigger_error("Couldn't lock the file !");
            }

            fclose($fh);
        } else {
            trigger_error('could not open memcache fail log at /tmp/amp-memcache-fails.php for reading and writing');
        }
    }

    function add( &$item, $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        $result = $this->_memcache_connection->set( $authorized_key, $item, MEMCACHE_COMPRESSED );
        if ( !$result ) {
            trigger_error( 'retrying ADD ' . $authorized_key );
            //try, try again
            $result = $this->_memcache_connection->set( $authorized_key, $item, MEMCACHE_COMPRESSED );

            trigger_error('logging memcache failure');
            $this->log_memcache_failure();
        }

        if ( $result ) {
            $this->_add_index_key( $authorized_key );
            if ( isset( $this->_items_retrieved[ $authorized_key ])) {
                $this->_items_retrieved[ $authorized_key ] = &$item;
            }
        } elseif ( AMP_DISPLAYMODE_DEBUG_CACHE ) {
            trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, get_class( $this ), __FUNCTION__, $key ) );
        }
        
        return $result;

    }

	function refresh( $key ) {
		$authorized_key = $this->authorize($key);
        if ( !$authorized_key ) return false;
        $this->_add_index_key( $authorized_key );
		if (isset($this->_items_retrieved[ $authorized_key ] )) {
			$this->_memcache_connection->set( $authorized_key, $this->_items_retrieved[ $authorized_key ], MEMCACHE_COMPRESSED );
		}
	}

    function contains( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        if ( $authorized_key == $this->_index_key ) return true;
        if ( !isset( $this->_index[$authorized_key] )) return false; 
    
        return $this->_confirm_memcache_retrieve( $authorized_key );

    }

    function _confirm_memcache_retrieve( $authorized_key ){
        if ( isset( $this->_items_retrieved[ $authorized_key ])) return $authorized_key;
        $item = $this->_memcache_connection->get( $authorized_key ) ;

        $this->_items_retrieved[ $authorized_key ] = &$item;
        if ( !$item ) return false; 
        return $authorized_key;
    }

    function &retrieve( $key ){
        $empty_value = false;
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return $empty_value;
        if ( !$this->contains( $authorized_key )) {
            if ( AMP_DISPLAYMODE_DEBUG_CACHE ) {
                trigger_error( sprintf( AMP_TEXT_ERROR_CACHE_REQUEST_FAILED, get_class( $this ), __FUNCTION__, $key ));
            }
            return $empty_value;
        }

        $result = $this->_confirm_memcache_retrieve( $authorized_key );
        if ( !$result ) return $empty_value;

        return $this->_items_retrieved[ $authorized_key ];
    }

    function delete( $key ){
        $authorized_key = $this->authorize( $key );
        if ( !$authorized_key ) return false;

        $result = $this->_memcache_connection->delete( $authorized_key );
        if ( $result ) {
            $this->_remove_index_key( $authorized_key );
            unset( $this->_items_retrieved[ $authorized_key ]);
        }
        return $result;
    }
    
    function clear( $key_token = null ){
        $preserve_keys = array( );
        foreach( $this->_index as $authorized_key => $time_stored ){
            if ( isset( $key_token ) && ( strpos( $authorized_key, $key_token ) === FALSE )) {
                $preserve_keys[] = $authorized_key;
                continue;
            }
            $this->delete( $authorized_key );
        }

        if ( isset( $key_token ) && !empty( $preserve_keys )) {
            $this->_items_retrieved = array_combine_key( $preserve_keys, $this->_items_retrieved );
        } else {
            $this->_items_retrieved = array( );
        }
    }

    function shutdown( ){
        return $this->_memcache_connection->close( );
    }

    function failover( ){
        return 'file';
    }
}

require_once( 'AMP/System/Cache/File.php');
?>
