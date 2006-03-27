<?php

$modid = 109;
$mod_id = 74;


require_once( "AMP/Content/Page.inc.php" );
require_once( "Modules/VoterGuide/Controller.inc.php" );

$currentPage = &AMPContent_Page::instance();

$voterguide =& new VoterGuide_Controller($currentPage);
$voterguide->execute();
$intro_id = $voterguide->getIntroID();

require_once( "AMP/BaseTemplate.php" );
require_once( "AMP/BaseModuleIntro.php" );
include("AMP/BaseFooter.php"); 

?>
