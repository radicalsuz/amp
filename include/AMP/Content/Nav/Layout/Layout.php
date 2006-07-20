<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Nav_Layout extends AMPSystem_Data_Item {

    var $datatable = "nav_layouts";
    var $name_field = "name";
    var $_locations;
    var $_location_search;

    function AMP_Content_Nav_Layout ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
        $this->_addAllowedKey( 'locations' );
        $this->_addAllowedKey( 'layout_anchor' );
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
        $this->_location_search = &$location->getSearchSource( );

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

        if ( isset( $this->_location_search )) {
            $this->_location_search->clearCache( );
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

    function getSectionId( ){
        return $this->getData( 'section_id' );
    }

    function getSectionIdList( ){
        return $this->getData( 'section_id_list' );
    }

    function getClassId( ){
        return $this->getData( 'class_id' );
    }

    function getPublicPageId( ){
        return $this->getData( 'introtext_id' );
    }

    function getLayoutAnchor( ){
        $anchor = $this->getData( 'layout_anchor' );
        if ( !$anchor ) $this->_readLayoutAnchor( );
        return $this->getData( 'layout_anchor' );
    }

    function _readLayoutAnchor( ){
        $result = false;
        if ( $section_id = $this->getSectionId( )){
            $names_lookup = AMPContent_Lookup::instance( 'sections' );
            $result = array(    
                'description' => AMP_TEXT_SECTION ,
                'id'    => $section_id,
                'name'  => $names_lookup[ $section_id ],
                'class'  => 'Section'
                );
    }
        if ( $section_id_list = $this->getSectionIdList( )){
            $names_lookup = AMPContent_Lookup::instance( 'sections' );
            $result = array(    
                'description' => AMP_TEXT_SECTION_LIST ,
                'id'    => $section_id_list,
                'name'  => $names_lookup[ $section_id_list ],
                'class'  => 'Section'
                );
        }
        if ( $class_id = $this->getClassId( )){
            $names_lookup = AMPContent_Lookup::instance( 'classes' );
            $result = array(    
                'description' => AMP_TEXT_CLASS ,
                'id'    => $class_id,
                'name'  => $names_lookup[ $class_id ],
                'class'  => 'ContentClass'
                );
        }
        if ( $publicpage_id = $this->getPublicPageId( ) ) {
            $names_lookup = AMPContent_Lookup::instance( 'introtexts' );
            $result = array(    
                'description' => AMP_TEXT_PUBLIC_PAGE,
                'id'    => $publicpage_id,
                'name'  => $names_lookup[ $publicpage_id ],
                'class'  => 'AMPSystem_IntroText'
                );
        }
        $this->mergeData( array( 'layout_anchor' => $result ));
    }

}

?>
