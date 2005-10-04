<?php

require_once('AMP/Content/Page.inc.php');
require_once('Modules/VoterGuide/VoterGuide.php');

$currentPage =& AMPContent_Page::instance();
$guide =& $currentPage->getObject('UserDataPlugin_Save_AMPVoterGuide');

print $guide->getName();

?>
