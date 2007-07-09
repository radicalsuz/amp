<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Nav/Layout/ComponentMap.inc.php');
require_once( 'AMP/Content/Nav/Location/List.inc.php');
require_once( 'AMP/Content/Nav/Location/Input.php');
require_once( 'AMP/Content/Nav/Location/Values.php');

class AMP_Content_Nav_Layout_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';
    var $_position_list;
    var $_position_form;
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

        $nav_blocks = AMP_lookup( 'navBlocks');
        foreach( $nav_blocks as $block_name => $token ) {
            $fields['order_tracker_' . $block_name ] = array( 'type' => 'textarea', 'attr' => array( 'id' => 'order_tracker_amp_content_nav_location_values_'.$block_name ));
        }
        
        //$fields['position_list']['default'] = $this->_getPositionList( $id );
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

    function _formHeader( ) {
        if ( $id = $this->getIdValue( )) {
            return $this->_getPositionList( $id );
        }
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

    function _getPositionList( $id = 0 ) {
        if ( isset( $this->_positionList )) return $this->_positionList->execute( );

        $positions = &new AMP_Content_Nav_Location_Values( false, array( 'layout_id' => $id ));
        $position_form = &new AMP_Content_Nav_Location_Input( );
        $this->_position_list = &$positions;
        $this->_position_form = &$position_form;
        $this->_position_form->set( 'layout_id', $id);
 
        /*
        $positionList = &new AMP_Content_Nav_Location_List( AMP_Registry::getDbcon( ));
        $positionList->applySearch( array( 'layout_id' => $id ));
        $this->_positionList = &$positionList;
        */
        
        $renderer = AMP_get_renderer( );
        return 
                  $positions->execute( ) 
                . $renderer->newline( 1, array( 'clear' => 'all'))
                . $position_form->execute( );

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
