<?php

// security - hide paths
if (!defined('ADODB_DIR')) die();

global $ADODB_INCLUDED_MEMCACHE;
$ADODB_INCLUDED_MEMCACHE = 1;

/* 

  V4.90 8 June 2006  (c) 2000-2006 John Lim (jlim#natsoft.com.my). All rights reserved.
  Released under both BSD license and Lesser GPL library license. 
  Whenever there is any discrepancy between the two licenses, 
  the BSD license will take precedence. See License.txt. 
  Set tabs to 4 for best viewing.
  
  Latest version is available at http://adodb.sourceforge.net
  
*/

	function &getmemcache($key,&$err, $timeout=0, $host, $port)
	{
		$false = false;
		$err = false;
        
        $AMP_key = AMP_CACHE_TOKEN_ADODB . $key;
		if ($cache_value = AMP_cache_get($AMP_key)){
			return $cache_value;
		} else {
            return $false;
        }

        /***
         * ADODB standard library memcache code
         * not used by AMP
         **/
		if (!function_exists('memcache_pconnect')) {
			$err = 'Memcache module PECL extension not found!';
			return $false;
		}

		$memcache = new Memcache;
		if (!@$memcache->pconnect($host, $port)) {
			$err = 'Can\'t connect to memcache server on: '.$host.':'.$port;
			return $false;
		}

		$rs = $memcache->get($key);
		if (!$rs) {
			$err = 'Item with such key doesn\'t exists on the memcached server.';
			return $false;
		}

		$tdiff = intval($rs->timeCreated+$timeout - time());
		if ($tdiff <= 2) {
			switch($tdiff) {
				case 2: 
					if ((rand() & 15) == 0) {
						$err = "Timeout 2";
						return $false;
					}
					break;
				case 1:
					if ((rand() & 3) == 0) {
						$err = "Timeout 1";
						return $false;
					}
					break;
				default: 
					$err = "Timeout 0";
					return $false;
			}
		}
		return $rs;
	}

	function putmemcache($key, $rs, $host, $port, $compress, $debug=false)
	{
		$false = false;
		$true = true;

        $AMP_key = AMP_CACHE_TOKEN_ADODB . $key;
		if ($success = AMP_cache_set($AMP_key, $rs )){
			return $true;
		} else {
            return false;
        }


        /***
         * ADODB standard library memcache code
         * not used by AMP
         **/
		if (!function_exists('memcache_pconnect')) {
			if ($debug) ADOConnection::outp(" Memcache module PECL extension not found!<br>\n");
			return $false;
		}

		$memcache = new Memcache;
		if (!@$memcache->pconnect($host, $port)) {
			if ($debug) ADOConnection::outp(" Can't connect to memcache server on: $host:$port<br>\n");
			return $false;
		}

		$rs->timeCreated = time();
		if (!$memcache->set($key, $rs, $compress, 0)) {
			if ($debug) ADOConnection::outp(" Failed to save data at the memcached server!<br>\n");
			return $false;
		}
		return $true;
	}

	function FlushMemCache($key=false, $host, $port, $debug=false)
	{
        $AMP_key = AMP_CACHE_TOKEN_ADODB . $key;
		if ($key) {
			return AMP_cache_delete($AMP_key);
		} else {
			return AMP_cacheFlush();
		}


        /***
         * ADODB standard library memcache code
         * not used by AMP
         **/
		if (!function_exists('memcache_pconnect')) {
			if ($debug) ADOConnection::outp(" Memcache module PECL extension not found!<br>\n");
			return;
		}

		$memcache = new Memcache;
		if (!@$memcache->pconnect($host, $port)) {
			if ($debug) ADOConnection::outp(" Can't connect to memcache server on: $host:$port<br>\n");
			return;
		}

		if ($key) {
			if (!$memcache->delete($key)) {
				if ($debug) ADOConnection::outp("CacheFlush: $key entery doesn't exist on memcached server!<br>\n");
			} else {
				if ($debug) ADOConnection::outp("CacheFlush: $key entery flushed from memcached server!<br>\n");
			}
		} else {
			if (!$memcache->flush()) {
				if ($debug) ADOConnection::outp("CacheFlush: Failure flushing all enteries from memcached server!<br>\n");
			} else {
				if ($debug) ADOConnection::outp("CacheFlush: All enteries flushed from memcached server!<br>\n");
			}
		}
		return;
	}
?>
