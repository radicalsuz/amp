<?php

require_once('AMP/System/Copy/Copy.inc.php');
class AMPSystem_IntroText_Copy extends AMPSystem_Copy {

    var $datatable = 'moduletext';

    function AMPSystem_IntroText_Copy (&$dbcon, $modtext_id = null) {
        $this->init($dbcon, $modtext_id);
    }
}



?>
