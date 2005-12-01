<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/System/Tool/Control/Control.php');

class ToolControlSet extends AMPSystem_Data_Set {
    var $datatable = 'module_control';
    var $sort = array( "description");

    function ToolControlSet ( &$dbcon, $modid= null ){
        $this->init( $dbcon );
        if (isset($modid)) $this->setTool( $modid );
    }

    function setTool( $modid ) {
        $this->addCriteria( "modid=" . $modid );
        $this->readData();
    }

    function globalizeSettings() {
        if (!$this->makeReady()) return false;

        while( $data = $this->getData() ) {
            $GLOBALS[ $data['var'] ] = $data['setting'];
        }
        return true;
    }
}

?>
