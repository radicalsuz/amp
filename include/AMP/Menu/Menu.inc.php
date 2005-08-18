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

     var $_baseComponent = 'AMP_MenuComponent_UL';

     //Constructor
     function AMP_Menu( &$menu_array, $name = "menu" ) {
         $this->init( $menu_array, $name );

     }

     //init routine
     function init( &$menu_array, $name ) {
         $menu_array[$name] = $menu_array[ AMP_MENU_ROOT_ENTRY ];
         $this->style['id'] = $this->name = $name;
         $this->menuset=$this->buildMenu($menu_array);
     }

     //constructs the object model
     //or, more accurately, asks the MenuComponents to construct it
     function &buildMenu (&$menu_array) {
         $root_menu= new $this->_baseComponent ( $this, array('id'=>AMP_MENU_ROOT_ENTRY,'href'=>'','label'=>'') );

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
    
    
     function setStyle( $style_select, $new_value, $component_id = null ) {
        $component = &$this;
        if (isset($component_id)) $component = &$this->root_menu->getChild( $component_id );
        if (!$component) return false;

        else return ($component->style[$style_select] = $new_value);
     }

     function getStyle() {
        return $this->style;
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


//Alias for the default format behavior of the class
class AMP_Menu_UL extends AMP_Menu {
    function AMP_Menu_UL (&$menu_array, $name="menu") {
        $this->init( $menu_array, $name);
    }
}


//UL Menu component
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

//LI Menu Component
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
}


?>
