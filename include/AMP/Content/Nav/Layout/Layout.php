<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Nav_Layout extends AMPSystem_Data_Item {

    var $datatable = "nav_layouts";
    var $name_field = "name";
    var $_locations;
    var $_location_search;
    var $_exact_value_fields = array( 'section_id_list', 'section_id', 'class_id', 'introtext_id');
    var $locator_fields = array( 'section_id_list', 'section_id', 'class_id', 'introtext_id');
    var $_class_name = 'AMP_Content_Nav_Layout';

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

    function _readLocations( ) {
        require_once( 'AMP/Content/Nav/Location/Location.php');
        $location = &new AMP_Content_Nav_Location( $this->dbcon );
        $criteria = $location->makeCriteria( array( 'layout_id' => $this->id ));
        $this->_locations = $location->search( $criteria );

        if ( !$this->_locations ) return false;
        $results = array( );
        $this->_location_search = &$location->getSearchSource( );

        foreach( $this->_locations as $location ){
            $results[] = $location->getData( );
        }

        $this->mergeData( array( 'locations'=> $results ));
    }


    function _saveLocations( ) {
        if ( !$locations = $this->getLocations( )) return false;
        require_once( 'AMP/Content/Nav/Location/Location.php');
        $allowed_ids = array( );
        foreach( $locations as $location_data ){
            $location_data['layout_id'] = $this->id;
            $location_data['position'] = str_replace(  "'", "", $location_data['position']);

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
        $location->setDefaults( );
        $location->mergeData( $location_data );
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

    function get_url_edit( ) {
        return AMP_url_add_vars( AMP_SYSTEM_URL_NAV_LAYOUT, array( 'id=' . $this->id ));
    }

    function loadLocations( ) {
        if ( $this->getLocations( )) return;
        $this->_readLocations( );
    }

    function copy( $section_id_list = false, $section_id = false, $class_id = false, $introtext_id = false ) {
        $this->loadLocations( );
        if ( !( $locations = $this->getLocations( ))) return false;

        $clean_keys = array( 'navid', 'position' );
        foreach( $locations as $location_def ) {
            $clean_locations[] = array_combine_key( $clean_keys, $location_def );
        }

        $result = false;
        $totals = 0;
        $failures = array( );
        $this->dbcon->CacheFlush( );

        foreach( $this->locator_fields as $criteria_name ) {
            if ( !( isset( $$criteria_name ) && $$criteria_name )) continue;
            $criteria_value = $$criteria_name;
            unset( $target_layout );

            //search for an existing layout
            $target_layout = $this->find( array( $criteria_name => $criteria_value ));

            if ( !$target_layout ) {
                //create a new layout, move on
                $target_layout = $this->create( array( $criteria_name => $criteria_value ), $clean_locations );
                $result = $target_layout->save( );
                continue;
            } 

            //edit an existing layout
            if ( is_array( $target_layout )) {
                $target_layout = current( $target_layout );
            }

            $target_layout->loadLocations( );
            if ( !( $target_locations = $target_layout->getLocations( ) )) {
                //add these locations, move on
                $target_layout->mergeData( array( 'locations' => $clean_locations ));
                $result = $target_layout->save( );
                continue;
            }

            $new_locations = array_merge( $target_locations, $clean_locations );

            $target_layout->mergeData( array( 'locations' => $new_locations ));
            $result = $target_layout->save( );
            if ( !$result ) {
                $failures[ $target_layout->getName( ) ] = 1;
            }
        }

        ampredirect( AMP_SYSTEM_URL_NAV_LAYOUT );

        return $result;
    }


    /**
     * create 
     * 
     * @param array $anchor = array containing one element of section_id/section_id_list/class_id/introtext_id 
     * @param array $positions 
     * @access public
     * @return AMP_Content_Nav_Layout 
     */

    function &create( $anchor, $locations = array( )) {
        $new_layout = new AMP_Content_Nav_Layout( AMP_Registry::getDbcon( ) );
        $new_layout->setDefaults( );
        $new_layout->mergeData( $anchor);
        $new_layout_desc = $new_layout->getLayoutAnchor( );
        $target_name = $new_layout_desc['name'];
        if ( $new_layout_desc['description'] == AMP_TEXT_SECTION_LIST ) {
            $target_name .= ' ' . ucfirst( AMP_TEXT_LIST );
        }
        $new_layout->mergeData( array( 'name' => $target_name ));
        if ( !empty( $locations )) {
            $new_layout->mergeData( array( 'locations' => $locations ));
        }
        return $new_layout;

    }

    function makeCriteriaAllowed( ) {
        $result =  AMP_lookup( 'sectionMap') ;
        unset( $result[""]);
        $keys = array_keys( $result );
        return "( ( isnull( section_id ) or section_id = 0 or section_id in ( ". join( ',', $keys ) . " )) and ( isnull( section_id_list ) or section_id_list = 0 or section_id_list in ( ".join( ',', $keys ) . ")))";
    }

}

?>
