<?php

/* * * * * * * * * * * * * *
 * 
 * AMPSystem_Menu
 *
 * builds and outputs the dropdown menu
 *
 * AMP 3.5.0
 * 2005-07-08
 * Author: austin@radicaldesigns.org
 *
 *
 * * * * * **/

require_once ('AMP/Menu/FWTableRow.inc.php');
require_once ('AMP/System/Map.inc.php');

class AMPSystem_Menu extends AMP_Menu_FWTableRow {
    var $_baseComponentHTML = 'AMP_MenuComponent_TableRow_System';
    var $_activationMethod = 'click';
    var $_final_output;
    
    function AMPSystem_Menu () {
        
        $this->init( $this->loadMap(), 'AMPSystem_Menu'  );
        $this->setStyles();
        

    }

    function output( ){
        if ( isset( $this->_final_output )) return $this->_final_output;
        if ( $this->is_cached( ) && ( $result = $this->cachedVersion( ))) return $result;

        return $this->cache_components( );
        
    }

    function init_header( ){
        $this->_final_output = $this->cache_components( );
    }

    function cache_components( ){
        return
              $this->cache_css( )
            . $this->cache_js( )
            . $this->cache_html( );
    }

    function cache_css( ){
        $this->getCSS();
        $this->script_set->setCSS();
        $this->addCSS(  $this->script_set->getCSS() );

        if ( !( $cache = &AMP_get_cache( ))) return $this->outputCSS( );
        if ( !( $url = $cache->url( AMP_CACHE_KEY_SYSTEM_MENU_CSS ))) {
            if ( !$cache->add( $this->output_css_to_file( ), AMP_CACHE_KEY_SYSTEM_MENU_CSS )){
                return $this->outputCSS( );
            }
            $url = $cache->url( AMP_CACHE_KEY_SYSTEM_MENU_CSS );
        }

        $header = &AMPSystem_Header::instance( );
        $header->addStylesheet( $url, get_class( $this ) );
        return false;
        

    }

    function cache_js( ){
        if ( !( $cache = &AMP_get_cache( ))) return $this->script_set->output( );
        if ( !( $url = $cache->url( AMP_CACHE_KEY_SYSTEM_MENU_JS ))) {
            $output = $this->script_set->output_to_file( );
            if ( !$cache->add( $output, AMP_CACHE_KEY_SYSTEM_MENU_JS )){
                return $this->script_set->template_item_start 
                        . $output
                        . $this->script_set->template_item_end;
            }
            $url = $cache->url( AMP_CACHE_KEY_SYSTEM_MENU_JS );
        }

        $header = &AMPSystem_Header::instance( );
        $header->addJavaScript( 'scripts/fw_menu.js',  get_class( $this ).'base');
        $header->addJavaScript( $url,  get_class( $this ));
        return false;

    }

    function cache_html( ){
        if ( !( $cache = &AMP_get_cache( ))) return $this->menuset->output( );
        if ( !( $url = $cache->contains( AMP_CACHE_KEY_SYSTEM_MENU ))) {
            if ( !$cache->add( $this->menuset->output( ), AMP_CACHE_KEY_SYSTEM_MENU )){
                return $this->menuset->output( );
            }
        }
        return $cache->retrieve( AMP_CACHE_KEY_SYSTEM_MENU );

    }

    function is_cached( ){
        if ( !( $cache = &AMP_get_cache( ))) return false;
        return $cache->contains( AMP_CACHE_KEY_SYSTEM_MENU );

    }

    function cachedVersion( ){
        if ( !( $cache = &AMP_get_cache( ))) return false;
        return $cache->retrieve( AMP_CACHE_KEY_SYSTEM_MENU );
        
    }

    function loadMap() {
        $map = & AMPSystem_Map::instance();
        $menumap = $map->getMenu();
        $menumap[ AMP_MENU_ROOT_ENTRY ] = $menumap[ $map->top ];
        return $menumap;
    }

    function setStyles() {
        $this->setStyle('bgcolor_hover', 'fAfAfA');
        $this->setStyle('color_hover', '0066FF');
        $this->setStyle('bgcolor', 'dedede');
        $this->setStyle('bg_image', 'images/arrow.gif');
        $this->setStyle('bg_image_hover', 'images/arrow.gif');
        $this->setStyle('border_color', '006699');
        $this->setStyle('color', '006699');
        $this->setStyle('font_size', '12');
        $this->setStyle('font_face', 'Arial, Verdana,\'Trebuchet MS\',Helvetica,sans-serif');
        $this->setStyle('font_weight', 'bold');
        $this->setStyle('width', 150);
        $this->setStyle('width', 150, 'contenttools');
        $this->setStyle('width', 200, 'forms');
        $this->setStyle('width', 175, 'content');
        $this->setStyle('width', 200, 'navigation');
        $this->setStyle('height', 22);
    }
}

class AMP_MenuComponent_TableRow_System extends AMP_MenuComponent_TableRow {

    var $css_template = "

        TABLE#%5\$s {
            padding-right: 20px;
            position: relative;
            float: right;
        }
        TABLE#%5\$s td.AMPMenu {
            font-family: Arial, Verdana,\"Trebuchet MS\",Helvetica,sans-serif; 
            background-color: transparent;
            text-align:center;
            font-size: 12px; 
            font-weight: bold;
            white-space: nowrap;
            height: 100%;
            border: 0px; color: #%1\$s; padding-bottom: 5px;}
        ";
    function AMP_MenuComponent_TableRow_System (&$menu, $def ) {
        $this->init($menu, $def);
    }
}

?>
