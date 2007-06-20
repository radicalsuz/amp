<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Share_Public extends AMPSystem_ComponentMap {

    var $_path_controller = 'Modules/Share/Public/Controller.php';
    var $_component_controller = 'Share_Public_Controller';
    var $_public_page_id_input = AMP_CONTENT_PUBLICPAGE_ID_SHARE;

    var $paths = array( 
        'source' => 'Modules/Share/Recipient.php',
        'form'   => 'Modules/Share/Public/Form.php',
        'form'   => 'Modules/Share/Public/Form.php',
        'fields'   => 'Modules/Share/Public/Fields.xml',
    );

    var $components = array( 
        'source' => 'Share_Recipient',
        'form'   => 'Share_Public_Form',
    );

}

?>
