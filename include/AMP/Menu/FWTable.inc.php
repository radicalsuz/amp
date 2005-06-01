<?php
require_once("AMP/Menu/Menu.inc.php");
require_once("AMP/Menu/FWmenuScript.inc.php");

/* * * * * * * * *
 * AMP_Menu_FWTable
 * 
 * use the fw_menu.js script
 * anchored in a table
 * to produce a dynamic menu
 *
 * Author: austin@radicaldesigns.org
 *
 * Date: 6/1/2005
 *
 * * */

class AMP_Menu_FWTable extends AMP_Menu {

		//Script set is an additional menu hierarchy object
		var $script_set;

		function AMP_Menu_FWTable( &$menu_array, $name="menu" ) {

				$this->init( $menu_array, $name );
		}

		function &buildMenu ( &$menu_array ) {

				//First build the table
				$root_menu = new AMP_MenuComponent_Table( $this, array('id'=>$this->name, 'label'=>'', 'href'=>'') );

				// the build call is not recursive, the table is only an anchor
				$root_menu->buildMenuSub ( $menu_array, false );

				$lastChild = end($root_menu->getChildren());
				$this->script_set = &new AMP_MenuComponent_FWmenuScriptHeader( $this, array('id'=>$this->name, 'label'=>$lastChild->id, 'href'=>'') ); 

				$this->script_set->buildMenuSub ( $menu_array );

				return $root_menu;
		}

		function output($item_name=null) {

				$this->getCSS();
				$this->script_set->setCSS();
				$this->addCSS(  $this->script_set->getCSS() );

				return ($this->outputCSS() . $this->script_set->output() . $this->menuset->output() );

		}
}

class AMP_MenuComponent_Table extends AMP_MenuComponent {
		var $default_child_class = "AMP_MenuComponent_FWmenuTD";

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

class AMP_MenuComponent_FWmenuTD extends AMP_MenuComponent {
		#var $default_child_class = "AMP_MenuComponent_FWmenuItem";
	
		var $template = "\n<tr><td class=\"AMPmenu\" onMouseOut=\"window.FW_startTimeout();\" 
        onMouseOver=\"FW_showMenu(window.fw_menu_%1\$s, (window.getOffLeft(this)+this.offsetWidth), (window.getOffTop(this)));\" id=\"mrow_%1\$s\">
        %2\$s</td></tr>\n<tr><td><img src=\"img/s.gif\" width=\"5\" height=\"3\"></td></tr>\n";
		
		function AMP_MenuComponent_FWmenuTD (&$menu, $def ) {
				$this->init($menu, $def);
		}
}
?>
