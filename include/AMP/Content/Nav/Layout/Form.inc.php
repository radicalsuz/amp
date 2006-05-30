<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Nav/Layout/ComponentMap.inc.php');

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
        if ( isset( $id )) return $fields;

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

    function _getPositionList( $id = null ) {
        if ( isset( $this->_positionList )) return $this->_positionList( );
        require_once( 'AMP/Content/Nav/Location/List.inc.php');

        $positionList = &new AMP_Content_Nav_Location_List( AMP_Registry::getDbcon( ));
        $positionList->applySearch( array( 'layout_id' => $id ));
        $this->_positionList = &$positionList;
        
        return $positionList->execute( );

    }

    function _getLocations( $data, $fieldname ){
        return $this->_positionList->getPositions( );
    }


}
?>
