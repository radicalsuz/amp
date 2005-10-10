<?php
require_once("AMP/Menu/Menu.inc.php");
require_once("AMP/Menu/FWmenuScript.inc.php");


 define( 'AMP_MENU_ACTIVATION_LOCATION_RIGHT', 'right' );
 define( 'AMP_MENU_ACTIVATION_LOCATION_BELOW', 'below' );

/**
 * Creates a dynamic javascript dropdown menu 
 * 
 * @uses AMP_Menu
 * @package Menu 
 * @version 3.4.8
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_Menu_FWTable extends AMP_Menu {

    //Script set is an additional menu hierarchy object
    var $script_set;

    var $_baseComponentHTML = 'AMP_MenuComponent_Table';
    var $_baseComponentScript = 'AMP_MenuComponent_FWmenuScriptItem';

    var $_activationMethod = 'MouseOver';
    var $_activationLocation = AMP_MENU_ACTIVATION_LOCATION_RIGHT;

    function AMP_Menu_FWTable( &$menu_array, $name="menu" ) {

        $this->init( $menu_array, $name );
    }

    function &buildMenu ( &$menu_array ) {

            //First build the table
            $component = $this->_baseComponentHTML;
            $root_menu = &new $component( $this, array('id'=>$this->name, 'label'=>'', 'href'=>'') );

            // the build call is not recursive, the table is only an anchor
            $root_menu->buildMenuSub ( $menu_array, false );

            $lastChild = end($root_menu->getChildren());
            $this->script_set = & new AMP_MenuComponent_FWmenuScriptHeader( $this, array('id'=>$this->name, 'label'=>$lastChild->id, 'href'=>'') ); 
            $this->script_set->setChildComponent( $this->_baseComponentScript );

            $this->script_set->buildMenuSub ( $menu_array );

            return $root_menu;
    }

		function output($item_name=null) {

				$this->getCSS();
				$this->script_set->setCSS();
				$this->addCSS(  $this->script_set->getCSS() );

				return ($this->outputCSS() . $this->script_set->output() . $this->menuset->output() );

		}

     function setStyle( $style_select, $new_value, $component_id = null ) {
        $component = &$this;
        if (isset($component_id)) $component = &$this->script_set->getChild( $component_id );
        if ($component) {
            $component->style[$style_select] = $new_value;
            return true;
        }
        if (isset($component_id)) $component = &$this->menuset->getChild( $component_id );
        if ($component) {
            $component->style[$style_select] = $new_value;
            return true;
        }
        return false;
     }

     function setActivationMethod( $activationMethod ) {
        $this->_activationMethod = $activationMethod;
     }

     function getActivationScript( $width ) {
        $mouseover_action = "";
        if ($this->_activationMethod != 'MouseOver' ) {
            $mouseover_action = "onMouseOver=\"if (FW_menuisActive()) { this.on".$this->_activationMethod."(); FW_clearTimeout(); }\"";
        }
		return 
            "onMouseOut=\"window.FW_startTimeout();\"\n". 
            "on".$this->_activationMethod ."=\"FW_showMenu(window.fw_menu_%1\$s,". 
            $this->_getActivationLocation( $width ) .
            ");\"\n".
            $mouseover_action;
                
     }
     function _getActivationLocation( $width ) {
        $loc_method = '_getActivationLocation' . ucfirst($this->_activationLocation );
        return $this->$loc_method( $width );
     }

     function _getActivationLocationBelow( $width ) {
        return 
        "( window.getWindowWidth() < (window.getOffLeft( this ) + ". $width . 
        ") ? (window.getOffLeft(this)+this.offsetWidth-".$width.") :  window.getOffLeft(this) ),".
        "( window.getOffTop(this) + this.offsetHeight ) ";
     }

     function _getActivationLocationRight( $width ) {
        return "(window.getOffLeft(this)+this.offsetWidth), (window.getOffTop(this))";
     }

     function setActivationLocation( $loc ) {
        $permitted_locations = filterConstants( 'AMP_MENU_ACTIVATION_LOCATION' );
        if (array_search( $loc, $permitted_locations) === false ) return false;
        $this->_activationLocation = $loc;
     }

}

class AMP_MenuComponent_Table extends AMP_MenuComponent {
		var $_child_component = "AMP_MenuComponent_FWmenuTR";

		var $template = "<table width=\"100%%\" border=\"0\" cellspacing=\"0\" id=\"%1\$s\" cellpadding=\"3\" class=\"AMPmenu\">\n%2\$s</table>\n";
		var $css_template = "
				TABLE#%5\$s td.AMPMenu {
				font-family: Verdana, Arial, Helvetica, sans-serif; background-color: #%2\$s;
						font-size: 11px; font-weight: bold; background-image: url(%3\$s); 
						background-position: 98%% 50%%; background-repeat:no-repeat; 
						border: solid black 1px; color: #%1\$s; padding-bottom: 5px;}
				TABLE#%5\$s td.AMPMenu:hover {
						background-image: url(%4\$s);
				}";
		var $css_template_vars = array( 'color', 'bgcolor', 'bg_image', 'bg_image_hover', 'id'); 

		function AMP_MenuComponent_Table (&$menu, $def ) {
				$this->init($menu, $def);
		}

		function make_core() {
				return $this->outputChildren(false);
	
	  }
}

class AMP_MenuComponent_FWmenuTR extends AMP_MenuComponent {
		#var $default_child_class = "AMP_MenuComponent_FWmenuItem";
/*	
		var $template = "\n<tr><td class=\"AMPmenu\" onMouseOut=\"window.FW_startTimeout();\" 
        onMouseOver=\"FW_showMenu(window.fw_menu_%1\$s, (window.getOffLeft(this)+this.offsetWidth), (window.getOffTop(this)));\" id=\"mrow_%1\$s\">
        %2\$s</td></tr>\n<tr><td><img src=\"img/s.gif\" width=\"5\" height=\"3\"></td></tr>\n";
        */
		
		function AMP_MenuComponent_FWmenuTR (&$menu, $def ) {
				$this->init($menu, $def);
		}

        function setCSS() {
            $childMenu = &$this->menu->script_set->getChild( $this->id );
            $childStyle = $childMenu->getStyle();
            $width = $childStyle['width'];
            $this->template = "\n<tr><td class=\"AMPmenu\" " . $this->menu->getActivationScript( $width ) .
            " id=\"mrow_%1\$s\">
            %2\$s</td></tr>\n<tr><td><img src=\"img/s.gif\" width=\"5\" height=\"3\"></td></tr>\n";
        }
            
}
?>
