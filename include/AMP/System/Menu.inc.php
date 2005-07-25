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

require_once ('AMP/Menu/FWTable.inc.php');
require_once ('AMP/System/Map.inc.php');

class AMPSystem_Menu extends AMP_Menu_FWTable {
    var $_baseComponentHTML = 'AMP_MenuComponent_TableRow';
    var $_baseComponentScript = 'AMP_MenuComponent_FWmenuScriptItem';

    function AMPSystem_Menu () {
        
        $this->init( $this->loadMap(), 'AMPSystem_Menu'  );
        $this->setStyles();
        

    }

    function output($item_name=null) {

            $this->getCSS();
            $this->script_set->setCSS();
            $this->addCSS(  $this->script_set->getCSS() );

            return ($this->outputCSS() . $this->script_set->output() . $this->menuset->output() );
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

class AMP_MenuComponent_TableRow extends AMP_MenuComponent_Table {
    var $_child_component = "AMP_MenuComponent_FWmenuTD";

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
            border: opx; color: #%1\$s; padding-bottom: 5px;}
        ";
    var $css_template_vars = array( 'color', 'bgcolor', 'bg_image', 'bg_image_hover', 'id'); 
    var $columnWidth =80;

    function AMP_MenuComponent_TableRow (&$menu, $def ) {
        $this->init($menu, $def);
    }

    function setCSS() {
        $totalwidth = count($this->getChildren()) * $this->columnWidth;
        $this->template = "<table border=\"0\" width=\"". ($totalwidth) ."\" cellspacing=\"0\" id=\"%1\$s\" cellpadding=\"3\" class=\"AMPmenu\"><tr>\n%2\$s</tr></table>\n";
        parent::setCSS();
    }
}

class AMP_MenuComponent_FWmenuTD extends AMP_MenuComponent {

    var $core_template = "%1\$s";

    function AMP_MenuComponent_FWmenuTD (&$menu, $def ) {
        $this->init($menu, $def);
    }

    function setCSS() {
        $childMenu = &$this->menu->script_set->getChild( $this->id );
        $childStyle = $childMenu->getStyle();
        $width = $childStyle['width'];

        $this->template =  "\n<td class=\"AMPmenu\" onMouseOut=\"window.FW_startTimeout();\"\n". 
                "onClick=\"FW_showMenu(window.fw_menu_%1\$s,". 
                "( window.getWindowWidth() < (window.getOffLeft( this ) + ". $width . 
                ") ? (window.getOffLeft(this)+this.offsetWidth-".$width.") :  window.getOffLeft(this) ),".
                "( window.getOffTop(this) + this.offsetHeight ) );\"\n".
                "onMouseOver=\"if (FW_menuisActive()) { this.onclick(); FW_clearTimeout(); }\"".
                "id=\"mrow_%1\$s\" NOWRAP>%2\$s</td>";
    }

    function make_core() {
        return sprintf($this->core_template, $this->label);
    }

}

?>
