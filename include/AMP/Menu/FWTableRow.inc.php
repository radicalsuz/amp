<?php

require_once ('AMP/Menu/FWTable.inc.php');

class AMP_Menu_FWTableRow extends AMP_Menu_FWTable {
    var $_baseComponentHTML = 'AMP_MenuComponent_TableRow';
    var $_baseComponentScript = 'AMP_MenuComponent_FWmenuScriptItem';
    var $_activationLocation = AMP_MENU_ACTIVATION_LOCATION_BELOW;

    function AMP_Menu_FWTableRow( &$menu_array, $name="rowmenu" ) {

            $this->init( $menu_array, $name );
    }
}


class AMP_MenuComponent_TableRow extends AMP_MenuComponent_Table {
    var $_child_component = "AMP_MenuComponent_FWmenuTD";

    var $css_template = "

        TABLE#%5\$s {
            position: relative;
            float: right;
            border-spacing: 5px;
        }
        TABLE#%5\$s td.AMPMenu {
            font-family: Arial, Verdana,\"Trebuchet MS\",Helvetica,sans-serif; 
            background-color: %2\$s;
            border: 1px solid silver;
            text-align:center;
            font-size: 12px; 
            font-weight: bold;
            background-image: url( %3\$s );
            background-position: 95% 50%;
            background-repeat: no-repeat;
            padding: 5px 20px 5px 5px;
            white-space: nowrap;
            height: 100%;
            border: 0px; color: #%1\$s; padding-bottom: 5px;}
        TABLE#%5\$s td.AMPMenu:hover {
            background-image: url( %4\$s );
        }
        ";
    var $css_template_vars = array( 'color', 'bgcolor', 'bg_image', 'bg_image_hover', 'id'); 
    var $columnWidth =80;

    function AMP_MenuComponent_TableRow (&$menu, $def ) {
        $this->init($menu, $def);
    }

    function tallyWidth() {
        $cols = 0;
        $children = &$this->getChildren();
        if (empty($children)) return $cols;
        foreach( $children as $child ) {
            if (!method_exists($child, 'hasSeparator' )) {
                $cols++;
                continue;
            }
            if ($child->hasSeparator()) break;
            $cols++;
        }
        return $cols * $this->columnWidth;
    }

    function setCSS() {
        $totalwidth = $this->tallyWidth();
        $this->template = "<table border=\"0\" width=\"". ($totalwidth) ."\" cellspacing=\"0\" id=\"%1\$s\" cellpadding=\"3\" class=\"AMPmenu\"><tr>\n%2\$s</tr></table>\n";
        PARENT::setCSS();
    }
}

class AMP_MenuComponent_FWmenuTD extends AMP_MenuComponent {

    var $core_template = "%1\$s";
    var $separator = "</tr><tr>\n";
    var $_use_separator = false;

    function AMP_MenuComponent_FWmenuTD (&$menu, $def ) {
        $this->init($menu, $def);
    }

    function _register_def( $def ) {
        if (isset($def['separator']) && $def['separator']) $this->addSeparator();
    }

    function addSeparator() {
        $this->_use_separator = true ;
    }

    function hasSeparator() {
        return $this->_use_separator;
    }

    function setCSS() {
        $childMenu = &$this->menu->script_set->getChild( $this->id );
        $childStyle = $childMenu->getStyle();
        $width = $childStyle['width'];

        $this->template =  
                ( $this->_use_separator ? $this->separator : "" ) . 
                "\n<td class=\"AMPmenu\" onMouseOut=\"window.FW_startTimeout();\"\n". 
                $this->menu->getActivationScript( $width ) .
                "id=\"mrow_%1\$s\" NOWRAP>%2\$s</td>" ;
        /*
        $this->template =  
                ( $this->_use_separator ? $this->separator : "" ) . 
                "\n<td class=\"AMPmenu\" onMouseOut=\"window.FW_startTimeout();\"\n". 
                "onClick=\"FW_showMenu(window.fw_menu_%1\$s,". 
                "( window.getWindowWidth() < (window.getOffLeft( this ) + ". $width . 
                ") ? (window.getOffLeft(this)+this.offsetWidth-".$width.") :  window.getOffLeft(this) ),".
                "( window.getOffTop(this) + this.offsetHeight ) );\"\n".
                "onMouseOver=\"if (FW_menuisActive()) { this.onclick(); FW_clearTimeout(); }\"".
                "id=\"mrow_%1\$s\" NOWRAP>%2\$s</td>" ;
                */
    }

    function make_core() {
        return sprintf($this->core_template, $this->label);
    }

}
?>
