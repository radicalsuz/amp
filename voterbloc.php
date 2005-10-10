<?php

define('DIA_API_DEBUG', false);
require_once('AMP/BaseDB.php');
require_once('DIA/API.php');
require_once('XML/Unserializer.php');
require_once('Modules/VoterGuide/VoterGuide.php');

$api =& DIA_API::create();

$guide =& new VoterGuide($dbcon, $_REQUEST['id']);

$link_xml = $api->get('supporter_groups', array('where' => 'groups_KEY='.$guide->getBlocID()));

$xmlparser =& new XML_Unserializer();
$status = $xmlparser->unserialize($link_xml);
$links = $xmlparser->getUnserializedData();

if($links['supporter_groups']['count'] > 1) {
	foreach ($links['supporter_groups']['item'] as $item) {
		if($item['supporter_KEY']) {
			$supporters[] = $item['supporter_KEY'];
		}
	}
} else {
	$supporters = array($links['supporter_groups']['item']['supporter_KEY']);
}

$bloc_xml = $api->get('supporter', array('key' => $supporters));

$xmlparser2 =& new XML_Unserializer();
$status = $xmlparser2->unserialize($bloc_xml);
$bloc = $xmlparser2->getUnserializedData();

$csv = "First Name, Last Initial, Zip Code, Has Email\n";

if($bloc['supporter']['count'] == 1) {
	$bloc['supporter']['item'] = array($bloc['supporter']['item']);
}
	foreach($bloc['supporter']['item'] as $supporter) {
		$available[] = array($supporter['First_Name'],
							substr($supporter['Last_Name'], 0, 1),
							$supporter['Zip'],
							($supporter['Email'] && 'NONE' != $supporter['Email'])?'Yes':'No');
	}

	header("Content-type: application/csv");
	header("Content-Disposition: attachment; filename=".$guide->getShortName().".csv");

	print $csv;
	print amp_writecsv($available);

?>
