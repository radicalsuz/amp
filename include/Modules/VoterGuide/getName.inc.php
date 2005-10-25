<?php

require_once('AMP/Content/Page.inc.php');
require_once('Modules/VoterGuide/VoterGuide.php');

$currentPage =& AMPContent_Page::instance();
$guide =& $currentPage->getObject(strtolower('UserDataPlugin_Save_AMPVoterGuide'));

if(isset($guide) && $guide) {
	print $guide->getName();
}

?>
