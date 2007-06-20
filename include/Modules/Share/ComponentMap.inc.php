<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Share extends AMPSystem_ComponentMap {

    var $paths = array( 
        'source' => 'Modules/Share/Recipient.php',
        'form'   => 'Modules/Share/Form.inc.php',
        'list'   => 'Modules/Share/List.php'
    );

    var $components = array( 
        'source' => 'Share_Recipient',
        'form'   => 'Share_Form',
        'list'   => 'Share_List',
    );

}


?>
