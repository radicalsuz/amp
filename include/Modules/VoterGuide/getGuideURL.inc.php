<?php

require_once('AMP/Content/Page.inc.php');
require_once('Modules/VoterGuide/VoterGuide.php');

$currentPage =& AMPContent_Page::instance();
$guide =& $currentPage->getObject(strtolower('UserDataPlugin_Save_AMPVoterGuide'));
if($guide) {
	print $_SERVER['PHP_SELF'] . $guide->getURL();
} else {
	trigger_error('no guide for getURL');
}

?>
