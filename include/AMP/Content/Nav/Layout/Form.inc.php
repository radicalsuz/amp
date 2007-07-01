<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Nav/Layout/ComponentMap.inc.php');
require_once( 'AMP/Content/Nav/Location/List.inc.php');

class AMP_Content_Nav_Layout_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';
    var $_positionList;
    var $_selector_fields = array( 'introtext_id', 'section_id', 'class_id', 'section_id_list');

    function AMP_Content_Nav_Layout_Form( ) {
        $name = 'nav_layouts';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_NAV_LAYOUT );
        $this->addTranslation( 'locations', '_getLocations', 'get');
        $this->removeSubmit( 'copy' );
    }

    function adjustFields( $fields ) {
        
        if ( !( $id = $this->getIdValue( ))) {
            $id = null;
        }
        
        $fields['position_list']['default'] = $this->_getPositionList( $id );
        if ( isset( $id )){
            $fields['layout_anchor_description']['default'] = $this->_describeLayoutAnchor( $id );
            return $fields;

        }

        $requested_selectors = array_combine_key( $this->_selector_fields, $_GET );
        if ( !empty( $requested_selectors )){
            foreach( $requested_selectors as $selector => $value ){
                $fields[ $selector ]['type'] = 'select';
            }
            return $fields;
        }

        foreach( $fields as $key => $field_def  ){
            if ( array_search( $key, $this->_selector_fields ) === FALSE ) continue;
            $fields[ $key ]['type'] = 'select' ;
        }
        return $fields;
    }

    function _describeLayoutAnchor( $id ){
        $lookup_set = array( 
            AMP_TEXT_PUBLIC_PAGE  =>  AMPContent_Lookup::instance( 'navLayoutsByIntrotext' ),
            AMP_TEXT_CLASS        =>  AMPContent_Lookup::instance( 'navLayoutsByClass' ),
            AMP_TEXT_SECTION      =>  AMPContent_Lookup::instance( 'navLayoutsBySection' ),
            AMP_TEXT_SECTION_LIST =>  AMPContent_Lookup::instance( 'navLayoutsBySectionList' )
        );

        $lookup_names_set = array( 
            AMP_TEXT_PUBLIC_PAGE  =>  AMPContent_Lookup::instance( 'introTexts' ),
            AMP_TEXT_CLASS        =>  AMPContent_Lookup::instance( 'classes'  ),
            AMP_TEXT_SECTION      =>  AMPContent_Lookup::instance( 'sections' ),
            AMP_TEXT_SECTION_LIST =>  AMPContent_Lookup::instance( 'sections' )
        );

        foreach( $lookup_set as $description => $values ){
            if ( !isset( $values[$id])) continue;
            return sprintf( AMP_TEXT_CONTENT_NAV_LAYOUT_HEADER, ucwords( $description ) , $lookup_names_set[ $description ][ $values[$id] ] );
        }
        return false;
    }

    function _getPositionList( $id = null ) {
        if ( isset( $this->_positionList )) return $this->_positionList->execute( );
 
        $positionList = &new AMP_Content_Nav_Location_List( AMP_Registry::getDbcon( ));
        $positionList->applySearch( array( 'layout_id' => $id ));
        $this->_positionList = &$positionList;
        
        return $positionList->execute( );

    }

    function _getLocations( $data, $fieldname ){
        $value = $this->_positionList->getPositions( );
        return $value;
    }

    function __wakeup( ) {
        if ( isset( $this->_positionList )) {
            $this->_positionList->execute( );
        }
        parent::__wakeup( );
    }


}
?>
