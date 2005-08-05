<?php
require_once ( 'AMP/Content/Lookups.inc.php' );
require_once ( 'Modules/Schedule/Lookups.inc.php' );

class AMPSystem_LookupFactory {

    var $dbcon;

    function AMPSystem_LookupFactory() {
    }

    function init( &$dbcon ) {
        $this->dbcon = & $dbcon;
    }

    function &instance() {
        static $factory = false;

        if (!$factory) $factory =  new AMPSystem_LookupFactory();
        return $factory;
    }
        
    function readData( &$lookup ) {
        if (!isset($lookup->datatable)) return false;
        if (!isset($lookup->result_field)) return false;
        if ( ! ($data = $this->dbcon->CacheGetAssoc( $this->assembleSQL( $lookup ) ))) {
            if ($this->dbcon->ErrorMsg()) 
                trigger_error( 'Failed to retrieve '.get_class($lookup).': '.$this->dbcon->ErrorMsg() );
            return false;
        }
        return $data;
    }

    function assembleSQL( &$lookup ) {
        $sql = "Select ".$lookup->id_field.", ".$lookup->result_field." from ".$lookup->datatable;
        if ($lookup->criteria) $sql .= " where ". $lookup->criteria;
        if ($lookup->sortby) $sql .= " order by ". $lookup->sortby;

        if (isset($_GET['debug_lookups'])) AMP_DebugSQL( $sql, get_class( $lookup ));
        return $sql;
    }
}


class AMPSystem_Lookup {
    var $datatable;
    var $criteria;
    var $id_field = "id";
    var $result_field;
    var $dataset;
    var $sortby;

    function AMPSystem_Lookup() {
        $this->init();
    }

    function init() {
        $factory = & AMPSystem_LookupFactory::instance();
        $this->dataset = $factory->readData( $this );
    }

    function &instance( $type, $lookup_baseclass="AMPSystemLookup" ) {
        static $lookup_set = false;
        $req_class = $lookup_baseclass . '_' . $type;
        if (!$lookup_set) $lookup_set = array();
        if (!isset($lookup_set[$type])) $lookup_set[$type] = &new $req_class(); 
        return $lookup_set[$type]->dataset;
    }

}

class AMPSystemLookup_Modules extends AMPSystem_Lookup {
    var $datatable = "modules";
    var $result_field = "name";
    var $sortby = "name";

    function AMPSystemLookup_Modules() {
        $permissions = & AMPSystem_PermissionManager::instance();
        $this->criteria = "perid in (" . join(',', $permissions->entireSet()) .") AND publish = 1";
        $this->init();
    }
}

class AMPSystemLookup_ToolsbyForm extends AMPSystem_Lookup {
    var $datatable = "modules";
    var $result_field = "id";
    var $id_field = "userdatamodid";

    function AMPSystemLookup_ToolsbyForm() {
        $permissions = & AMPSystem_PermissionManager::instance();
        $this->criteria = "perid in (" . join(',', $permissions->entireSet()) .") AND publish = 1 AND !isnull(userdatamodid)";
        $this->init();
    }
}

class AMPSystemLookup_FormsbyTool extends AMPSystem_Lookup {
    var $datatable = "modules";
    var $result_field = "userdatamodid";

    function AMPSystemLookup_FormsbyTool() {
        $permissions = & AMPSystem_PermissionManager::instance();
        $this->criteria = "perid in (" . join(',', $permissions->entireSet()) .") AND publish = 1 AND !isnull(userdatamodid)";
        $this->init();
    }
}


class AMPSystemLookup_Forms extends AMPSystem_Lookup {
    var $datatable = "userdata_fields";
    var $result_field = "name";
    var $sortby = "name";

    function AMPSystemLookup_Forms() {
        $formpermission = AMPSystem_Lookup::instance('PermissionsbyForm');
        $modules = & AMPSystem_Lookup::instance( 'FormsbyTool' );

        $un_designated = "id not in(" . join(', ', array_keys($formpermission)) . ")";
        $allowed = "id in(" . join(', ', $modules).")";

        $this->criteria = "( $un_designated OR $allowed )";
        $this->init();
    }
}

class AMPSystemLookup_PermissionsbyForm extends AMPSystem_Lookup {
    var $datatable = "modules";
    var $result_field = "perid";
    var $id_field = "userdatamodid";
    var $criteria = "publish = 1 and !isnull(userdatamodid)";

    function AMPSystemLookup_PermissionsbyForm() {
        $this->init();
    }
}

class AMPSystemLookup_Templates extends AMPSystem_Lookup {
    var $datatable = "template";
    var $result_field = "name";
    var $sortby = "name";

    function AMPSystemLookup_Templates() {
        $this->init();
    }

}

class AMPSystemLookup_UserDataNames extends AMPSystem_Lookup {
    var $datatable = "userdata";
    var $result_field = "Concat( First_Name, ' ', Last_Name ) as name";
    var $sortby = "Last_Name, First_Name";
    var $criteria = "( (!isnull(Last_Name) OR !isnull(First_Name)) AND (First_Name!='' OR Last_Name!='')) ";
    
    function AMPSystemLookup_UserDataNames() {
        $this->init();
    }
}

class AMPSystemLookup_UserDataFormalNames extends AMPSystem_Lookup {
    var $datatable = "userdata";
    var $result_field = "Concat( Last_Name, ', ', First_Name ) as name";
    var $sortby = "Last_Name, First_Name";
    var $criteria = "( (!isnull(Last_Name) OR !isnull(First_Name)) AND (First_Name!='' OR Last_Name!='')) ";
    
    function AMPSystemLookup_UserDataNames() {
        $this->init();
    }
}

class AMPSystemLookup_UserDataEmails extends AMPSystem_Lookup {
    var $datatable = "userdata";
    var $result_field = "Email";
    var $criteria = "( !(isnull(Email) OR (Email=''))) ";

    function AMPSystemLookup_UserDataEmails() {
        $this->init();
    }
}

class AMPSystemLookup_IntroTexts extends AMPSystem_Lookup {
    var $datatable = "moduletext";
    var $result_field = "name";

    function AMPSystemLookup_IntroTexts () {
        $this->init();
    }

}

?>
