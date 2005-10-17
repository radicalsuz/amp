<?php

$modid = 109;
$mod_id = 74;

require_once( "AMP/BaseDB.php" );
require_once( "AMP/UserData/Input.inc.php" );
require_once( "AMP/Content/Page.inc.php" );
require_once( "AMP/Content/Map.inc.php" );
require_once( "Modules/VoterGuide/ComponentMap.inc.php" );
require_once( "Modules/VoterGuide/Lookups.inc.php" );
require_once( "AMP/Form/ElementCopierScript.inc.php" );


require_once( "Modules/VoterGuide/VoterGuide.php" );
require_once( "Modules/VoterGuide/Search/Form.inc.php" );
require_once( "Modules/VoterGuide/SetDisplay.inc.php" );
require_once( "Modules/VoterGuide/Controller.inc.php" );

$currentPage = &AMPContent_Page::instance();

$voterguide =& new VoterGuide_Controller($currentPage);
$voterguide->execute();
$intro_id = $voterguide->getIntroID();

require_once( "AMP/BaseTemplate.php" );
require_once( "AMP/BaseModuleIntro.php" );
include("AMP/BaseFooter.php"); 

?>
