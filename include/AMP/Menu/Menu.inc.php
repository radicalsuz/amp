<?php

/* * * * * * * * *
 *  AMP_Menu base class
 *  used for dynamic menus
 *  
 *  Author: austin@radicaldesigns.org
 *  5-28-2005
 *
 * * */

 require_once('AMP/Menu/Component.inc.php');


class AMP_Menu {

    //Style values;
		//the menu's style is shared by all its components by default
    var $style = array (
        'width'         => '200',
        'height'        => '20',

        'color'         => 'FFFF99',
        'bgcolor'       => 'FF0066',
        'color_hover'   => 'FFFF99',
        'bgcolor_hover' => 'FF0066',

        'font_face'     => 'Arial, Verdana, Helvetica, sans-serif',
        'font_size'     => '12',
        'font_weight'   => 'normal',
        'border_color'  => '000000',

				'bg_image'			=> 'img/point_r_wt.gif',
				'bg_image_hover'=> 'img/point_r_rd.gif',
				'id'						=> null 

        );


		//container for final inline css
		var $css;

    //Menuset is an object containing the definition of the current menu
    var $menuset;

		//the name of the menu and id of the top-level component
	  var $name;

    //Constructor
    function AMP_Menu( &$menu_array, $name = "menu" ) {
        $this->init( $menu_array, $name );

    }

    //init routine
    function init( &$menu_array, $name ) {
				$menu_array[$name] = $menu_array['1'];
				$this->style['id'] = $this->name = $name;
        $this->menuset=$this->buildMenu($menu_array);
    }

		//constructs the object model
		//or, more accurately, asks the MenuComponents to construct it
		function &buildMenu (&$menu_array) {
				$root_menu= new AMP_MenuComponent_UL ( $this, array('id'=>'1','href'=>'','label'=>'') );

				$root_menu->buildMenuSub ( $menu_array );
				return $root_menu;
		}

		//retrieve output from the menu hierarchy object
		function output() {
				return $this->menuset->output();
		}

		// CSS manipulation functions

		//retrieve stylesheet information from the menu hierarchy
    function getCSS() {
				$this->menuset->setCSS();
				return ($this->css = $this->menuset->getCSS());
    }

		//add additional stylesheet values to the menu object
		function addCSS($css) {
				$this->css .= $css;
		}

		//print the stylesheet
		function outputCSS () {
				if (!isset($this->css)) return false;
				return "<STYLE type = \"text/css\">\n".$this->css."</STYLE>\n";
		}

		

}

/* * * * * * *
 *  AMP_Menu_ArticleType
 *
 *  this function prepares an array
 *  such as the one that would be produced by reading the 
 *  articletype table
 *  to be modeled as an object tree
 *
 */

function &AMP_Menu_ArticleType($articletype_format_menuset, $format = "FWTable", $name="menu") {
   foreach ($articletype_format_menuset as $type_id=>$typeinfo) {
				$typeinfo['label'] = $typeinfo['type'];
				if (!isset($typeinfo['link'])) $typeinfo['href'] = "article.php?list=type&type=".$type_id;
				else $typeinfo['href'] = $typeinfo['link'];
				$menuset[$typeinfo['parent']][$type_id]=$typeinfo;
		}
		$classname = "AMP_Menu_".$format;
		return new $classname($menuset, $name); 
}


//Alias for the default format behavior of the class
class AMP_Menu_UL extends AMP_Menu {
		function AMP_Menu_UL (&$menu_array, $name="menu") {
				$this->init( $menu_array, $name);
		}
}
		

//UL Menu component
class AMP_MenuComponent_UL extends AMP_MenuComponent {
		var $template = "<UL id = 'listfolder_%1\$s' class='AMPmenu'>%2\$s</UL>\n";
		var $default_child_class = "AMP_MenuComponent_LI";

		function AMP_MenuComponent_UL( &$menu, $def ) {
				$this->init($menu, $def);
		}
		
		// the core of the UL object must contain all child LIs
		function make_core() {
				return $this->outputChildren(false);
		}
}

//LI Menu Component
class AMP_MenuComponent_LI extends AMP_MenuComponent {
		var $template = "<LI id = 'listitem_%1\$s' class='AMPmenu'>%2\$s</LI>\n";
		var $folder_template = "<UL class = 'AMPMenu' id = 'listfolder_%1\$s'>\n%2\$s</UL>\n";

		function AMP_MenuComponent_LI( &$menu, $def ) {
				$this->init($menu, $def);
		}

		function make_core() {
				$output = parent::make_core();
				if ($this->hasChildren()) {
						$output .= $this->makefolder();
				}
				return $output;
		}

		function makefolder() {
				return sprintf($this->folder_template, $this->id, $this->outputChildren());
		}
}


?>
