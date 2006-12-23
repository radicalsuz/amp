<?php

require_once ( 'AMP/Menu/Menu.inc.php' );
require_once ( 'AMP/Content/Map.inc.php');

class SectionMenu extends AMP_Menu {
    var $_baseComponent = 'MenuComponent_scriptBase';
    var $_cache;
    var $_header;

    function SectionMenu() {
        $this->init( $this->loadMap(), 'AMP_Content_Explorer' );
    }

    function loadMap() {
        $map = & AMPContent_Map::instance();
        $menumap = $map->getMenu();
        if ( isset( $menumap[ $map->top ])) {
            $menumap[ AMP_MENU_ROOT_ENTRY ] = $menumap[ $map->top ];
        }
        return $menumap;
    }

    function output() {
        return $this->cache_output( );
        //return $this->menuset->output( false );
    }

    function cache_output( ){
        $this->_cache  = &AMP_get_cache( );
        $this->_header = &AMP_getHeader( );
        $this->_header->addJavaScript( 'scripts/folder_tree/tree.js',     'section_menu_tree'     );
        $this->_header->addJavaScript( 'scripts/folder_tree/tree_tpl.js', 'section_menu_tree_tpl' );

        return $this->cache_js( );
    }

    function execute( ){
        return $this->output( );
    }

    function cache_js( ){
        //try to just apply the cached version of the script 
        if ( $this->_cache ) {

            $cache_key_public = AMP_CACHE_TOKEN_LOOKUP . sprintf( AMP_CACHE_KEY_JAVASCRIPT, get_class( $this ));
            $cache_key_private = $this->_cache->identify( $cache_key_public, AMP_SYSTEM_USER_ID );
            
            //if ( $this->_cache->contains( $cache_key_private )) {
            if ( $js_values = $this->_cache->retrieve( $cache_key_private )) {
                $this->_cache->refresh( $cache_key_private );
                //return $this->_apply_cached_javascript( $cache_key_public );
                return $this->_apply_cached_javascript( $js_values );
            }
        }

        //generate the js
        //$js_values = $this->script_set->output_to_file( );
        $js_values = $this->menuset->output( false );

        if ( $this->_cache ) {
            $result = $this->_cache->add( $js_values, $cache_key_private );
            //if ( $result ) return $this->_apply_cached_javascript( $cache_key_public );
        }

        //$this->_header->addJavascriptDynamic( $js_values,  get_class( $this ).'base');
        $js_trigger =  "new tree (TREE_ITEMS_1, tree_tpl);\n" ;
        return 
         "<script language=\"Javascript\"  type=\"text/javascript\">$js_values\n\n$js_trigger</script>\n";
    }

    //function _apply_cached_javascript( $key ){
    function _apply_cached_javascript( $js_values ){
        //$url = $this->_cache->url( $key );
        //$this->_header->addJavaScript( $url, get_class( $this ) );
        $js_trigger =  "new tree (TREE_ITEMS_1, tree_tpl);\n" ;
        return 
         "<script language=\"Javascript\"  type=\"text/javascript\">$js_values\n\n$js_trigger</script>\n";
    }

}

class MenuComponent_scriptBase extends AMP_MenuComponent {
    var $template = 
//        "<script language='JavaScript' src='/scripts/folder_tree/tree.js'></script>
//        <script language='JavaScript' src='/scripts/folder_tree/tree_tpl.js'></script>
//        <script language='JavaScript'>
"                var TREE_ITEMS_%1\$s =  [\n%2\$s\n];";
//                new tree (TREE_ITEMS_%1\$s, tree_tpl);\n" ;
//        </script>";
    var $_child_component = "MenuComponent_treeScriptItem";
    var $spacer10 = '<img src="/system/images/spacer.gif" width="10" height="10" border="0">';

    function MenuComponent_scriptBase( &$menu, $def ) {
        $this->init( $menu, $def );
    }

    function make_core() {
        return "[\n '". $this->spacer10 . "<a class=\"standout\">". AMP_clearSpecialChars( AMP_SITE_NAME ) ."</a>', '', " . $this->outputChildren( false ) . "]";
        /*
        return '[\'</a><a href="type_edit.php?id=1"><img src="images/edit.png" border="0" valign="bottom"></a>'.
                '&nbsp;<a href="article_list.php?type=1"><img src="images/spacer.gif" width="7" border="0">'.
                '<img src="images/view.jpg" border="0"><img src="images/spacer.gif" width="5" border="0">Front Page</a> \''.
                ', \'article_list.php?type=1\', ' .$this->outputChildren( false ) . ']' ;
                */
                
    }
}

class MenuComponent_treeScriptItem extends AMP_MenuComponent  {

    function MenuComponent_treeScriptItem( &$menu, $def ) {
        $this->init( $menu, $def );
        $this->_makeTemplates();
    }

    function _makeTemplates() {
        $this->core_template = 
              "<img src=\"/system/images/edit.png\" border=\"0\" valign=\"bottom\"></a>&nbsp;"
            . "<a href=\"%1\$s\"><img src=\"/system/images/spacer.gif\" width=\"7\" border=\"0\">"
            . "<img src=\"/system/images/view.jpg\" border=\"0\"><img src=\"/system/images/spacer.gif\" width=\"5\" border=\"0\">"
            . "%2\$s</a> ', '%1\$s'";

        $this->template = 
        "['</a><a href=\"section.php?id=%1\$s\">%2\$s";

    }

    function make_core() {
        $output = parent::make_core();
        if ($this->hasChildren()) {
            $output .= $this->makefolder();
        }
        return $output . "],\n";
    }

    function makefolder() {
        return ', '. $this->outputChildren( false );
    }


}
?>
