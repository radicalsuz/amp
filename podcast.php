<?php

require_once('Modules/Podcast/Controller.inc.php');

$pod =& new Podcast_Controller();
header('Content-Type: text/xml');
echo $pod->execute();

?>
