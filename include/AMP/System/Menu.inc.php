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

    var $_cache;
    var $_header;
    var $_final_output = false;
    
    function AMPSystem_Menu () {
        
        $this->init( $this->loadMap(), 'AMPSystem_Menu'  );
        $this->setStyles();
        

    }

    function init_header( ){
        $this->_final_output = $this->cache_components( );
    }

    function output( ){
        if ( $this->_final_output ) return $this->_final_output;

        return $this->cache_components( );
        
    }

    function cache_components( ){
        $this->_cache  = &AMP_get_cache( );
        $this->_header = &AMP_getHeader( );
        return
              $this->cache_css( )
            . $this->cache_js( )
            . $this->cache_html( );
    }

    function cache_css( ){
        //try to just apply the cached version of the CSS
        if ( $this->_cache ) {
            $cache_key_public = sprintf( AMP_CACHE_KEY_STYLESHEET, get_class( $this ));
            $cache_key_private = $this->_cache->identify( $cache_key_public, AMP_SYSTEM_USER_ID );

            if ( $this->_cache->contains( $cache_key_private )) {
                $this->_cache->refresh( $cache_key_private );
                return $this->_apply_cached_stylesheet( $cache_key_public );
            }
        }

        //no cached version of stylesheet exists
        $this->getCSS();
        $this->script_set->setCSS();
        $this->addCSS(  $this->script_set->getCSS() );
        $css_values = $this->output_css_to_file( );

        if ( $this->_cache && $css_values ) {
            $result = $this->_cache->add( $css_values, $cache_key_private );
            if ( $result ) return $this->_apply_cached_stylesheet( $cache_key_public );
        }

        //cache is not active
        $this->_header->addStyleSheetDynamic( $css_values, 'AMP_System_Menu');
        return false;

    }

    function _apply_cached_stylesheet( $key ){
        $url = $this->_cache->url( $key );
        $this->_header->addStylesheet( $url, get_class( $this ) );
        return false;
    }

    function _apply_cached_javascript( $key ){
        $url = $this->_cache->url( $key );
        $this->_header->addJavaScript( $url, get_class( $this ) );
        return false;
    }

    function cache_js( ) {
        //try to just apply the cached version of the CSS
        $this->_header->addJavaScript( 'scripts/fw_menu.js',  get_class( $this ).'base');
        $script_trigger = AMP_HTML_JAVASCRIPT_START  
                        . 'fwLoadMenus( );'
                        . AMP_HTML_JAVASCRIPT_END;

        
        if ( $this->_cache ) {

            $cache_key_public = sprintf( AMP_CACHE_KEY_JAVASCRIPT, get_class( $this ));
            $cache_key_private = $this->_cache->identify( $cache_key_public, AMP_SYSTEM_USER_ID );
            
            if ( $this->_cache->contains( $cache_key_private )) {
                $this->_cache->refresh( $cache_key_private );
                $this->_apply_cached_javascript( $cache_key_public );
                return $script_trigger;
            }
        }
        

        //generate the js
        $js_values = $this->script_set->output_to_file( );
        
        if ( $this->_cache && $js_values ){
            $result = $this->_cache->add( $js_values, $cache_key_private );
            if ( $result ) {
                $this->_apply_cached_javascript( $cache_key_public );
                return $script_trigger;
            }
        }
        

        $this->_header->addJavascriptDynamic( $js_values,  get_class( $this ).'base');

        return $script_trigger;


    }

    function cache_html( ){
        if ( $this->_cache ){
            $cache_key_public = sprintf( AMP_CACHE_KEY_OUTPUT, get_class( $this ));
            $cache_key_private = $this->_cache->identify( $cache_key_public, AMP_SYSTEM_USER_ID );

            if ( $this->_cache->contains( $cache_key_private )) {
                $this->_cache->refresh( $cache_key_private );
                return $this->_cache->retrieve( $cache_key_private );
            }
        }

        $html_value = $this->menuset->output( );
        if ( !$this->_cache ) return $html_value;

        if ( $html_value ) {
            $result = $this->_cache->add( $html_value, $cache_key_private );
        }

        return $html_value;

    }
/*
    function is_cached( ){
        if ( !( $cache = &AMP_get_cache( ))) return false;
        return $cache->contains( AMP_CACHE_KEY_SYSTEM_MENU );

    }

    function cachedVersion( ){
        if ( !( $cache = &AMP_get_cache( ))) return false;
        return $cache->retrieve( AMP_CACHE_KEY_SYSTEM_MENU );
        
    }
    */

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
