<?php
require_once ( 'AMP/Menu/Menu.inc.php' );
require_once ( 'AMP/System/Map.inc.php');
require_once ( 'AMP/Content/Section/Menu.inc.php');

class AMP_System_Page_Menu extends AMP_Menu {
    var $_baseComponent = 'MenuComponent_scriptBase';

    function AMP_System_Page_Menu( $base_menu ) {
        $this->init( $this->loadMap( $base_menu ), 'AMP_Content_Explorer' );
    }

    function loadMap( $base_menu ) {
        $map = & AMPSystem_Map::instance();
        $menumap = $map->getMenu( $base_menu );
        $menumap[ AMP_MENU_ROOT_ENTRY ] = $menumap[ $base_menu ];
        return $menumap;
    }

    function output() {
        return $this->menuset->output( false );
    }
}
?>
