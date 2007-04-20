<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_System_User_Profile extends AMPSystem_Data_Item {

    var $datatable = 'userdata';
    var $_fields = array( );

    function AMP_System_User_Profile( &$dbcon, $id =null ){
        $this->init( $dbcon, $id );
    }

    function getName( ) {
        $fname = $this->getData( 'First_Name');
        $lname = $this->getData( 'Last_Name');
        if ( $fname && $lname ) {
            return $lname . ', ' . $fname;
        }
        if ( $lname ) {
            return $lname;
        }
        if ( $fname ) {
            return $fname;
        }
        $org_name = $this->getData( 'Company');
        return $org_name;
    }

    function getModin( ) {
        return $this->getData( 'modin');
    }

    function getURL( ) {
        if( !$this->isLive( )) return false; 
        return '/' . AMP_Url_AddVars( AMP_CONTENT_URL_FORM_DISPLAY, array( 'modin=' . $this->getModin( ), 'uid=' . $this->id ));
    }

    function get_url_edit( ) {
        return AMP_Url_AddVars( AMP_SYSTEM_URL_FORM_ENTRY, array( 'modin=' . $this->getModin( ), 'uid=' . $this->id ) );
    }

    function tag( $tag_ids, $tag_names ) {
        $result = AMP_add_tags( $tag_ids, $tag_names, $this->id, AMP_SYSTEM_ITEM_TYPE_FORM );
        return ( $result ? 1 : 0 );
    }

    function _init_fields ( ) {
        if ( !empty( $this->_fields )) {
            return $this->_fields;
        }
        $modin = $this->getModin( );
        if ( !$modin ) return false;
        $sourceDef = get_class( $this ) . $modin;

        //check registry for field defs
        $reg = &AMP_Registry::instance();
        $definedSources = &$reg->getEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS );
        if ( !$definedSources ) {
            $definedSources = AMP_cache_get( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS );
        }
        if ($definedSources && isset($definedSources[ $sourceDef ])) return $definedSources[ $sourceDef ];

        require_once( 'AMP/System/UserData.php');
        $moduleSource = &new AMPSystem_UserData( $this->dbcon, $modin );
        if ( !$moduleSource->hasData( )) return false;

        $md = $moduleSource->getData( );

        $fields = $this->_allowed_keys; 

        $keys = array( 'label', 'public', 'type', 'required', 'values', 'lookup', 'size', 'enabled' );

        foreach ( $fields as $fname ) {

            if (!$fname) continue;

            if ( !( isset( $md[ 'enabled_' . $fname ] ) && $md[ 'enabled_' . $fname ] )) continue;

            $field = array();

            foreach ( $keys as $key ) {
                $field[ $key ] = $md[ $key . "_" . $fname ];
            }
            $field = $this->_register_lookups( $field );
            $this->_fields[ $fname ] = $field;
        }

        //Publish Field Hack
        if ($md['publish']) {
            $publish_field = array('type'=>'checkbox', 'label'=>'<span class=publish_label>PUBLISH</span>', 'required'=>false, 'public'=>false,  'values'=>0, 'size'=>null, 'enabled'=>true);
            $this->_fields['publish']=$publish_field;
        }

        //cache field defs to registry
        $definedSources[ $sourceDef ] = $this->_fields;

        $reg->setEntry( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );
        AMP_cache_set( AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS, $definedSources );
        return $this->_fields;
    }

    function _register_lookups( $field_def ) {

        if ( isset( $field_def['lookup']) && $field_def['lookup'] ){
            $requested_lookup_class = $field_def['lookup'];
        } else {
            unset( $field_def['lookup']);
            return $field_def;
        }
        unset( $field_def['lookup']);
        $lookup_values = AMP_lookup( $requested_lookup_class );
        if ( !$lookup_values ) return $field_def;

        $field_def['values'] = $lookup_values;
        return $field_def;

    }

    function &getImageRef( ) {
        $false = false;
        $field_defs = $this->_init_fields( );
        if ( !$field_defs ) return $false;

        foreach( $field_defs as $fname => $fdef ) {
            if ( isset( $fdef['type']) && $fdef['type'] == 'imagepicker') {
                $image_name = $this->getData( $fname );
                if ( !$image_name ) continue;
                
                require_once( 'AMP/Content/Image.inc.php');
                $result = &new Content_Image( $image_name );
                return $result;
            }
        }
    }

    function getBlurb( ) {
        $modin = $this->getModin( );
        if ( !$modin ) return false;
        $blurb_field = 'AMP_FORM_DATA_BLURB_' . $modin;
        if ( defined( $blurb_field )) {
            return $this->getData( constant( $blurb_field ));
        }
        trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $blurb_field ));
    }

}

?>
