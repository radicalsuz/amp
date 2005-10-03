<?php

require_once('AMP/Registry.php');
require_once('Modules/VoterGuide/VoterGuide.php');

$guide_id = $_REQUEST['id'];
$guide =& new VoterGuide(AMP_Registry::getDbcon(), $guide_id);

print DIA_ORGANIZATION_SHORT_NAME.'+'.$guide->getShortName().'-'.$guide->getBlocID().
		'@lists.democracyinaction.org';

?>
