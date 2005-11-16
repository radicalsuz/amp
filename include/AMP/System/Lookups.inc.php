<?php
define ('AMP_ERROR_LOOKUP_SQL_FAILED', 'Failed to retrieve %s: %s' );
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
            if ($dbError = $this->dbcon->ErrorMsg()) 
                trigger_error( sprintf( AMP_ERROR_LOOKUP_SQL_FAILED, get_class($lookup), $dbError ) );
            return false;
        }
        return $data;
    }

    function assembleSQL( &$lookup ) {
        $sql = "Select ".$lookup->id_field.", ".$lookup->result_field." from ".$lookup->datatable;
        if ($lookup->criteria) $sql .= " where ". $lookup->criteria;
        if ($lookup->sortby) $sql .= " order by ". $lookup->sortby;

        if (AMP_DISPLAYMODE_DEBUG_LOOKUPS) AMP_DebugSQL( $sql, get_class( $lookup ));
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

    function &locate( $lookup_def ){
        if ( !isset( $lookup_def['module'])) $lookup_def['module'] = 'AMPSystem';
        if ( "content" == $lookup_def['module']) $lookup_def['module'] = "AMPContent";
        if ( "constant" == $lookup_def['module']) $lookup_def['module'] = "AMPConstant";
        $lookup_class = str_replace( " ", "", ucwords( $lookup_def['module'])) . '_Lookup';
        if ( !class_exists( $lookup_class ) && !$this->_loadLookups( $lookup_def['module'], $lookup_class )) return false;
        return call_user_func( array( $lookup_class, 'instance'), $lookup_def['instance'] ) ;
    }
    function _loadLookups( $module, $class ){
        if ( 'form' == $module ) {
            include_once( 'AMP/UserData/Lookups.inc.php');
        } else {
            include_once( 'Modules/'.ucfirst( $module ).'Lookups.inc.php');
        }
        return class_exists( $class );

    }

}

class AMPSystemLookup_Modules extends AMPSystem_Lookup {
    var $datatable = "modules";
    var $result_field = "name";
    var $sortby = "name";

    function AMPSystemLookup_Modules() {
        $permissions = &AMPSystem_PermissionManager::instance();
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
class AMPSystemLookup_ToolLinks extends AMPSystem_Lookup {
    var $datatable = "moduletext";
    var $id_field = "searchtype";
    var $result_field = "name";

    function AMPSystemLookup_ToolLinks() {
        $modules = &AMPSystem_Lookup::instance( 'modules');
        $allowed = array_keys( $modules );
        $this->criteria = "( !isnull( searchtype) and searchtype !='') and modid in (".join( ",", $allowed ) . " )";
        $this->init();
    }
}
class AMPSystemLookup_ToolsByIntroText extends AMPSystem_Lookup {
    var $datatable = "moduletext";
    var $result_field = "modid";

    function AMPSystemLookup_ToolsByIntroText () {
        $this->init();
    }

}

class AMPSystemLookup_EventTypes extends AMPSystem_Lookup {
    var $datatable = "eventtype";
    var $result_field = "name";

    function AMPSystemLookup_EventTypes() {
        $this->init();
    }
}

class AMPSystemLookup_Users extends AMPSystem_Lookup {
    var $datatable = "users";
    var $result_field = "name";
    var $sortby = "name";

    function AMPSystemLookup_Users () {
        $this->init();
    }
}

class AMPSystemLookup_Lists extends AMPSystem_Lookup {
    var $datatable = 'lists';
    var $result_field = 'name';
    var $criteria = 'publish=1';
    var $sortby = 'name';

    function AMPSystemLookup_Lists( ) {
        if ( isset( $GLOBALS['MM_listtable'])) {
            $this->datatable = $GLOBALS['MM_listtable'];
            $this->criteria = 'active=1';
        }
        if ( AMP_DBTABLE_BLAST_LISTS ) {
            $this->datatable = AMP_DBTABLE_BLAST_LISTS;
            $this->criteria = 'active=1';
        }
        if ( AMP_MODULE_BLAST == 'PHPlist') {
            $this->datatable = 'phplist_list';
            $this->criteria = 'active=1';
        }
        if ( AMP_MODULE_BLAST == 'Listserve'){
            $this->criteria = "publish=1 and !isnull( subscribe_address) and subscribe_address != ''";
        }

        $this->init();
        
    }

}

class AMPSystemLookup_ListHosts extends AMPSystem_Lookup {
    var $datatable = 'lists';
    var $result_field = 'subscribe_address';
    var $criteria = "!isnull( subscribe_address) and subscribe_address != ''";

    function AMPSystemLookup_ListHosts( ) {
        $this->init( );
    }
}

class AMPSystemLookup_States extends AMPSystem_Lookup {
	var $datatable = 'states';
	var $result_field = 'state';
}

class AMPSystemLookup_Regions extends AMPSystem_Lookup {
	var $datatable = 'region';
	var $result_field = 'title';
    function AMPSystemLookup_Regions( ){
        $this->init( );
    }
}

class AMPSystemLookup_CellProviders {

    function AMPSystemLookup_CellProviders( ) {
        $this->init(); 
    }

    function init( ) {
        $this->dataset = array( 

            "airmessage" =>"airmessage",
            "alltel" =>"alltel",
            "ameritech/acs" => "ameritech/acs",
            "att wireless" => "att wireless",

            "bell mobility (canada)" => "bell mobility (canada)",
            "cellular one" => "cellular one",
            "cellular south" => "cellular south",
            "cinbell" => "cinbell",
            "cingular" => "cingular",
            "fido (canada)" => "fido (canada)",

            "manitoba telecom" => "manitoba telecom",
            "metrocall pagers" => "metrocall pagers",
            "metropcs" => "metropcs",
            "midwest wireless" => "midwest wireless",
            "nextel" => "nextel",
            "qwest" => "qwest",
            "rcc/unicel" => "rcc/unicel",
            "rogers (canada)" => "rogers (canada)",
            "simple freedom" => "simple freedom",

            "skytell" => "skytell",
            "sprint pcs" => "sprint pcs",
            "suncom" => "suncom",
            "t-mobile" => "t-mobile",
            "telus/clearnet (canada)" => "telus/clearnet (canada)",
            "US cellular" => "US cellular",
            "verizon pagers" => "verizon pagers",
            "verizon wireless" => "verizon wireless",
            "virgin mobile usa" => "virgin mobile usa" );
    }
}


class AMPConstant_Lookup {

    var $dataset;
    var $prefix_values;
    var $prefix_labels;

    function init() {
        if (isset($this->_prefix_values)) {
            $this->dataset = array_flip(filterConstants( $this->_prefix_values ));
        }
        if (isset($this->_prefix_labels)) {
            $this->_swapLabels( filterConstants( $this->_prefix_labels ) );
        }
        ksort( $this->dataset );
    }

    function _swapLabels ( $new_labels ) {
        if (!$new_labels || empty( $new_labels )) return false;
        foreach ($new_labels as $label_key => $label_value ) {
            $applied_key = array_search( $label_key, $this->dataset );
            if ($applied_key === FALSE ) continue;
            $this->dataset[ $applied_key ] = $label_value;
        }
    }

    function &instance( $type, $lookup_baseclass="AMPConstantLookup" ) {
        static $lookup_set = false;
        $req_class = $lookup_baseclass . '_' . ucfirst($type);
        if (!$lookup_set) $lookup_set = new $req_class(); 
        return $lookup_set->dataset;
    }
}

?>
