<?php

if ( !defined( 'AMP_SYSTEM_CACHE_PATH')) define( 'AMP_SYSTEM_CACHE_PATH', AMP_LOCAL_PATH . DIRECTORY_SEPARATOR . 'cache');
define( 'MAGIC_QUOTES_ACTIVE', get_magic_quotes_gpc());
if ( !defined( 'PHPLIST_CONFIG_ADMIN_ID')) define( 'PHPLIST_CONFIG_ADMIN_ID', 1);
define('ADODB_REPLACE_INSERTED', 2);
define('ADODB_REPLACE_UPDATED',  1);

define( 'AMP_SYSTEM_UNIQUE_ID', AMP_DB_NAME );

?>
