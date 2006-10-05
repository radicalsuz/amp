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
 
 define( 'AMP_MENU_ROOT_ENTRY', 1);

 /**
  * AMP_Menu 
  * 
  * @package Menu 
  * @version 3.4.8
  * @copyright 2005 Radical Designs
  * @author Austin Putman <austin@radicaldesigns.org> 
  * @license http://opensource.org/licenses/gpl-license.php GNU Public License
  */
 class AMP_Menu {

     //Style values;
     //the menu's style is shared by all its components by default
     /**
      * An array of style definitions to be applied to the generated menu CSS 
      * 
      * - width: the width ( in pixels ) of the menu elements
      * - height: the height ( in pixels ) of the menu elements
      * - color: the color of the menu item text
      * - bgcolor: the background-color of the menu elements
      * - color_hover: the color of the menu item text when under the pointer
      * - bgcolor_hover: the color of the menu element background when under the pointer
      * - font_face: the font to be used by the menu item text
      * - font_size: the size ( in pixels ) used by the menu item text
      * - font_weight: the style ( normal or bold ) of the displayed text 
      * - border_color: the color of the border for menu elements
      * - bg_image: the image used to denote child menus
      * - bg_image_hover: the image used to denote child menus when under the pointer
      * - id: the element ID tag --  this is usually generated by the menu itself
      *
      * @var array 
      * @access public
      */
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


     /**
      * contains the final inline css
      * 
      * @var string 
      * @access public
      */
     var $css;

     /**
      * an object containing the definition of the current menu
      * 
      * @var object 
      * @access public
      */
     var $menuset;

     /**
      * the name of the menu and id of the top-level component
      * 
      * @var string 
      * @access public
      */
     var $name;

     /**
      * the class of component initialized by the Menu as the root component 
      * 
      * @var string
      * @access protected
      */
     var $_baseComponent = 'AMP_MenuComponent_UL';

     /**
      * AMP_Menu 
      * 
      * @param array &$menu_array 
      * @param string $name 
      * @access public
      * @return void
      */
     function AMP_Menu( &$menu_array, $name = "menu" ) {
         $this->init( $menu_array, $name );

     }

     /**
      * init 
      * 
      * @param & $menu_array 
      * @param mixed $name 
      * @access public
      * @return void
      */
     function init( &$menu_array, $name ) {
         $menu_array[$name] = $menu_array[ AMP_MENU_ROOT_ENTRY ];
         $this->style['id'] = $this->name = $name;
         $this->menuset=$this->buildMenu($menu_array);
     }

     /**
      * Contructs the Menu object from the designated MenuComponent  
      * 
      * @param & $menu_array 
      * @access public
      * @return void
      */
     function &buildMenu (&$menu_array) {
         $root_menu= &new $this->_baseComponent ( $this, array('id'=>AMP_MENU_ROOT_ENTRY,'href'=>'','label'=>'') );

         $root_menu->buildMenuSub ( $menu_array );
         return $root_menu;
     }

     /**
      * returns an HTML representation of the current menu 
      * 
      * @access public
      * @return void
      */
     function output() {
         return $this->menuset->output();
     }

     // {{{  Style manipulation functions

     //
     /**
      * returns the current CSS data for the menu
      *
      * if no css data is defined, this method calls initCSS( );)
      * @access public
      * @return void
      */
     function getCSS() {
        if (isset( $this->css ) && $this->css) return $this->css;
        return $this->initCSS( );
     }

     /**
      * Runs setCSS on the menu hierarchy 
      * 
      * @access public
      * @return void
      */
     function initCSS( ){
        $this->menuset->setCSS();
        $this->css = $this->menuset->getCSS();
        return $this->css;

     }

     /**
      * add additional stylesheet HTML to the menu object
      * 
      * @param mixed $css 
      * @access public
      * @return void
      */
     function addCSS($css) {
         $this->css .= $css;
     }

     /**
      * returns an HTML version of the current CSS data
      * 
      * @access public
      * @return void
      */
     function outputCSS () {
         if (!isset($this->css)) return false;
         return "<STYLE type = \"text/css\">\n".$this->css."</STYLE>\n";
     }

     function output_css_to_file ( ){
         if (!isset($this->css)) return false;
         return $this->css;
     }
    
    
     /**
      * Assigns a style to the menu.
      *
      * Styles may be targeted to a particular component by specifying the component id.  Styles are automatically
      * inherited by child components.
      * 
      * @param string $style_selector   the string description of the style to be inherited. See {@link AMP_Menu::style style}.
      * @param string $value            the css value to be used
      * @param string $component_id     (Optional) the component to which the style is applied) 
      * @access public
      * @return mixed 
      */
     function setStyle( $style_selector, $value, $component_id = null ) {
        $component = &$this;
        if (isset($component_id)) $component = &$this->root_menu->getChild( $component_id );
        if (!$component) return false;

        else return ($component->style[$style_selector] = $value);
     }

     /**
      * returns the current style values for the menu 
      * 
      * @access public
      * @return array   the current style values for the object 
      */
     function getStyle() {
        return $this->style;
     }
    // }}}


 }

/* * * * * * *
 *  to be modeled as an object tree
 *
 */

/**
 *  
 *  Converts an array of values in the form produced by the articletype table 
 *  into a menu-readable format and returns the resulting menu
 *
 *  this function prepares an array
 *  such as the one that would be produced by reading the 
 *  articletype table
 *  @param array    $articletype_format_menuset    an array containing values 'type', 'id', and 'parent'
"*  @param string   $format     the type of menu object desired, default is "FWTable"
 *  @param string   $name       the name of the menu to be produced, default is "menu"
 *  @access public
 *  @return AMP_Menu
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

/**
 * Converts an XML file into an AMP_Menu instance
 *
 * @param   string     $xml_filename    Filename of the XML to be read
 * @param   string     $format          the type of menu object desired, default is "FWTable"
 * @param   string     $name            Name of the menu object to be produced
 * @param   string     $top             ID of the top level element
 * @access  public
 * @return  AMP_Menu
 */
function &AMP_Menu_XML( $xml_filename, $format = "FWTable", $name = "menu", $top = AMP_MENU_ROOT_ENTRY ) {
    require_once( 'AMP/System/XMLEngine.inc.php' );

    $xmlGet = &new AMPSystem_XMLEngine($xml_filename);
    if (!$menuset =  $xmlGet->readData()) {
        trigger_error( 'Failed to read Menu XML '.$xml_filename );
        return false;
    }
    $menumap = AMP_Menu_XML_getMenu( $menuset, $top );
    $menumap[ AMP_MENU_ROOT_ENTRY ] = $menumap[ $top ];
    $classname = "AMP_Menu_".$format;
    return new $classname($menumap, $name); 
}

/**
 * Converts an array derived from XML into an array suitable for using with AMP_Menu 
 *
 * @param   string     $menuset         Array containing values in the form 'item' with sub-elements 'label', 'href', and 'child'
 * @param   string     $startLevel      key of the parent element to be 
 * @access  public
 * @return  array      Array suitable for sending to AMP_Menu 
 */
function AMP_Menu_XML_getMenu( &$menuset, $startLevel ) {
    if (!(isset ($menuset[$startLevel]) && isset( $menuset[$startLevel]['item'] ))) return;
    if (!is_array( $menuset[$startLevel]['item'] )) return;
    $currentSet = $menuset[$startLevel]['item'] ;
    if (isset( $menuset[$startLevel]['item']['href'] )) $currentSet = array( $menuset[$startLevel]['item'] );

    foreach ($currentSet as $id => $desc) {
        $unique_id = isset($desc['child'])? $desc['child'] : $startLevel .'_'. $id;
        $result[$startLevel][ $unique_id ] = 
            $desc;
        if (isset($desc['child'])) $result = array_merge( $result, AMP_Menu_XML_getMenu( $menuset, $desc['child'] ));

    }
    return $result;
}


/**
 * Default Menu Format 
 * 
 * produces an HTML unordered list as output 
 *
 * @uses AMP_Menu
 * @package Menu
 * @version 3.4.8
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_Menu_UL extends AMP_Menu {
    function AMP_Menu_UL (&$menu_array, $name="menu") {
        $this->init( $menu_array, $name);
    }
}


/**
 * MenuComponent representing a composite element of AMP_Menu_UL 
 * 
 * @uses AMP_MenuComponent
 * @package Menu
 * @version 3.5.4
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_MenuComponent_UL extends AMP_MenuComponent {
    var $template = "\n<UL id = 'listfolder_%1\$s' class='AMPmenu'>%2\$s</UL>\n";
    var $_child_component = "AMP_MenuComponent_LI";

    function AMP_MenuComponent_UL( &$menu, $def ) {
        $this->init($menu, $def);
    }

    // the core of the UL object must contain all child LIs
    function make_core() {
        return $this->outputChildren(false);
    }
}

/**
 * MenuComponent representing a single element of AMP_Menu_UL 
 * 
 * @uses AMP_MenuComponent
 * @package Menu
 * @version 3.4.8
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_MenuComponent_LI extends AMP_MenuComponent {
    var $template = "<LI id = 'listitem_%1\$s' class='AMPmenu'>%2\$s</LI>\n";
    var $folder_template = "\n<UL class = 'AMPMenu' id = 'listfolder_%1\$s'>\n%2\$s\n</UL>\n";

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

    function __sleep( ){
        return array_keys( get_object_vars( $this ) );
    }
}


?>
