<?php
 // 
 $jpcacheon=0; 
 // require ("Connections/config.php") ; 
$JPCACHE_VERSION="1.1.1";

	if ($jpcacheon==1) {
	

/*
  jpcache-sql.php v1.1.1 [2001-06-13]
  Copyright  2001 Jean-Pierre Deckers <jpcache@weirdpier.com>

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
*/

/*
 Credits:

    Based upon 
        phpCache        <nathan@0x00.org> (http://www.0x00.org/phpCache)
        gzdoc.php       <catoc@163.net> and <jlim@natsoft.com.my> 
        jr-cache.php    <jr-jrcache@quo.to>

    Inspired by the following threads:
        http://www.aota.net/ubb/Forum15/HTML/000738-1.html
        http://www.aota.net/ubb/Forum15/HTML/000746-1.html
        http://www.aota.net/ubb/Forum15/HTML/000749-1.html

    More info on http://www.weirdpier.com/jpcache/

 */
 
/* 
 CREATE TABLE cachedata (
   CACHEKEY varchar(255) NOT NULL,
   CACHEEXPIRATION int(11) NOT NULL,
   GZDATA blob,
   DATASIZE int(11),
   DATACRC int(11),
   PRIMARY KEY (CACHEKEY)
 ); 
*/
 
/******************************************************************************/
 
    $JPCACHE_TIME=900;      // Default: 900 - number seconds to cache
    $JPCACHE_DEBUG=1;       // Default: 0 - Turn debugging on/off
    $JPCACHE_SINGLE_SITE=1; // Default: 1 - Used on a single domain ?
    $JPCACHE_ON=1;          // Default: 1 - Turn caching on/off
    $JPCACHE_USE_GZIP=1;    // Default: 1 - Whether or not to use GZIP
    $JPCACHE_POST=1;        // Default: 1 - Should POST's be cached
    $JPCACHE_GC=1;          // Default: 1 - Probability of garbage collection (i.e: 1%)
    $JPCACHE_MAXKEY=250;    // Default: 250 - Maximum keylength for key


    $JPCACHE_DB_HOST =  $MM_HOSTNAME ;    // MySQL Host
    $JPCACHE_DB_DATABASE = $MM_DATABASE;   // MySQL Database to use
    $JPCACHE_DB_USERNAME = $MM_USERNAME;   // MySQL Username 
    $JPCACHE_DB_PASSWORD = $MM_PASSWORD;    // MySQL Password
    $JPCACHE_DB_TABLE = "cachedata";    // MySQL table that holds the data
   
/******************************************************************************/

    /* jpcache_db_connect()
     *
     * Makes connection to the database
     */
    function jpcache_db_connect()
    {
        global $JPCACHE_DB_HOST, $JPCACHE_DB_USERNAME, $JPCACHE_DB_PASSWORD, $sql_link;
        $sql_link = @mysql_connect($JPCACHE_DB_HOST, $JPCACHE_DB_USERNAME, $JPCACHE_DB_PASSWORD);
    }
    
    /* jpcache_db_query($query)
     *
     */    
    function jpcache_db_query($query)
    {
        global $JPCACHE_DB_DATABASE, $sql_link;
        // jpcache_debug("Executing SQL-query $query");
        $ret = @mysql_db_query($JPCACHE_DB_DATABASE, $query, $sql_link);
        return $ret;
    }
    
    /* Take a wild guess... */
    function jpcache_debug($s) 
    {
        global $JPCACHE_DEBUG;
        static $debugline;

        if ($JPCACHE_DEBUG) 
        {
            $debugline++;
            header("X-Debug-$debugline: $s");
        }
    }

    /* jpcache_varkey()
     * 
     * Returns the key for the get & post vars
     */
    function jpcache_varkey() 
    {
        global $HTTP_POST_VARS, $HTTP_GET_VARS;
        return md5("POST=" . serialize($HTTP_POST_VARS) . " GET=" . serialize($HTTP_GET_VARS));
    }

    /* jpcache_requestkey()
     *
     * Returns the key for the request
     */
    function jpcache_requestkey()
    {
        global $SCRIPT_URI, $SERVER_NAME, $SCRIPT_NAME, $JPCACHE_SINGLE_SITE;

        if ($JPCACHE_SINGLE_SITE)
        {
            $name=$SCRIPT_NAME;
        } 
        else 
        {
            $name=$SCRIPT_URI;
        }

        if ($name=="") 
        {
            $name="http://$SERVER_NAME/$SCRIPT_NAME";
        }
        return $name;
    }

    /* jpcache_read()
     *
     * Will try to restore the cachedata from the db.
     */
    function jpcache_restore()
    {
        global $JPCACHE_TIME, $JPCACHE_DB_TABLE, $cache_key, $cachedata_gzdata, $cachedata_datasize, $cachedata_datacrc;
        
        $res = jpcache_db_query("select 
                                    GZDATA, DATASIZE, DATACRC 
                                 from 
                                    $JPCACHE_DB_TABLE 
                                 where
                                    CACHEKEY='".addslashes($cache_key)."'
                                 and
                                    (CACHEEXPIRATION>".time(). " or CACHEEXPIRATION=0)"
                               );
                                
        if ($res && mysql_num_rows($res))
        {
            if ($row = mysql_fetch_array($res))
            {
                // restore data from found row
                $cachedata_gzdata   = $row["GZDATA"];
                $cachedata_datasize = $row["DATASIZE"];
                $cachedata_datacrc  = $row["DATACRC"];
                return true;
            }
        }
        return false;
    }

    /* jpcache_write()
     *
     * Will (try to) write out the cachedata to the db
     */
    function jpcache_write($gzcontents, $size, $crc32) 
    {
		global $JPCACHE_TIME, $JPCACHE_ON, $JPCACHE_DB_TABLE, $cache_key;

		if (!$JPCACHE_ON || $JPCACHE_TIME < 0) 
		{
			jpcache_debug("Not caching, disabled!");
			return false;
		}
		
		// XXX: Later on, implement locking mechanism inhere.
		
		// Check if it already exists
		$res = jpcache_db_query("select CACHEEXPIRATION from $JPCACHE_DB_TABLE where CACHEKEY='".addslashes($cache_key)."'");
		if (!$res || mysql_num_rows($res) < 1) 
		{
		    // Key not found, so insert
		    $res = jpcache_db_query("insert into $JPCACHE_DB_TABLE
		                                (CACHEKEY, CACHEEXPIRATION, GZDATA, DATASIZE, DATACRC)
		                             values
		                                ('".addslashes($cache_key)."'
		                                ,".(($JPCACHE_TIME != 0) ? (time() + $JPCACHE_TIME) : 0)."
		                                ,'".addslashes($gzcontents)."'
		                                , $size, $crc32
		                           )");
            // This fails with unique-key violation when another thread has just inserted the same key.
            // Just continue, as the result is (almost) the same.
            return true;
        }
        else
        {
            // Key found, so update
		    $res = jpcache_db_query("update $JPCACHE_DB_TABLE 
		                             set
		                                CACHEEXPIRATION=".(($JPCACHE_TIME != 0) ? (time() + $JPCACHE_TIME) : 0)."
		                               ,GZDATA='".addslashes($gzcontents)."'
		                               ,DATASIZE=$size
		                               ,DATACRC=$crc32
		                             where  
		                                CACHEKEY='".addslashes($cache_key)."'");
            // This might be an update too much, but it shouldn't matter, so continue.
            return true; 
        }
    }

    /* jpcache_check()
     *
     */
    function jpcache_check() 
    {
        global $JPCACHE_ON, $JPCACHE_MAXKEY, $cache_key;
        
        if (!$JPCACHE_ON) 
        {
            jpcache_debug("Caching has been disabled!");
            return false;
        }
        
        $cache_key = jpcache_requestkey() . ":" . jpcache_varkey();
        jpcache_debug("Cache based upon $cache_key");
        
        if (strlen($cache_key) > $JPCACHE_MAXKEY)
        {
            jpcache_debug("Cachekey too long, md5-ing.");
            $cache_key = md5($cache_key);
        }
        
        // Can we read the cached data for this key ?
        if ((jpcache_restore())) 
        {
            jpcache_debug("cachedata of $cache_key found, data restored");    
            return true;
        } 
        else 
        {
            // No cache data (yet) or unable to read
            jpcache_debug("No valid cachedata of $cache_key");
            return false;
        }
    }
    
    /* jpcache_encoding()
     *
     * Are we capable of receiving gzipped data ?
     * Returns the encoding that is accepted. Maybe additional check for Mac ?
     */
    function jpcache_encoding()
    { 
        global $HTTP_ACCEPT_ENCODING;
        if (headers_sent() || connection_aborted())
        { 
            return false; 
        } 
        if (strpos($HTTP_ACCEPT_ENCODING,'x-gzip') !== false)
        {
            return "x-gzip";
        }
        if (strpos($HTTP_ACCEPT_ENCODING,'gzip') !== false)
        {
            return "gzip";
        }
        return false; 
    }

    /* jpcache_init()
     *
     * Checks some global variables and might decide to disable caching
     */
    function jpcache_init()
    {
        global $JPCACHE_TIME, $JPCACHE_ON, $JPCACHE_POST, $HTTP_POST_VARS, $JPCACHE_VERSION, $cachetimeout;

        // Override default JPCACHE_TIME ?
        if (isset($cachetimeout))
        {
            $JPCACHE_TIME=$cachetimeout;
        }

        // Force cache off when POST occured when you don't want it cached
        if (!$JPCACHE_POST && (count($HTTP_POST_VARS) > 0)) 
        {
            $JPCACHE_ON = 0;
            $JPCACHE_TIME = -1;
        }
        
        // A cachetimeout of -1 disables writing, only ETag and content encoding if possible
        if ($JPCACHE_TIME == -1)
        {
            $JPCACHE_ON=0;
        }
        
        // Output header to recognize version
       // header("X-Cache: jpcache-sql v$JPCACHE_VERSION");
    }

    /* jpcache_gc()
     *
     * Handles the garbagecollection call
     */
    function jpcache_gc()
    {
        global $JPCACHE_GC, $JPCACHE_DB_TABLE;
        
        // Should we garbage collect ?
        if ($JPCACHE_GC>0) 
        {
            mt_srand(time(NULL));
            $precision=100000;
            $r=(mt_rand()%$precision)/$precision;
            if ($r<=($JPCACHE_GC/100)) 
            {
                jpcache_debug("GC hit!");
                jpcache_db_query("delete from 
                                    $JPCACHE_DB_TABLE
                                  where
                                    CACHEEXPIRATION<=".time()."
                                  and
                                    CACHEEXPIRATION!=0"
                                );                                
            }
        }
    }

    /* jpcache_start()
     *
     * Sets the handler for callback
     */
    function jpcache_start()
    {
        global $cachedata_gzdata, $cachedata_datasize, $cachedata_datacrc;

        // Initialize cache
        jpcache_init();
        
        // Connect to db
        jpcache_db_connect();
   
        // Check cache
        if (jpcache_check())
        {
            // Cache is valid and restored: flush it!
            print jpcache_flush($cachedata_gzdata, $cachedata_datasize, $cachedata_datacrc);
            exit;
        }
        else
        {
            // if we came here, cache is invalid: go generate page 
            // and wait for jpCacheEnd() which will be called automagicly
            
            // Check garbagecollection
            jpcache_gc();
            // Go generate page and wait for callback
            ob_start("jpcache_end");
            ob_implicit_flush(0);
        }
    }

    /* jpcache_end()
     *
     * This one is called by the callback-funtion of the ob_start. 
     */
    function jpcache_end($contents)
    {
        global $JPCACHE_USE_GZIP;
        jpcache_debug("Callback happened");
        
        $size = strlen($contents);
        $crc32 = crc32($contents);
        
        if ($JPCACHE_USE_GZIP) 
        {
            $gzcontent = gzcompress($contents, 9);
        } 
        else 
        {
            $gzcontent = $contents;
        }
        
        // write the cache with the current data
        jpcache_write($gzcontent, $size, $crc32);
        
        // Return flushed data
        return jpcache_flush($gzcontent, $size, $crc32);
    }

    /* jpcache_flush()
     *
     * Responsible for final flushing everything.
     * Sets ETag-headers and returns "Not modified" when possible
     *
     * When ETag doesn't match (or is invalid), it is tried to send
     * the gzipped data. If that is also not possible, we sadly have to
     * uncompress (assuming $JPCACHE_USE_GZIP is on)
     */
    function jpcache_flush($gzcontents, $size, $crc32)
    {
        global $HTTP_SERVER_VARS, $JPCACHE_USE_GZIP;
        
        // First check if we can send last-modified
        $myETag = "\"jpd-$crc32.$size\"";
       // header("ETag: $myETag");
        $foundETag = stripslashes($HTTP_SERVER_VARS["HTTP_IF_NONE_MATCH"]);
        $ret = NULL;
        
        if (strstr($foundETag, $myETag))
        {
            // Not modified!
            header("HTTP/1.0 304");
        }
        else
        {
            // Are we gzipping ?
            if ($JPCACHE_USE_GZIP) 
            {
                $ENCODING = jpcache_encoding(); 
                if ($ENCODING) 
                { 
                    // compressed output: set header
                    header("Content-Encoding: $ENCODING");
                    $ret =  "\x1f\x8b\x08\x00\x00\x00\x00\x00";
                    $ret .= substr($gzcontents, 0, strlen($gzcontents) - 4);
                    $ret .= pack('V',$crc32);
                    $ret .= pack('V',$size);
                } 
                else 
                {
                    // Darn, we need to uncompress :(
                    $ret = gzuncompress($gzcontents);
                }
            } 
            else 
            {
                // So content isn't gzipped either
                $ret=$gzcontents;
            }
        }
        return $ret;
    }

    jpcache_start();
	
	}

?>