<?php

require_once( 'AMP/System/Page/Controller.inc.php');
require_once( 'AMP/UserData/Input.inc.php');
require_once( 'Modules/WebAction/WebAction.php');

class WebAction_Controller extends AMPSystemPage_Controller {
    var $_identifiers = array( 'id' => 'setActionId', 'action' => 'setActionId' );
    var $_action_object_class = "WebAction";

    function WebAction_Controller(){
        $this->init( );
    }

}
?>
