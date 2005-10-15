<?php

require_once ( 'AMP/Menu/Menu.inc.php' );
require_once ( 'AMP/Content/Map.inc.php');

class SectionMenu extends AMP_Menu {
    var $_baseComponent = 'MenuComponent_scriptBase';

    function SectionMenu() {
        $this->init( $this->loadMap(), 'AMP_Content_Explorer' );
    }

    function loadMap() {
        $map = & AMPContent_Map::instance();
        $menumap = $map->getMenu();
        $menumap[ AMP_MENU_ROOT_ENTRY ] = $menumap[ $map->top ];
        return $menumap;
    }

    function output() {
        return $this->menuset->output( false );
    }
}

class MenuComponent_scriptBase extends AMP_MenuComponent {
    var $template = 
    "<script language='JavaScript' src='/scripts/folder_tree/tree.js'></script>
    <script language='JavaScript' src='/scripts/folder_tree/tree_tpl.js'></script>
    <script language='JavaScript'>
            var TREE_ITEMS_%1\$s =  [\n%2\$s\n];
            new tree (TREE_ITEMS_%1\$s, tree_tpl); 
    </script>";
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
        "<img src=\"/system/images/edit.png\" border=\"0\" valign=\"bottom\"></a>&nbsp;".
        "<a href=\"%1\$s\"><img src=\"/system/images/spacer.gif\" width=\"7\" border=\"0\">".
        "<img src=\"/system/images/view.jpg\" border=\"0\"><img src=\"/system/images/spacer.gif\" width=\"5\" border=\"0\">".
        "%2\$s</a> ', '%1\$s'";

        $this->template = 
        "['</a><a href=\"type_edit.php?id=%1\$s\">%2\$s";

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
