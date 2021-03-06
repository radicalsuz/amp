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

        $this->menuset = 
            $this->_allowedItems($this->_convertPermissions( $this->_init_map_values( ) ));
        $this->menuset = $this->_convertUrls( $this->menuset );
        $this->_mapForms();
        $this->_mapLists();
        $this->_buildMap();

    }

    function _init_map_values( ){
        $cache_key = AMP_CACHE_TOKEN_XML_DATA . get_class( $this );

        //check for cached version of map
        if ( $map = &AMP_cache_get( $cache_key ) ) {
            return $map;
        }

        //load map values from XML
        $map_source = &new AMPSystem_XMLEngine('Map');
        $map = $map_source->readData( );
        if ( !$map ) return array( );

        //load custom extensions to XML map
        $map_extensions_source = &new AMPSystem_XMLEngine('Map_Override');
        $map_extensions = $map_extensions_source->readData( );
        if ( !$map_extensions )  {
            $complete_map = &$map;
        } else {
            $complete_map = array_merge( $map, $map_extensions );
        }

        //cache the map
        AMP_cache_set( $cache_key, $complete_map );
        return $complete_map;

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
            if (isset($desc['child'])) {
                $child_menu = $this->getMenu( $desc['child']);
                if ( !empty( $child_menu )) {
                    $result = array_merge( $result, $child_menu );
                }
            }
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
            if (empty($m_desc['item'])) continue;
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

    function _mapLists() {
        foreach( $this->menuset as $menu_id => $menu_def ) {
            if (!isset($menu_def['list'])) continue;
            if (!isset($menu_def['list']['lookup'])) continue;

            if (!is_array($menu_def['list']['lookup'])) $itemset = &AMPSystem_Lookup::instance( $menu_def['list']['lookup'] );            
            else $itemset = &AMPSystem_Lookup::locate( $menu_def['list']['lookup'] );            
            if ( !$itemset ) continue;
            
            $sep = isset( $menu_def['list']['separator']) ? $menu_def['list']['separator'] : false;
            foreach ($itemset as $id => $name ) {
                $this->_addListItem( $id, $name, $menu_id, $menu_def['list']['href'], $sep );
                $sep = false;
            }
        }
        return true;
    }
    function _addListItem( $id, $name, $target, $href, $sep = false ) {
        $link = AMP_Url_AddVars( $href['base'], $href['var'].'='.$id );
        $def = array( 'href' => $link, 'label' => substr( $name, 0, 25), 'separator'=>$sep);
        return $this->addItem( $target, $def );
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
        $result_set=array();
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
    function _convertUrls( $set ) {
        if (empty($set)) return false;
        require_once( 'AMP/System/Page/Urls.inc.php');
        foreach ($set as $key => $item) {
            if (isset($item['item'])) $set[$key]['item'] = $this->_convertUrls( $item['item'] );
            if (!( is_array($item) && isset( $item['href']))) continue;
            if (defined($item['href'])) $set[$key]['href'] = constant( $item['href']) ;
        }
        return $set;
    }


}
?>
