<?php

require_once( 'AMP/System/Base.php');
require_once( 'AMP/System/Component/Controller.php');

class AMP_System_Permission_ACL_Controller extends AMP_System_Component_Controller {
    function AMP_System_Permission_ACL_Controller( ) {
        $this->__construct( );
    }

    function commit_update( ) {
        //set phpgacl options
        
        $gacl_options = array( 
            'smarty_dir' => 'phpgacl/admin/smarty/libs',
            'smarty_template_dir' => 'phpgacl/admin/templates',
            'smarty_compile_dir'  => AMP_SYSTEM_CACHE_PATH,
            'db_type' 		    => AMP_DB_TYPE,
            'db_host'			=> AMP_DB_HOST,
            'db_user'			=> AMP_DB_USER,
            'db_password'		=> AMP_DB_PASS, 
            'db_name'			=> AMP_DB_NAME, 
            'db_table_prefix'   => 'acl_',
            //'debug' => 1

            );

        if ( !defined ( 'AMP_SYSTEM_PERMISSIONS_LOADING')) define( 'AMP_SYSTEM_PERMISSIONS_LOADING', 1 );
        require_once( 'phpgacl/gacl_api.class.php');

        $gacl = &new gacl_api( $gacl_options );
        
        //$gacl = AMP_acl( true );
        $this->_upgrade_database( 'acl_' );

        $gacl->clear_database( );

        //ACOs
        $aco_objects = array( 
            'view'    => 'View',
            'access'  => 'Access',
            'create'  => 'Create',

            'save'    => 'Save',
            'submit'  => 'Submit',
            'publish' => 'Publish',

            'delete'  => 'Delete'
            );

        $aco_sections = array( 'commands' => 'Commands');
        $aco_complete_set = array( 'commands' => $aco_objects );

        foreach( $aco_sections as $value => $name ) {
            $aco_section_id[ $value ] = $gacl->add_object_section ( $name, $value, 0, 0, 'ACO' );
            foreach( $aco_objects as $aco_value => $aco_name ) {
                $aco_id[$aco_value] = $gacl->add_object( $value, $aco_name, $aco_value, 0, 0, 'ACO');
            }
        }

        // AROs
        $client_root_id = $gacl->add_group( 'clients', 'Clients', 0, 'ARO' );
        $admin_group = $gacl->add_group( 'admins', 'Admins' , $client_root_id, 'ARO' );

        $users = AMP_lookup( 'admins' );
        $aro_sections = array( 'users' => 'Users' );
        foreach( $users as $id => $name ) {
            $aro_objects['user_' . $id ] = $name;
        }
        foreach( $aro_sections as $value => $name ) {
            $gacl->add_object_section( $name, $value, 0, 0, 'ARO');
        }
        foreach( $aro_objects as $value => $name ) {
            $aro_object_ids[$value] = $gacl->add_object( 'users', $name, $value, 0, 0, 'ARO');
            $gacl->add_group_object( $admin_group, 'users', $value, 'ARO');
        }

        //AXOs

        $system_root  = $gacl->add_group( 'system', 'AMP', 0, 'AXO' );
        $site_root    = $gacl->add_group( 'site', AMP_SITE_NAME, $system_root, 'AXO');
        $section_root = $gacl->add_object_section( AMP_SITE_NAME . ' Content', 'sections', 0, 0, 'AXO' );

        $section_order_ref = AMP_lookup( 'sectionMap' );
        require_once( 'AMP/Content/Map/Complete.php');
        $map = AMP_Content_Map_Complete::instance( );
        $map_result = ( $map->selectOptions( ));
        $section_order_ref = $map_result;

        $section_names_source = new AMPContentLookup_Sections( );//AMP_lookup( 'sections' );
        $section_parents_source = new AMPContentLookup_SectionParents( );AMP_lookup( 'sectionParents');
        $section_names = $section_names_source->dataset;
        $section_parents = $section_parents_source->dataset;

		if ($section_order_ref && $section_names) {
			$sections = array_combine_key( array_keys( $section_order_ref ), $section_names );
			$sections = array( AMP_CONTENT_MAP_ROOT_SECTION => AMP_SITE_NAME ) + $sections;
		} else {
			$sections = array( AMP_CONTENT_MAP_ROOT_SECTION => AMP_SITE_NAME );
		}
        $axo_group_ids = array( );

        foreach( $sections as $id => $name ) {
            $parent_group_id = $site_root;
            
            $parent_id_content = isset( $section_parents[$id]) ? $section_parents[$id] : AMP_CONTENT_MAP_ROOT_SECTION;

            if ( isset( $axo_group_ids[$parent_id_content])) {
                $parent_group_id = $axo_group_ids[$parent_id_content];
            }
            
            $parent_group_id = $gacl->add_group( 'section_'. $id, $name, $parent_group_id , 'AXO' );
            $axo_group_ids['section'][$id] = $parent_group_id;
            
            $axo_object_ids[$id] = $gacl->add_object( 'sections', $name, 'section_' . $id, 0, 0, 'AXO');
            $gacl->add_group_object( $parent_group_id, 'sections', 'section_' . $id, 'AXO');
            
        }

        //ACLs
        $group_ids = AMP_lookup( 'permissionGroups');
        foreach( $group_ids as $group_id => $group_name ) {
            $acl_group_id = $gacl->add_group( 'group_'. $group_id, $group_name, $admin_group, 'ARO' );
            $allowed_sections_lookup = & new AMPSystemLookup_SectionsByGroup( $group_id ); //AMP_lookup( 'sectionsByGroup', $group_id );
            $allowed_sections = $allowed_sections_lookup->dataset;
            $affected_users = AMP_lookup( 'usersByGroup', $group_id );
            if ( !$affected_users ) continue;
            foreach( $affected_users as $user_id => $user_name ) {
                $gacl->add_group_object( $acl_group_id, 'users', 'user_'.$user_id, 'ARO' );
            }

            if ( !$allowed_sections ) {
                $allow_group = array( $site_root );
            } else {
                $allow_group = array_combine_key( array_keys( $allowed_sections ), $axo_group_ids['section'] );
            }

            $acl_id = $gacl->add_acl( $aco_complete_set, array( ), array( $acl_group_id ), array( ), $allow_group, true, true  );
        }

        $reg = &AMP_Registry::instance( );
        $reg->setEntry( AMP_REGISTRY_PERMISSION_MANAGER, $gacl );
        $this->message( 'Permissions Update Successful' );
        AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );

        return true;

    }

    function _upgrade_database( $table_prefix ) {
        $dbcon = AMP_Registry::getDbcon( );
        $table_names = $dbcon->MetaTables( );
        $target_table = strtolower( $table_prefix . 'phpgacl' ) ;

        if ( array_search( $target_table, $table_names ) !== FALSE ) {
            return true;
        }
        require_once('adodb/adodb-xmlschema.inc.php');

        $schema = new adoSchema($dbcon);
        $schema->SetPrefix($table_prefix, FALSE);
        $schema->ParseSchema(AMP_BASE_INCLUDE_PATH.'phpgacl/schema.xml');
        $schema->ContinueOnError(TRUE);
        $result = $schema->ExecuteSchema();

        if ( $result == 2 ) {
            $this->message( 'Database upgraded successfully');
        } else {
            $this->error( 'Database upgrade failed');
        }
    }
}

?>
