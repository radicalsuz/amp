<?php
require_once ( 'AMP/Content/Lookups.inc.php' );
if ( file_exists_incpath( 'custom.lookups.inc.php')) include ( 'custom.lookups.inc.php' );

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
                trigger_error( sprintf( AMP_TEXT_ERROR_LOOKUP_SQL_FAILED, get_class($lookup), $dbError ) );
            return false;
        }
        return $data;
    }

    function assembleSQL( &$lookup ) {
        $distinct = ( $lookup->distinct ) ? " distinct " : "";
        $sql = "Select " . $distinct . $lookup->id_field.", ".$lookup->result_field." from ".$lookup->datatable;
        if ($lookup->criteria) $sql .= " where ". $lookup->criteria;
        if ($lookup->sortby) $sql .= " order by ". $lookup->sortby;

        if (AMP_DISPLAYMODE_DEBUG_LOOKUPS) AMP_DebugSQL( $sql, get_class( $lookup ));
        return $sql;
    }

    function clearCache( &$lookup ){
        if (!isset($lookup->datatable)) return false;
        if (!isset($lookup->result_field)) return false;
        return $this->dbcon->CacheFlush( $this->assembleSQL( $lookup ) ); 
    }

    function available ( ){
        return false;
    }
}


class AMPSystem_Lookup {

    var $datatable;
    var $criteria;
    var $id_field = "id";
    var $result_field;
    var $dataset;
    var $sortby;
    var $distinct;
    var $name = false;
    var $basetype = 'AMPSystem';

    function AMPSystem_Lookup() {
        $this->init();
    }

    function init() {
        $factory = & AMPSystem_LookupFactory::instance();
        $this->dataset = $factory->readData( $this );
    }

    function &instance( $type, $lookup_baseclass="AMPSystemLookup" ) {
        static $lookup_set = false;
        $empty_value = false;
        if (!$lookup_set) $lookup_set = array();
        $req_class = $lookup_baseclass . '_' . $type;
        if ( !class_exists( $req_class ) ){
            trigger_error( sprintf( AMP_TEXT_ERROR_LOOKUP_NOT_FOUND, $req_class) );
            return $empty_value;
        }
        if (!isset($lookup_set[$type])) $lookup_set[$type] = &new $req_class(); 
        return $lookup_set[$type]->dataset;
    }

    function &locate( $lookup_def ){
        $empty_value = false;
        if ( !isset( $lookup_def['module'])) $lookup_def['module'] = 'AMPSystem';
        if ( 'content'  == $lookup_def['module']) $lookup_def['module'] = "AMPContent";
        if ( 'constant' == $lookup_def['module']) $lookup_def['module'] = "AMPConstant";
        $lookup_class = str_replace( " ", "", ucwords( $lookup_def['module'])) . '_Lookup';
        if ( !class_exists( $lookup_class ) && !AMPSystem_Lookup::loadLookups( $lookup_def['module'], $lookup_class )) return $empty_value;
        $result = call_user_func( array( $lookup_class, 'instance'), $lookup_def['instance'] ) ;
        return $result;
    }

    function loadLookups( $module, $class ){
        if ( 'form' == $module ) {
            include_once( 'AMP/UserData/Lookups.inc.php');
        } else {
            include_once( 'Modules' . DIRECTORY_SEPARATOR . ucfirst( $module ) . DIRECTORY_SEPARATOR . 'Lookups.inc.php');
        }
        return class_exists( $class );

    }
    function available( ){
        return false;
    }

    function clearCache( ){
        $factory = & AMPSystem_LookupFactory::instance();
        $factory->clearCache( $this );
        
    }

}

class AMPSystemLookup_Lookups {
    var $_include_modules = array( 'schedule', 'form', 'calendar', 'voterGuide' );
    var $dataset;

    function AMPSystemLookup_Lookups( ){
        $this->init( );
    }

    function init() {
        foreach( $this->_include_modules as $module ){
            AMPSystem_Lookup::loadLookups( $module, 'test');
        }
        require_once( 'AMP/Content/Lookups.inc.php');
        $this->get_defined_lookups( );
    }

    function get_defined_lookups( ){
        $class_set = get_declared_classes( );
        foreach( $class_set as $test_class ){

            if ( !strpos( strtolower( $test_class ), 'lookup')) continue;
            if ( AMP_getClassAncestors( $test_class, 'AMPSystem_Lookup')
                 || AMP_getClassAncestors( $test_class, 'AMPConstant_Lookup')) {

                $class_vars = get_class_vars( $test_class );
                $class_available = ( isset( $class_vars['available']) && $class_vars['available']); 

                if ( !isset( $class_vars['available'])) {
                    if ( !call_user_func( array( $test_class, 'available' ))) continue;
                } elseif ( !$class_available ) {
                    continue;
                }

                $lookup_name = $this->get_lookup_name( $test_class );
                if ( strtolower( $lookup_name ) == 'lookup' ) continue;
                $this->dataset[ $test_class ] = $lookup_name;
            }
        }
        asort( $this->dataset );
    }

    function get_lookup_name( $class_name ){
        $test_lookup = new $class_name( );
        if ( $test_lookup->name ) return $test_lookup->name;
        $space = strpos( $class_name, '_' ) + 1;
        if ( !$space ) return $class_name;
        return substr( $class_name, $space );
    }

    function available ( ){
        return false;
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

class AMPSystemLookup_PermissionGroups extends AMPSystem_Lookup {
    var $datatable = 'per_group';
    var $result_field = "name";
    var $sortby ='name';

    function AMPSystemLookup_PermissionGroups( ){
        $this->init( );
    }
}

class AMPSystemLookup_Tools extends AMPSystem_Lookup {
    function AMPSystemLookup_Tools( ){
        $this->dataset = &AMPSystem_Lookup::instance( 'modules' );
    }
}

class AMPSystemLookup_ToolsbyForm extends AMPSystem_Lookup {
    var $datatable = "modules";
    var $result_field = "id";
    var $id_field = "userdatamodid";

    function AMPSystemLookup_ToolsbyForm() {
        require_once( 'AMP/System/Permission/Manager.inc.php');
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
    var $sortby = "name";

    function AMPSystemLookup_IntroTexts () {
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
        if ( defined('AMP_DBTABLE_BLAST_LISTS') && AMP_DBTABLE_BLAST_LISTS ) {
            $this->datatable = AMP_DBTABLE_BLAST_LISTS;
            $this->criteria = 'active=1';
        }
		if ( defined('AMP_MODULE_BLAST') ) {
			if ( AMP_MODULE_BLAST == 'PHPlist') {
				$this->datatable = 'phplist_list';
				$this->criteria = 'active=1';
			}
			if ( AMP_MODULE_BLAST == 'Listserve') {
				$this->criteria = "publish=1 and !isnull( subscribe_address) and subscribe_address != ''";
			}
			if ( AMP_MODULE_BLAST == 'DIA') {
				require_once('DIA/API.php');
				$api =& DIA_API::create();
				$groups = $api->getGroupNamesAssoc();
				$this->dataset = isset($groups)?$groups:array();

				//update our local copy of available DIA lists
				$dbcon =& AMP_Registry::getDbcon();
				foreach($groups as $id => $name) {
					$list = array('id' => $id, 'name' => $name, 'description' => 'DIA Group', 'service_type' => 'DIA');
					$dbcon->Replace('lists', $list, array('id', 'service_type'), $quote=true); 
				}
				return;
			}
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
    function AMPSystemLookup_States( ){
        $this->init( );
    }
}

class AMPSystemLookup_Regions extends AMPSystem_Lookup {
	var $datatable = 'region';
	var $result_field = 'title';
    var $sortby = 'title';

    function AMPSystemLookup_Regions( ){
        $this->init( );
    }
}

class Region_Lookup extends AMPSystem_Lookup {
    var $_parent_region = false;
    var $_region;

    function init( ){
        require_once( 'AMP/Region.inc.php' );
        $this->_region = &Region::instance( );
        $this->_init_dataset( );
    }

    function _init_dataset( ){
        if ( $this->_parent_region ){
            $this->dataset = $this->_region->getSubRegions( $this->_parent_region );
            return;
        }
        // default
        $this->dataset = $this->_region->getTLRegions( );
    }

    function available( ){
        return true;
    }
}

class AMPSystemLookup_Regions_US extends Region_Lookup {
    var $_parent_region = 'US';
    var $name = 'Region: US States';
    var $form_def =  'regions_US';

    function AMPSystemLookup_Regions_US( ){
        $this->init( );
    }
}

class AMPSystemLookup_Regions_Canada extends Region_Lookup {
    var $_parent_region = 'CDN';
    var $name = 'Region: Canadian Provinces';
    var $form_def =  'regions_Canada';

    function AMPSystemLookup_Regions_Canada( ){
        $this->init( );
    }
}

class AMPSystemLookup_Regions_World extends Region_Lookup {
    var $_parent_region = 'WORLD';
    var $name = 'Region: All Countries';
    var $form_def =  'regions_World';

    function AMPSystemLookup_Regions_World( ){
        $this->init( );
    }
}

class AMPSystemLookup_Regions_World_Long extends Region_Lookup {
    var $_parent_region = 'WORLD-LONG';
    var $name = 'Region: All Countries ( full names )';
    var $form_def =  'regions_World_Long';

    function AMPSystemLookup_Regions_World_Long( ){
        $this->init( );
    }
}

class AMPSystemLookup_Regions_US_and_Canada extends Region_Lookup {
    var $_parent_region = 'US AND CANADA';
    var $name = "Region: US States and Canadian Provinces";
    var $form_def =  'regions_US_and_Canada';

    function AMPSystemLookup_Regions_US_and_Canada( ){
        $this->init( );
    }
}

class AMPSystemLookup_CellProviderNames {
    var $name = "Cellphone Providers";
    var $form_def =  'cellProviderNames';

    function AMPSystemLookup_CellProviderNames( ) {
        $this->init(); 
    }

    function init( ) {
        $this->dataset = array( 
        	"att"       => "AT&amp;T",
        	"alltel"    => "AllTel",
        	"cellular one" => "Cellular One",
        	"cingular"  => "Cingular",
        	"dobson" => "Dobson Cellular",
        	"metropcs" => "MetroPCS",
        	"nextel" => "Nextel",
        	"pacbell" => "PacBell",
        	"sprint" => "Sprint",
        	"tmobile" => "T-Mobile",
        	"us cellular" => "US Cellular",
        	"verizon" => "Verizon",
        	"virgin" => "Virgin Mobile",
        	"voicestream" => "VoiceStream",
        	"western" => "Western Wireless" 
            );
    }


    function available ( ){
        return true;
    }
}

class AMPSystemLookup_CellProviderDomains {

    function AMPSystemLookup_CellProviderDomains( ) {
        $this->init(); 
    }

    function init( ) {
        $this->dataset = array( 
	       "at&t" => "mobile.att.net",
        	"att" => "mobile.att.net",
        	"alltel" => "message.alltel.com",
        	"cellular one" => "cellularone.txtmsg.com",
        	"cingular" => "mobile.mycingular.com",
        	"dobson cellular" => "mobile.dobson.net",
        	"dobson" => "mobile.dobson.net",
        	"metro pcs" => "metropcs.sms.us",
        	"metropcs" => "metropcs.sms.us",
        	"nextel" => "page.nextel.com",
        	"pacbell" => "pacbellpcs.net",
        	"pac bell" => "pacbellpcs.net",
        	"sprint" => "messaging.sprintpcs.com",
        	"tmobile" => "tmomail.net",
        	"us cellular" => "uscc.textmsg.com",
        	"verizon" => "vtext.com",
        	"virgin" => "vmobl.com",
        	"virgin mobile" => "vmobl.com",
        	"voicestream" => "voicestream.net",
        	"western" => "cellularonewest.com" );
        }


    function available ( ){
        return false;
    }
}

class AMPConstant_Lookup {

    var $dataset;
    var $prefix_values;
    var $prefix_labels;
    var $name = false;
    var $basetype = 'AMPConstant';

    function init() {
        if (isset($this->_prefix_values)) {
            $this->dataset = array_flip(filterConstants( $this->_prefix_values ));
        }
        if (isset($this->_prefix_labels)) {
            $this->_swapLabels( filterConstants( $this->_prefix_labels ) );
        }
        $this->_sort_default( );
    }

    function _sort_default( ){
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
        if (!$lookup_set) $lookup_set = array();
        $req_class = $lookup_baseclass . '_' . ucfirst($type);
        if ( !class_exists( $req_class ) ) {
            trigger_error( sprintf( AMP_TEXT_ERROR_LOOKUP_NOT_FOUND, $req_class) );
            return false;
        }
        if (!isset($lookup_set[$type])) $lookup_set[$type] = &new $req_class(); 
        return $lookup_set[$type]->dataset;
    }

    function available( ){
        return false;
    }
}

class AMPSystemLookup_Permissions extends AMPSystem_Lookup {
    var $datatable = "per_description";
    var $sortby = 'name';
    var $result_field = 'concat( name, " ( ", id, " )") as pername';

    function AMPSystemLookup_Permissions( ){
        $perManager = &AMPSystem_PermissionManager::instance( );
        $this->criteria = 
            join( " AND ",
                    array( 'publish=1',
                            'id in ('
                                .  join( ',', $perManager->entireSet( )) 
                                .  ' )')
                    );
        $this->init( );
    }
}

class AMPSystemLookup_PermissionNames extends AMPSystem_Lookup {
    var $datatable = 'per_description';
    var $result_field = 'name';
    var $sortby = 'name';
    var $criteria = 'publish=1';

    function AMPSystemLookup_PermissionNames( ){
        $this->init( );
    }
}

class AMPSystemLookup_PermissionLevel extends AMPSystem_Lookup {
    var $datatable = 'permission';
    var $result_field = 'perid';

    function AMPSystemLookup_PermissionLevel( $level = null ){
        if ( isset( $level )) $this->_addCriteriaLevel( $level );
        $this->init( );
    }
    function _addCriteriaLevel( $level ){
        $this->criteria = 'groupid='.$level;
    }
    function &instance( $group_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPSystemLookup_PermissionLevel( $group_id );
        } else {
            $lookup->_addCriteriaLevel( $group_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
    function available( ){
        return false;
    }

}

class AMPConstantLookup_NullDatetimes extends AMPConstant_Lookup {
    var $_prefix_values = 'AMP_NULL_DATETIME_VALUE';
    
    function AMPConstantLookup_NullDatetimes( ){
        $this->init( );
    }
}

class AMPConstantLookup_NullDates extends AMPConstant_Lookup {
    var $_prefix_values = 'AMP_NULL_DATE_VALUE';
    
    function AMPConstantLookup_NullDates( ){
        $this->init( );
    }
}

class AMPConstantLookup_BlastOptions extends AMPConstant_Lookup {
    var $dataset = array( 
        'DIA' => 'Democracy In Action',
        'phplist' => 'PHPlist'
    );

    function AMPConstantLookup_BlastOptions( ){
        //interface
    }
}

class AMPSystemLookup_DeclaredClasses {
    var $dataset;
    var $available = false; 

    function AMPSystemLookup_DeclaredClasses( $prefix = null ){
        $this->_init_classes( $prefix );
    }

    function _init_classes( $prefix = null ) {
        $declared_classes = get_declared_classes( );
        if ( !isset( $prefix )){
            $this->dataset = $declared_classes;
            return;
        }
        $results = array( );

        foreach( $declared_classes as $classname ){
            if ( strpos( strtolower( $classname ), strtolower( $prefix ) ) === 0 ) {
                $results[ $classname ] = substr( $classname, strlen( $prefix )) ;
            }
        }
        $this->dataset = $results;
    }

    function instance( $prefix ) {
        static $class_lookups = array( );
        if ( !isset( $class_lookups[ $prefix ])) {
            $class_lookups[ $prefix ] = new AMPSystemLookup_DeclaredClasses( $prefix );
        }
        return $class_lookups[$prefix]->dataset ;
    }

}

class AMPSystemLookup_Filters extends AMPConstant_Lookup {
    var $dataset;

    function AMPSystemLookup_Filters( ){
        $dataset = AMPSystemLookup_DeclaredClasses::instance( 'ContentFilter_');
        if ( !$dataset ) {
            $this->dataset = false;
            return;
        }

        foreach ( $dataset as $classname => $short_name ) {
            $this->dataset[ strtolower( $short_name )] = $short_name;
        }
    }
}
?>
