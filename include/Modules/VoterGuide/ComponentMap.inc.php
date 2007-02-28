<?php

if (!defined( 'AMP_FORM_ID_VOTERGUIDES' )) define( 'AMP_FORM_ID_VOTERGUIDES', 52 );
require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_VoterGuide extends AMPSystem_ComponentMap {

    var $heading = "Voter Guide";
    var $nav_name = "voterguides";

    var $paths = array( 
        'search' => 'Modules/VoterGuide/Search/Form.inc.php',
        'search_fields' => 'Modules/VoterGuide/Search/Fields.xml',
        'fields' => 'Modules/VoterGuide/Fields.xml',
        'list'   => 'Modules/VoterGuide/List.inc.php',
        'form'   => 'Modules/VoterGuide/Form.inc.php',
        'source' => 'Modules/VoterGuide/VoterGuide.php' );

    var $components = array(
        'search' => 'VoterGuideSearch_Form',
        'form' => 'VoterGuide_Form',
        'list' => 'VoterGuide_List',
        'source' => 'VoterGuide' );

    var $_allow_list = AMP_PERMISSION_VOTERGUIDE_ACCESS ;
    var $_allow_edit = AMP_PERMISSION_VOTERGUIDE_ACCESS ;
    var $_allow_save = AMP_PERMISSION_VOTERGUIDE_ACCESS;
    var $_allow_publish = AMP_PERMISSION_VOTERGUIDE_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_VOTERGUIDE_PUBLISH;
    var $_allow_delete = AMP_PERMISSION_VOTERGUIDE_ACCESS;
}
?>
