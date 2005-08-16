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
    
    function AMPSystem_Menu () {
        
        $this->init( $this->loadMap(), 'AMPSystem_Menu'  );
        $this->setStyles();
        

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
