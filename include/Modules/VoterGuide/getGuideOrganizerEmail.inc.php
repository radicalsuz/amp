<?php

require_once('AMP/Content/Page.inc.php');
require_once('Modules/VoterGuide/VoterGuide.php');
require_once('AMP/Registry.php');

$currentPage =& AMPContent_Page::instance();
$guide =& $currentPage->getObject(strtolower('UserDataPlugin_Save_AMPVoterGuide'));

$reg =& AMP_Registry::instance();
$db =& $reg->getDbcon();

$owneremail = $db->Execute('select Email from userdata where id='.$guide->getOwner());


if($owneremail) {
	print $owneremail;
} else {
	print 'the email that you registered with';
	trigger_error('couldnt retrieve email');
}

?>
