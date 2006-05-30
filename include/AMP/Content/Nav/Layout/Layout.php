<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Nav_Layout extends AMPSystem_Data_Item {

    var $datatable = "nav_layouts";
    var $name_field = "name";
    var $_locations;

    function AMP_Content_Nav_Layout ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( 'locations' );
    }

    function _afterSave( ) {
        $this->_saveLocations( );
    }

    function getLocations( ){
        return $this->getData( 'locations' );
    }

    function _afterRead( ){
        $this->_readLocations( );
    }

    function _readLocations( ){
        require_once( 'AMP/Content/Nav/Location/Location.php');
        $location = &new AMP_Content_Nav_Location( $this->dbcon );
        $criteria = $location->makeCriteria( array( 'layout_id' => $this->id ));
        $this->_locations = & $location->search( $criteria );

        if ( !$this->_locations ) return false;
        $results = array( );

        foreach( $this->_locations as $location ){
            $results[] = $location->getData( );
        }
        $this->mergeData( array( 'locations'=> $results ));
    }

    function _saveLocations( ){
        if ( !$locations = $this->getLocations( )) return false;
        require_once( 'AMP/Content/Nav/Location/Location.php');
        $allowed_ids = array( );
        foreach( $locations as $location_data ){
            $location_data['layout_id'] = $this->id;

            if ( isset( $location_data['id']) && $location_data['id']){
                $this->_updateLocation( $location_data, $location_data['id'] );
                $allowed_ids[] = $location_data['id'];
                continue;
            }

            $allowed_ids[] = $this->_addLocation( $location_data );
        }
        $this->_readLocations( );
        foreach( $this->_locations as $location ){
            if ( array_search( $location->id, $allowed_ids ) === FALSE ) $location->delete( );
        }
    }

    function _updateLocation( $location_data, $id  ){
        $location = &new AMP_Content_Nav_Location( $this->dbcon, $id );
        if ( (    $location->getNavid( )    == $location_data['navid'] )
             && ( $location->getPosition( ) == $location_data['position'] )) {
            return true; 
        }
        $location->setData( $location_data );
        return $location->save( );
    }

    function _addLocation( $location_data ) {
        $location = &new AMP_Content_Nav_Location( $this->dbcon );
        $location->setData( $location_data );
        $location->save( );
        return $location->id;
    }

}

?>
