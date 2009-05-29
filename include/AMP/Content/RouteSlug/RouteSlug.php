<?php
require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_RouteSlug extends AMPSystem_Data_Item {
    var $_exact_value_fields = array( 'id', 'owner_id', 'owner_type', 'name');
    var $datatable = 'route_slugs';
    var $name_field = "name";
    var $_class_name = 'AMP_Content_RouteSlug';

    function AMP_Content_RouteSlug( $dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

    function force_valid_slug( ) {
        $start = $this->getData( 'name');
        return $this->mergeData( array( 'name' => $this->find_valid_slug( $this->clean( $start ))));
    }

    function clean( $value ) {
        return strtolower( preg_replace( '/[^-A-z0-9]/', '', preg_replace( '/[\s_]/', '-', $value ) ));
    }

    function find_valid_slug( $start_value ) {
        $matching_slugs = $this->find( array( 'name' => $start_value ));
        if( empty( $matching_slugs )) return $start_value;
        $first_match = current( $matching_slugs );
        if ( ( count( $matching_slugs ) == 1) && $this->id && ( $first_match->id == $this->id ) ) {
            return $start_value; 
        }

        //add a number to the end of the name
        if ( preg_match( '/(^.+-)(\d+)$/', $start_value, $matches )) {
            $assigned = $matches[1] . strval( intval( $matches[2] ) + 1 );
        } else {
            $assigned = $start_value . '-1';
        }
        return $this->find_valid_slug( $assigned );
    }

    function _afterSave(){
        $this->update_routes( );
    }

    function update_routes( ){
        AMP_lookup_clear_cached( 'article_routes');
        AMP_lookup_clear_cached( 'section_routes');
        AMP_lookup_clear_cached( 'dispatch_for');
    }

    function getOwner() {
        $owner_class = ucfirst($this->getData('owner_type'));
        require_once('AMP/Content/'.$owner_class .'.inc.php');
        return new $owner_class( AMP_dbcon(), $this->getData('owner_id') );
    }
}

?>
