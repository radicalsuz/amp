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
        if (!isset($lookup->datatable)) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $lookup ), 'datatable' ));
            return false;
        }
        if (!isset($lookup->result_field)) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, get_class( $lookup ), 'result_field' ));
            return false;
        }
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

    function &instance( $type, $instance_var = null, $lookup_baseclass="AMPSystemLookup" ) {
        static $lookup_set = false;
        static $cache = false;
        
        $empty_value = false;
        if ( !$cache ) {
            $cache = AMP_get_cache( );
        }

        /*
        if (!$lookup_set) {
            $lookup_set = AMP_cache_get( AMP_CACHE_TOKEN_LOOKUP . 'Master__' . AMP_SYSTEM_USER_ID ); 
            if ( !$lookup_set ) {
                $lookup_set = array( );
            }
        }
        */
        $req_class = $lookup_baseclass . '_' . ucfirst( $type );
        if ( !class_exists( $req_class ) ){
            trigger_error( sprintf( AMP_TEXT_ERROR_LOOKUP_NOT_FOUND, $req_class) );
            return $empty_value;
        }
        if ( !isset( $instance_var )) {
            //standard lookup
            if (!isset($lookup_set[$type])) {
                /*
                $lookup_cache_key_base = AMP_CACHE_TOKEN_LOOKUP . ( $type );
                $lookup_cache_key = $lookup_cache_key_base;
                if ( defined( 'AMP_SYSTEM_USER_ID')) {
                    $lookup_cache_key = AMP_System_Cache::identify( $lookup_cache_key_base, AMP_SYSTEM_USER_ID );
                }
                */
                $lookup_cache_key = AMPSystem_Lookup::cache_key( $type, $instance_var );
                $cached_lookup = AMP_cache_get( $lookup_cache_key );
                if ( !$cached_lookup ) {
                    $lookup_set[$type] = &new $req_class(); 
                    AMP_cache_set( $lookup_cache_key, $lookup_set[$type]);
                } else {
                    $lookup_set[$type] = $cached_lookup;
                }
            } 
        } else {
            //instanced lookup
            if ( !isset( $lookup_set[$type])) {
                $lookup_set[$type] = array( );
            }
            if ( !isset( $lookup_set[$type][$instance_var])) {
                /*
                $lookup_cache_key = AMP_CACHE_TOKEN_LOOKUP . ( $type );
                if ( defined( 'AMP_SYSTEM_USER_ID')) {
                    $lookup_cache_key = AMP_System_Cache::identify( AMP_CACHE_TOKEN_LOOKUP . ( $type ), AMP_SYSTEM_USER_ID );
                }
                $lookup_cache_key = AMP_System_Cache::identify( $lookup_cache_key . 'K', $instance_var );
                $cached_lookup = AMP_cache_get( $lookup_cache_key );
                */
                $lookup_cache_key = AMPSystem_Lookup::cache_key( $type, $instance_var );
                $cached_lookup = AMP_cache_get( $lookup_cache_key );
                if ( !$cached_lookup ) {
                    $lookup_set[$type][$instance_var] = &new $req_class( $instance_var );
                    AMP_cache_set( $lookup_cache_key, $lookup_set[$type][$instance_var]);
                } else {
                    $lookup_set[$type][$instance_var] = $cached_lookup;
                }
            }
            return $lookup_set[$type][$instance_var]->dataset;
        }
        //AMP_cache_set( AMP_CACHE_TOKEN_LOOKUP . 'Master__' . AMP_SYSTEM_USER_ID, $lookup_set ); 
        return $lookup_set[$type]->dataset;
    }

    function cache_key( $type, $instance_var = null ) {
        $lookup_cache_key = AMP_CACHE_TOKEN_LOOKUP . ( $type );
        if ( defined( 'AMP_SYSTEM_USER_ID')) {
            $lookup_cache_key = AMP_System_Cache::identify( $lookup_cache_key, AMP_SYSTEM_USER_ID );
        }
        if ( !isset( $instance_var ))  return $lookup_cache_key;
        
        //instanced lookup
        return AMP_System_Cache::identify( $lookup_cache_key . 'K', $instance_var );

    }

    function &locate( $lookup_def ){
        $empty_value = false;
        if ( !isset( $lookup_def['module'])) $lookup_def['module'] = 'AMPSystem';
        if ( 'content'  == $lookup_def['module']) $lookup_def['module'] = "AMPContent";
        if ( 'constant' == $lookup_def['module']) $lookup_def['module'] = "AMPConstant";
        $lookup_class = str_replace( " ", "", ucwords( $lookup_def['module'])) . '_Lookup';
        if ( !class_exists( $lookup_class ) && !AMPSystem_Lookup::loadLookups( $lookup_def['module'], $lookup_class )) return $empty_value;

        //set lookup args
        $lookup_args = array( $lookup_def['instance'] );
        if ( isset( $lookup_def['instance_var'])) {
            $lookup_args[] = $lookup_args['instance_var'];
        }
        $result = call_user_func_array( array( $lookup_class, 'instance'), $lookup_args ) ;
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
                $this->dataset[ strtolower( $test_class ) ] = $lookup_name;
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

class AMPSystemLookup_FormsPublic extends AMPSystem_Lookup {
    var $datatable = "userdata_fields";
    var $result_field = "name";
    var $sortby = "name";
    var $criteria = 'publish = 1';

    function AMPSystemLookup_Forms() {
        $this->init( );
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
    var $sortby = 'name';

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

class AMPSystemLookup_Admins extends AMPSystem_Lookup {
    var $datatable = "users";
    var $result_field = "name";
    var $sortby = "name";

    function AMPSystemLookup_Admins() {
        $this->init();
    }

}

class AMPSystemLookup_UsersByGroup extends AMPSystem_Lookup {
    var $datatable = 'users';
    var $result_field = 'name';
    var $sortby = 'name';

    function AMPSystemLookup_UsersByGroup( $group_id = null ) {
        if ( isset( $group_id )) {
            $this->add_criteria_group( $group_id );
        }
        $this->init( );
    }

    function add_criteria_group( $group_id ) {
        $this->criteria = 'permission=' . $group_id;
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
                if ( !$groups ) return false;

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
class AMPSystemLookup_Statenames extends AMPSystem_Lookup {
	var $datatable = 'states';
	var $result_field = 'statename';
    function AMPSystemLookup_Statenames( ){
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

class AMPSystemLookup_PersonalTitles {
    var $name = "Personal Titles";
    var $form_def =  'personalTitles';
    var $dataset = array( 
        "Mr.", "Ms.", "Mrs.", "Miss",
        "Dr.", "Rabbi", "Fr.", "Rev.",
        "Hon.", "Sr.", "Br.", "Msr."
        );

    function AMPSystemLookup_PersonalTitles( ) {
        $this->init( );
    }
    
    function init( ) {
        //do nothing
    }

    function available( ) {
        return true;
    }

}

class AMPConstant_Lookup {

    var $dataset;
    var $prefix_values;
    var $prefix_labels;
    var $name = false;
    var $basetype = 'AMPConstant';

    function __construct( ) {
        $this->init( );
    }

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

    function &instance( $type, $instance_var = null, $lookup_baseclass="AMPConstantLookup" ) {
        static $lookup_set = false;
        if (!$lookup_set) $lookup_set = array();
        $req_class = $lookup_baseclass . '_' . ucfirst($type);
        if ( !class_exists( $req_class ) ) {
            trigger_error( sprintf( AMP_TEXT_ERROR_LOOKUP_NOT_FOUND, $req_class) );
            return false;
        }
        if ( !isset( $instance_var )) {
            //standard lookup
            if (!isset($lookup_set[$type])) $lookup_set[$type] = &new $req_class(); 
        } else {
            //instanced lookup
            if ( !isset( $lookup_set[$type])) {
                $lookup_set[$type] = array( );
            }
            if ( !isset( $lookup_set[$type][$instance_var])) {
                $lookup_set[$type][$instance_var] = &new $req_class( $instance_var );
            }
            return $lookup_set[$type][$instance_var]->dataset;
        }
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

class AMPSystemLookup_NullDatetimes extends AMPConstant_Lookup {
    var $_prefix_values = 'AMP_NULL_DATETIME_VALUE';
    
    function AMPSystemLookup_NullDatetimes( ){
        $this->init( );
    }
}

class AMPSystemLookup_NullDates extends AMPConstant_Lookup {
    var $_prefix_values = 'AMP_NULL_DATE_VALUE';
    
    function AMPSystemLookup_NullDates( ){
        $this->init( );
    }
}

class AMPSystemLookup_BlastOptions extends AMPConstant_Lookup {
    var $dataset = array( 
        'DIA' => 'Democracy In Action',
        'phplist' => 'PHPlist'
    );

    function AMPSystemLookup_BlastOptions( ){
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
        require_once( 'AMP/Content/Article/Filter/Fp.inc.php');
        require_once( 'AMP/Content/Article/Filter/General.inc.php');
        require_once( 'AMP/Content/Article/Filter/News.inc.php');
        require_once( 'AMP/Content/Article/Filter/New.inc.php');
        require_once( 'AMP/Content/Article/Filter/Native.inc.php');
        require_once( 'AMP/Content/Article/Filter/Related.inc.php');

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

/**
 * AMPSystemLookup_Tags 
 * 
 * @uses AMPSystem_Lookup
 * @version 3.6.2
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMPSystemLookup_Tags extends AMPSystem_Lookup {
    var $datatable = 'tags';
    var $result_field = 'name';
    var $sortby = 'name';

    function AMPSystemLookup_Tags( ) {
        $this->init( );
    }
}

/**
 * AMPSystemLookup_TagsSimple 
 *
 * This lookup returns all tags in lowercase, for comparison purposes
 * 
 * @uses AMPSystemLookup
 * @uses AMP_Content_Tag
 * @package 
 * @version 3.6.2
 * @copyright 2006 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMPSystemLookup_TagsSimple extends AMPSystemLookup_Tags {
    var $result_field = 'LOWER( name ) as simple_tag';

    function AMPSystemLookup_TagsSimple( ) {
        $this->init( );
    }
}

class AMPSystemLookup_TagsLive extends AMPSystemLookup_Tags {
    var $criteria = 'publish=1';

    function AMPSystemLookup_TagsLive( ) {
        $this->init( );
    }
}

class AMPSystemLookup_TagImages extends AMPSystem_Lookup {
    var $datatable = 'tags';
    var $result_field = 'image';
    var $sortby = 'image';

    function AMPSystemLookup_TagImages( ) {
        $this->init( );
    }
}

class AMPSystemLookup_TagTotals extends AMPSystem_Lookup {
    var $datatable = 'tags_items';
    var $id_field = 'tag_id';
    var $result_field = 'count( item_id ) as qty';
    var $criteria = '1 GROUP BY tag_id';

    function AMPSystemLookup_TagTotals( ){
        $this->init( );
    }

}

class AMPSystemLookup_TagsByItem extends AMPSystem_Lookup {
    var $datatable = 'tags_items';
    var $result_field = 'tag_id';
    var $id_field = 'tag_id';
    var $_criteria_base = 'item_type= %s AND item_id = %s';

    function __construct( $item_id ) {
        $this->criteria = $this->makeCriteriaItem( $item_id );
        $this->init( );
        $this->_init_tag_names( );
    }

    function _init_tag_names( ) {
        $tag_lookup = AMPSystem_Lookup::instance( 'tags' );
        if ( !$tag_lookup ) return ;
        if ( !$this->dataset ) return;

        $this->dataset = array_combine_key( array_keys( $this->dataset ), $tag_lookup );

        if ( $this->dataset ) {
            natcasesort( $this->dataset );
        }
    }

    function makeCriteriaItem( $item_id ) {
        $dbcon = AMP_Registry::getDbcon( );
        return sprintf( $this->_criteria_base , 
                        $dbcon->qstr( $this->_criteria_item ),
                        $dbcon->qstr( $item_id )
                        );
    }
}

class AMPSystemLookup_TagTotalsArticlesLive extends AMPSystem_Lookup {
    var $datatable = 'tags_items a, articles b';
    var $id_field = 'a.tag_id';
    var $result_field = 'count( a.item_id ) as qty';
    var $criteria = 'a.item_id = b.id AND b.publish=1 AND a.item_type="article" GROUP BY a.tag_id';

    function AMPSystemLookup_TagTotalsArticlesLive( ) {
        $this->init( );
    }

}

class AMPSystemLookup_TagTotalsArticlesBySectionLive extends AMPSystem_Lookup {
    var $datatable = 'tags_items';
    var $id_field = 'tag_id';
    var $result_field = 'count( item_id ) as qty';
    var $_base_criteria = 'item_id in( %s ) and item_type="article" GROUP BY tag_id';

    function AMPSystemLookup_TagTotalsArticlesBySectionLive( $section_id ) {
        $articles = AMP_lookup( 'articles_by_section_live', $section_id );
        if ( !empty( $articles )) {
            $this->setIncludedArticles( array_keys( $articles ));
            $this->init( );
        }
    }

    function setIncludedArticles( $id_set ) {
        $keys = join( ",", $id_set ); 
        $this->criteria = sprintf( $this->_base_criteria, $keys );
    }

}
class AMPSystemLookup_TagTotalsArticlesBySectionLogicLive extends AMPSystemLookup_TagTotalsArticlesBySectionLive {

    function AMPSystemLookup_TagTotalsArticlesBySectionLogicLive( $section_id ) {
        $articles = AMP_lookup( 'articles_by_section_logic_live', $section_id );
        if ( !empty( $articles )) {
            $this->setIncludedArticles( array_keys( $articles ));
            $this->init( );
        }
    }
}

class AMPSystemLookup_TagsByForm extends AMPSystemLookup_TagsByItem {
    var $_criteria_item = AMP_SYSTEM_ITEM_TYPE_FORM;

    function AMPSystemLookup_TagsByForm( $form_id ) {
        $this->__construct( $form_id );
    }

}

class AMPSystemLookup_TagsByArticle extends AMPSystemLookup_TagsByItem {
    var $_criteria_item = AMP_SYSTEM_ITEM_TYPE_ARTICLE;

    function AMPSystemLookup_TagsByArticle( $item_id ) {
        if( empty( $item_id )) return;
        $this->__construct( $item_id );
    }

}

class AMPSystemLookup_ItemsByTag extends AMPSystem_Lookup {
    var $datatable = 'tags_items';
    var $result_field = 'item_id';
    var $id_field = 'item_id';
    var $_criteria_base = 'item_type = %s AND tag_id= %s';

    function __construct( $tag_id ) {
        $this->makeCriteriaTag( $tag_id );
        $this->init( );
        $this->_init_names( );

    }

    function _init_names( ) {
        if ( !$this->dataset ) return;
        if ( !( $this->_criteria_item == 'form')) {
            $lookup_name = AMP_pluralize( $this->_criteria_item );
        } else {
            $lookup_name = 'formNames';
        }
        $lookup_values = AMPSystem_Lookup::instance( $lookup_name );

        if ( !$lookup_values ) return;
        $this->dataset = array_combine_key( array_keys( $this->dataset ), $lookup_values );
    }

    function makeCriteriaTag( $tag_id ) {
        $dbcon = AMP_Registry::getDbcon( );
        $this->criteria = sprintf( $this->_criteria_base, 
                                    $dbcon->qstr( $this->_criteria_item ),
                                    $tag_id
                                    ); 
    }
}

class AMPSystemLookup_FormsByTag extends AMPSystemLookup_ItemsByTag {
    var $_criteria_item = AMP_SYSTEM_ITEM_TYPE_FORM;

    function AMPSystemLookup_FormsByTag( $tag_id ) {
        $this->__construct( $tag_id );
    }

}

class AMPSystemLookup_ArticlesByTag extends AMPSystemLookup_ItemsByTag {
    var $_criteria_item = AMP_SYSTEM_ITEM_TYPE_ARTICLE;

    function AMPSystemLookup_ArticlesByTag( $tag_id ) {
        $this->__construct( $tag_id );
    }
}

class AMPSystemLookup_Downloads extends AMPSystem_Lookup {
    function AMPSystemLookup_Downloads( $ext = null ) {
        $this->__construct( $ext );
    }

    function __construct( $ext = null ) {
        $this->dataset = AMPfile_list( 'downloads', $ext );
    }
}

class AMPSystemLookup_Images extends AMPSystem_Lookup {
    function AMPSystemLookup_Images( ) {
        $this->__construct( );
    }

    function __construct( ) {
        $this->dataset = AMPfile_list( 'img/thumb' );
    }
}

class AMPSystemLookup_Objects extends AMPConstant_Lookup {
    var $prefix_values = 'AMP_PERMISSION_OBJECT_';

    function AMPSystemLookup_Objects( ) {
        $this->init( );
    }

}

class AMPSystemLookup_SectionsByGroup extends AMPSystem_Lookup {
    var $datatable = 'permission_items';
    var $id_field = 'target_id';
    var $_base_criteria = 'target_type="section" AND allow=1'; 
    var $criteria = 'target_type="section" AND allow=1'; 
    var $result_field = 'target_type';

    function AMPSystemLookup_SectionsByGroup( $group_id = null ) {
        if ( isset( $group_id )) {
            $this->add_criteria_group( $group_id );
        }
        $this->init( );
        if ( !$this->dataset ) return;

        $this->dataset = array_combine_key( array_keys( $this->dataset ), AMP_lookup( 'sections'));

    }

    function add_criteria_group( $group_id ) {
        $this->criteria = $this->_base_criteria . ' AND group_id = ' . $group_id; 
    }
}

class AMPSystemLookup_SectionPermissionItemsByGroup extends AMPSystem_Lookup {
    var $datatable = 'permission_items';
    var $result_field = 'target_id';
    var $_base_criteria = 'target_type="section"';
    var $criteria = 'target_type="section"';

    function AMPSystemLookup_SectionPermissionItemsByGroup( $group_id = null ) {
        if ( isset( $group_id )) {
            $this->add_criteria_group( $group_id );
        }
        $this->init( );
    }

    function add_criteria_group( $group_id ) {
        $this->criteria = $this->_base_criteria . ' AND group_id = ' . $group_id; 
    }
}

class AMPSystemLookup_FormsByAction extends AMPSystem_Lookup {
    var $datatable = 'webactions';
    var $result_field = 'modin';
    var $criteria = 'status = 1 and ( isnull( enddate ) or enddate > CURRENT_DATE)';

    function AMPSystemLookup_FormsByAction( ) {
        $this->init( );
    }
}

class AMPSystemLookup_CustomFiles extends AMPSystem_Lookup {

	function AMPSystemLookup_CustomFiles() {
		$this->dataset = AMPfile_list('custom', 'php'); 
	}
}

class AMPSystemLookup_SiteRoots extends AMPSystem_Lookup {
    var $datatable = 'per_group';
    var $result_field = 'root_section_id';

    function AMPSystemLookup_SiteRoots( ) {
        $this->init( );
    }
}

class AMPSystemLookup_UserGroups extends AMPSystem_Lookup {
    var $datatable = 'users';
    var $result_field = 'permission';
    var $criteria='!isnull( permission) and permission !=0';

    function AMPSystemLookup_UserGroups( ) {
        $this->init( );
    }
}

class AMPSystemLookup_UserSiteRoots extends AMPSystem_Lookup {

    function AMPSystemLookup_UserSiteRoots( ) {
        $this->prepare_data( );
    }

    function prepare_data( ) {
        $users = AMP_lookup( 'userGroups') ;
        $roots = AMP_lookup( 'siteRoots');
        foreach( $users as $user_id => $group_id ) {
            $this->dataset[$user_id] = $roots[$group_id];
        }
    }
}

class AMPSystemLookup_Subsites extends AMPSystem_Lookup {
    var $datatable = 'sysvar';
    var $result_field = 'basepath';

    function AMPSystemLookup_Subsites( ) {
        $this->init( );
    }
}

?>
