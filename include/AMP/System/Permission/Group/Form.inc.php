<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/Permission/Group/ComponentMap.inc.php');

class PermissionGroup_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function PermissionGroup_Form( ) {
        $name = 'PermissionGroup';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_PERMISSION_GROUP );
    }

    function _after_init( ) {
        $this->_addSettingsField( );
        $this->addTranslation( 'sections', 'hideForSpecialUsers', 'set');
    }

    function _initJavascriptActions( ){
        $this->_init_sectional_script( );
    }

    function _addSettingsField( ){
        $this->addTranslation( 'settings', '_checkgroupToArray', 'get');
        $this->addTranslation( 'settings', '_checkgroupFromArray', 'set');
    }

    function hideForSpecialUsers( $data, $fieldname ) {
        //don't allow removal of content for user groups 1 and 3 (  admin + all permissions )
        if ( !isset( $data['id'])) return;
        if ( $data['id'] == 1 or $data['id'] == 3 ) {
            $this->form->removeElement( 'sections');
            $this->form->removeElement( 'root_section_id');
        //    $this->addField( array( 'type' => 'static', 'value' => 'Changes are not allowed for this group', 'block' => 'sectional'), 'section_plus');
        }
        return $data[$fieldname];
    }

    function _init_sectional_script( ) {
        $map = AMPContent_Map::instance( );
        $start_group = $map->getChildren( AMP_CONTENT_MAP_ROOT_SECTION );
        $start_group = $map->getAllParents( );
        $script_output = '';
        foreach( $start_group as $section_id ) {
            if ( $section_id == AMP_CONTENT_MAP_ROOT_SECTION ) continue;
            $children =$map->getChildren( $section_id );
            if ( !$children ) continue;
            $script_output .= 'document.forms["'.$this->formname.'"].elements["sections['.$section_id.']"].onchange=function( ) {' . "\n"
                . $this->_init_sectional_children( $children, $map )
                . '};' . "\n\n";

        }
        $script_output = 'function init_section_checkbox_cascade( ) { ' . "\n" . $script_output . "\n" . '}';
        $header = AMP_get_header( );
        $header->addJavascriptDynamic( $script_output, 'section_checkboxes');
        $header->addJavascriptOnload( 'init_section_checkbox_cascade( );');
    }

    function _init_sectional_children( $children, &$map ) {
        $output = '';
        foreach( $children as $section_id ) {
            $output .= '        document.forms["'.$this->formname.'"].elements["sections['.$section_id.']"].checked=this.checked;' . "\n";
            $new_children = $map->getChildren( $section_id );
            if ( !$new_children ) continue;
            $output .= $this->_init_sectional_children( $new_children, $map );
        }
        return $output;
    }

    function _selectAddNull( $valueset, $name ) {
        $required_selects = array( 'root_section_id' );
        if ( array_search( $name, $required_selects ) === FALSE ) return parent::_selectAddNull( $valueset, $name );
        return array( AMP_CONTENT_SECTION_ID_ROOT => '-- ' . AMP_SITE_NAME . ' --') + $valueset;
    }
}
?>
