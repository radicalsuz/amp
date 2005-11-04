<?php
require_once( 'AMP/System/ComponentMap.inc.php');

define( 'PHPLIST_TABLE_CONFIG', 'phplist_config');
define( 'PHPLIST_TABLE_LIST', 'phplist_list');
define( 'PHPLIST_TABLE_MESSAGE', 'phplist_message');
define( 'PHPLIST_TABLE_LIST_MESSAGE', 'phplist_listmessage');
define( 'PHPLIST_TABLE_LIST_USER', 'phplist_listuser');
define( 'PHPLIST_TABLE_USER', 'phplist_user_user');
define( 'PHPLIST_TABLE_USER_ATTRIBUTE', 'phplist_user_user_attribute');

define( 'AMP_URL_MAILER_ADMIN', '/phplist/admin');
if ( !defined( 'AMP_MAILER_LIMIT')) define( 'AMP_MAILER_LIMIT', 250);

class ComponentMap_Blast extends AMPSystem_ComponentMap {

}
?>
