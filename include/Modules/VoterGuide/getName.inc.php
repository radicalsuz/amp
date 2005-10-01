<?php

require_once('AMP/Registry.php');
require_once('Modules/VoterGuide/VoterGuide.php');

$guide_id = $_REQUEST['guide'];
$guide =& new VoterGuide(AMP_Registry::getDbcon(), $guide_id);

print $guide->getName();

?>
