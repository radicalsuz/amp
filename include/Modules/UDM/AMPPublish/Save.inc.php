<?php

/* * * * * * *
 *  AMP Publish Plugin
 *  Automatically publishes a record when it is saved
 *  only works from the user side
 *
 *  Author: austin@radicaldesigns.org
 *  5/25/2005
 */

class UserDataPlugin_Save_AMPPublish extends UserDataPlugin {
    
    var $name = 'AutoPublish';
    var $available = true;

    function UserDataPlugin_Save_AMPPublish (&$udm, $plugin_instance=null) {
        $this->init ($udm, $plugin_instance);
    }

    function execute() {
        if ( !$this->udm->uid ) return false;
        if ($this->udm->admin) return false;
        
        $sql = "update userdata set publish=1 where id=".$this->udm->uid;
        $this->dbcon->Execute($sql);
    }
}
