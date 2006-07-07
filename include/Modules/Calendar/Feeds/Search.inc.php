<?php
require_once('Modules/Calendar/Plugin.inc.php');

class CalendarPlugin_Search_Feeds extends CalendarPlugin {

	function CalendarPlugin_Search_Feeds(&$calendar, $plugin_instance=null) {
		$this->init($calendar, $plugin_instance);
	}

	function execute($options=null) {
		$sql = 'SELECT MAX(last_update) < DATE_SUB(NOW(), INTERVAL 24 HOUR) FROM calendar_feeds';

		$update = (isset($options['update']) && $options['update']) || 
			$this->dbcon->GetOne($sql);

		if($update) {
			require_once('Modules/Calendar/Feeds/CalendarFeeds.php');
			$feeds =& new CalendarFeeds($this->dbcon);
			$feeds->update();
		}

		return true;
	}

}
?>
