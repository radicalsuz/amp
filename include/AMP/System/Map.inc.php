<?php

/* * * * * * * * * *
 * 
 *  AMPSystem_Map
 *
 *  Models the structure for the system-side user interface
 *
 *  AMP 3.5.0
 *  2005-07-09
 *  Author: austin@radicaldesigns.org
 *
 * * * * * * **/
require_once( 'AMP/System/XMLEngine.inc.php' );
require_once( 'AMP/System/Permission/Manager.inc.php' );

class AMPSystem_Map {

    var $menuset;
    var $map;
    var $top = "home";

    function AMPSystem_Map() {
        $this->init();
    }

    function &instance() {
        static $system_map = false;

        if (!$system_map) $system_map = new AMPSystem_Map();
        return $system_map;
    }


    function init(){
        $this->per_manager = & AMPSystem_PermissionManager::instance();
        $xmlGet = &new AMPSystem_XMLEngine('Map');

        $this->menuset = 
            $this->_allowedItems($this->_convertPermissions( $xmlGet->readData() ));
        $this->_mapForms();
        $this->_buildMap();

    }

    #############################
    ### Public Access Methods ###
    #############################

    function getParent( $id ) {
        foreach ($this->map as $parent => $childset) {
            if (array_search($id, $childset)!==FALSE) return $parent;
        }
        return false;
    }

    function getName( $id ) {
        return $this->menuset[$id]['title'];
    }

    function getDepth( $id ) {
        if (!$id) return 0;
        if ($id == $this->top ) return 0;
        return $this->getDepth( $this->getParent( $id )) + 1;
    }

    function getMenu( $startLevel = null ) {
        if (!isset($startLevel)) $startLevel = $this->top;
        if (!(isset ($this->menuset[$startLevel]) && isset( $this->menuset[$startLevel]['item'] ))) return;
        if (!is_array( $this->menuset[$startLevel]['item'] )) return;

        foreach ($this->menuset[$startLevel]['item'] as $id => $desc) {
            $unique_id = isset($desc['child'])? $desc['child'] : $startLevel .'_'. $id;
            $result[$startLevel][ $unique_id ] = 
                $desc;
            if (isset($desc['child'])) $result = array_merge( $result, $this->getMenu( $desc['child'] ));

        }

        return $result;
    }

    function isLocation( $name ) {
        return (isset($this->menuset[$name]) && $this->menuset[$name]);
    }


    ############################
    ### Public Setup Methods ###
    ############################

    function addLocation( $location, $def ) {
        $this->menuset[ $location ] = $def;
    }

    function addItem( $location, $def ) {
        $this->menuset[$location]['item'][] = $def;
    }

    function addChild( $parent, $child_def ) {
        $this->map[$parent][] = $child_def;
    }

    #############################
    ### Private Setup Methods ###
    #############################


    function _buildMap() {
        if (!is_array($this->menuset)) return false;
        foreach ($this->menuset as $menuname => $m_desc) {
            if (!isset($m_desc['item'])) continue;
            $this->_addChildren( $menuname, $m_desc['item'] );
        }
    }
    
    function _addChildren( $menuname, $item_list ) {
        foreach ($item_list as $item => $item_desc) {
            if (!isset($item_desc['child'])) continue;
            
            $this->addChild( $menuname, $item_desc['child'] );
        }
    }

    function _mapForms() {
        if (!AMP_Authorized( AMP_PERMISSION_FORM_ACCESS )) return false;
        if (!($formset = &AMPSystem_Lookup::instance('Forms'))) return false;
        $form_menus=array();
        $this->_addToolFormsMenu();
        foreach ($formset as $id => $name ) {
            $this->_addFormItem( $id, $name ); 
            
        }
               
        return true;
    }


    function _addFormItem( $id, $name ) {
        $def = array( 'href' => ('userdata_list.php?modin=' . $id), 'label' => substr( $name, 0, 25));
        $target = ($id<50) ? 'toolforms' : 'forms';
        if ($target=="forms") $def = $this->_checkSeparator( $def );
        return $this->addItem( $target, $def );
    }

    function _addToolFormsMenu() {
        $link_def = 
            array(  'label'     => 'Standard Forms', 
                    'href'      => 'modinput4_list.php',
                    'per'   => AMP_PERMISSION_TOOLS_ACCESS, 
                    'child'     => 'toolforms' );
        $this->addItem( 'forms', $link_def );

        $location_def = array(
            'label' => 'Standard Forms',
            'href'  => 'system_map.php?id=toolforms',
            'per'   => AMP_PERMISSION_TOOLS_ACCESS );
        $this->addLocation( 'toolforms', $location_def );
    }

    function _checkSeparator( $def ) {
        static $separated = false;
        if ($separated) return $def;
        $separated = true;
        $def['separator'] = $separated;
        return $def;
    }

    function _allowedItems( $set ) {
        if (empty($set)) return false;
        foreach ($set as $key => $item ) {
            if (!is_array($item)) continue;
            if (isset($item['per']) && !AMP_Authorized($item['per'])) continue;
            $result_set[$key] = $item;
            if (isset($item['item'])) $result_set[$key]['item'] = $this->_allowedItems( $item['item'] );
        }
        return $result_set;
    }

    function _convertPermissions( $set ) {
        if (empty($set)) return false;
        foreach ($set as $key => $item) {
            if (!is_array($item)) continue;
            if (isset($item['per'])) $set[$key]['per'] = $this->per_manager->convertDescriptor( $item['per'] );
            if (isset($item['item'])) $set[$key]['item'] = $this->_convertPermissions( $item['item'] );
        }
        return $set;
    }

}
?>
