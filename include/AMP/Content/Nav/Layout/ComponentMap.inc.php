<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Nav_Layout extends AMPSystem_ComponentMap {

    var $heading = "Navigation Layout";
    var $nav_name = "nav";

    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Nav/Layout/Fields.xml',
        'list'   => 'AMP/Content/Nav/Layout/List.inc.php',
        'form'   => 'AMP/Content/Nav/Layout/Form.inc.php',
        'source' => 'AMP/Content/Nav/Layout/Layout.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Nav_Layout_Form',
        'list'  => 'AMP_Content_Nav_Layout_List',
        'source'=> 'AMP_Content_Nav_Layout');

    function onInitForm( &$controller ) {
        if ( $id = $controller->assert_var( 'id' )) return false;
        $permitted_names = array( 
            'introtext_id' => 'introTexts', 
            'section_id'   => 'sections', 
            'section_id_list' => 'sections', 
            'class_id'      => 'classes'
            );
        foreach( $permitted_names as $request_var => $name_lookup_tag ) {
            $request_value = $controller->assert_var( $request_var );
            if ( !$request_value ) continue;

            $form = &$controller->get_form( );
            $form->setDefaultValue( $request_var, $request_value );

            $name_lookup = &AMPContent_Lookup::instance( $name_lookup_tag );
            if ( !isset( $name_lookup[ $request_value ])) continue;
            $name_value = $name_lookup[ $request_value ];
            if ( $request_var == 'section_id_list' ) {
                $name_value .= ' ' . ucfirst( AMP_TEXT_LIST );
            }
            $form->setDefaultValue( 'name', $name_value );
        }
    }

    function onDelete( &$controller ){
        $item_id = &$controller->get_model_id( );
        require_once( 'AMP/Content/Nav/Location/Location.php');
        $location_source = &new AMP_Content_Nav_Location( AMP_Registry::getDbcon( ));
        $criteria = $location_source->makeCriteria( array( 'layout_id' => $item_id ));
        $locations = &$location_source->search( $criteria );
        if ( !$locations ) return false;
        foreach( $locations as $location ){
            $location->delete( );
        }
        
    }

}

?>
