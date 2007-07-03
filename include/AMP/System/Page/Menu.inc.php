<?php
require_once ( 'AMP/Menu/Menu.inc.php' );
require_once ( 'AMP/System/Map.inc.php');
require_once ( 'AMP/Content/Section/Menu.inc.php');

class AMP_System_Page_Menu extends AMP_Menu {
    var $_baseComponent = 'MenuComponent_scriptBaseMap';

    function AMP_System_Page_Menu( $base_menu ) {
        $this->init( $this->loadMap( $base_menu ), 'AMP_Content_Explorer' );
    }

    function loadMap( $base_menu ) {
        $map = & AMPSystem_Map::instance();
        trigger_error( $base_menu .' is base');
        $menumap = $map->getMenu( $base_menu );
        $menumap[ AMP_MENU_ROOT_ENTRY ] = $menumap[ $base_menu ];
        return $menumap;
    }

    function output() {
        return $this->cache_output( );
        //return $this->menuset->output( false );
    }

    function cache_output( ){
        $this->_cache  = &AMP_get_cache( );
        $this->_header = &AMP_getHeader( );
        $this->_header->addJavaScript( 'scripts/folder_tree/tree.js',     'page_menu_tree'     );
        $this->_header->addJavaScript( 'scripts/folder_tree/tree_tpl.js', 'page_menu_tree_tpl' );

        return $this->cache_js( );
    }

    function execute( ){
        return $this->output( );
    }

    function cache_js( ){
        //try to just apply the cached version of the script 
        if ( $this->_cache ) {

            $cache_key_public = sprintf( AMP_CACHE_KEY_JAVASCRIPT, get_class( $this ));
            $cache_key_private = $this->_cache->identify( $cache_key_public, AMP_SYSTEM_USER_ID );
            
            if ( $this->_cache->retrieve( $cache_key_private )) {
                return $this->_apply_cached_javascript( $cache_key_public );
            }
        }

        //generate the js
        //$js_values = $this->script_set->output_to_file( );
        $js_values = $this->menuset->output( false );

        if ( $this->_cache ) {
            $result = $this->_cache->add( $js_values, $cache_key_private );
            if ( $result ) return $this->_apply_cached_javascript( $cache_key_public );
        }

        //$this->_header->addJavascriptDynamic( $js_values,  get_class( $this ).'base');
        $js_trigger =  "new tree (TREE_ITEMS_1, tree_tpl);\n" ;
        return 
         "<script language=\"Javascript\"  type=\"text/javascript\">$js_values\n\n$js_trigger</script>\n";
        // "<script language=\"Javascript\"  type=\"text/javascript\">$js_values</script>\n";
    }

    function _apply_cached_javascript( $key ){
        $url = $this->_cache->url( $key );
        $this->_header->addJavaScript( $url, get_class( $this ) );
        #return "<script language=\"Javascript\"  type=\"text/javascript\" src=\"" . $url . "\"></script>\n";
        $js_trigger =  "new tree (TREE_ITEMS_1, tree_tpl);\n" ;
        return 
         "<script language=\"Javascript\"  type=\"text/javascript\">$js_trigger</script>\n";
    }
}

class MenuComponent_scriptBaseMap extends MenuComponent_scriptBase {
    var $_child_component = "MenuComponent_treeScriptItemMap";
    function MenuComponent_scriptBaseMap( &$menu, $def ) {
        $this->init( $menu, $def );
    }
}
class MenuComponent_treeScriptItemMap extends AMP_MenuComponent  {
    function MenuComponent_treeScriptItemMap( &$menu, $def ) {
        $this->init( $menu, $def );
        $this->_makeTemplates();
    }

    function _makeTemplates() {
        $this->core_template = 
//              "<img src=\"/system/images/edit.png\" border=\"0\" valign=\"bottom\"></a>&nbsp;"
//            . "<a href=\"%1\$s\"><img src=\"/system/images/spacer.gif\" width=\"7\" border=\"0\">"
//            . "<img src=\"/system/images/view.jpg\" border=\"0\"><img src=\"/system/images/spacer.gif\" width=\"5\" border=\"0\">"
//            . "%2\$s</a> ', '%1\$s'";
             "<a href=\"%1\$s\"><img src=\"/system/images/spacer.gif\" width=\"7\" border=\"0\">"
            . "%2\$s</a> ', '%1\$s'";

        $this->template = 
        "['</a><a href=\"system_map.php?id=%1\$s\">%2\$s";

    }
}

?>
