<?php

require_once('Modules/Podcast/Podcast.php');

class Podcast_Controller {

	function Podcast_Controller() {
	}

	function execute() {
		require_once('Modules/Podcast/Output/RSS.inc.php');
		$id = $_REQUEST['id'];
		$dbcon =& AMP_Registry::getDbcon(); 
		$podcast =& new Podcast($dbcon, $id);

		$rss =& new Podcast_Output_RSS($podcast);
		return $rss->execute();
	}
}
?>
