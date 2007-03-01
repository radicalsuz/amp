<?php
/*
 * phpGACL - Generic Access Control List
 * Copyright (C) 2002 Mike Benoit
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 *
 * For questions, help, comments, discussion, etc., please join the
 * phpGACL mailing list. http://sourceforge.net/mail/?group_id=57103
 *
 * You may contact the author of phpGACL by e-mail at:
 * ipso@snappymail.ca
 *
 * The latest version of phpGACL can be obtained from:
 * http://phpgacl.sourceforge.net/
 *
 */

require_once(dirname(__FILE__).'/../gacl.class.php');
require_once(dirname(__FILE__).'/../gacl_api.class.php');
require_once(dirname(__FILE__).'/gacl_admin_api.class.php');

// phpGACL Configuration file.
/* 
if ( !isset($config_file) ) {
#	$config_file = '../gacl.ini.php';
	$config_file = dirname(__FILE__).'/../gacl.ini.php';
}

//Values supplied in $gacl_options array overwrite those in the config file.
if ( file_exists($config_file) ) {
	$config = parse_ini_file($config_file);

	if ( is_array($config) ) {
		if ( isset($gacl_options) ) {
			$gacl_options = array_merge($config, $gacl_options);
		} else {
			$gacl_options = $config;
		}
	}
	unset($config);
}
*/
//require_once( 'AMP/Base/Permission.php');
        $gacl_options = array( 
            'smarty_dir' => 'phpgacl/admin/smarty/libs',
            'smarty_template_dir' => 'phpgacl/admin/templates',
            'smarty_compile_dir'  => AMP_SYSTEM_CACHE_PATH,
            'db_type' 		    => AMP_DB_TYPE,
            'db_host'			=> AMP_DB_HOST,
            'db_user'			=> AMP_DB_USER,
            'db_password'		=> AMP_DB_PASS, 
            'db_name'			=> AMP_DB_NAME, 
            'db_table_prefix'   => 'acl_',
        //    'debug' => 1
            );


$gacl_api = new gacl_admin_api($gacl_options);

$gacl = &$gacl_api;

$db = &$gacl->db;

//Setup the Smarty Class.
require_once($gacl_options['smarty_dir'].'/Smarty.class.php');

$smarty = new Smarty;
$smarty->compile_check = TRUE;
$smarty->template_dir = $gacl_options['smarty_template_dir'];
$smarty->compile_dir = $gacl_options['smarty_compile_dir'];

/*
 * Email address used in setup.php, please do not change.
 */
$author_email = 'ipso@snappymail.ca';

/*
 * Don't need to show notices, some of them are pretty lame and people get overly worried when they see them.
 * Mean while I will try to fix most of these. ;) Please submit patches if you find any I may have missed.
 */
error_reporting (E_ALL ^ E_NOTICE);

?>
