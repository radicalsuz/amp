<?php
require_once( 'AMP/System/Tree.inc.php');

/**
 * Composite object controlling output and representation of AMP_Menu 
 * 
 * @package Menu
 * @uses AMPSystem_Tree
 * @version 3.4.8
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMP_MenuComponent extends AMPSystem_Tree {

    // {{{ attributes: core characteristics 
    /**
     * Reference to the parent MenuHandle object 
     * 
     * @var AMP_Menu 
     * @access public
     */
    var $menu;

    /**
     * unique designator for the object, used by both the stylesheet and the AMP_Menu API 
     * 
     * @var string 
     * @access public
     */
    var $id;
    /**
     * URL triggered when the menu element is selected 
     * 
     * @var string 
     * @access public
     */
    var $href;
    /**
     * Title displayed for the menu element 
     * 
     * @var mixed
     * @access public
     */
    var $label;
    // }}} 

    // {{{ attributes: style values

    /**
     * Locally held style values 
     * 
     * This allows you to override the values held in the menu {@link AMP_Menu::style style} array
     * All child MenuComponents will inherit values held here.
     *
     * @var     array
     * @access  public
     */
    var $style = array();

    /**
     * CSS generated by this component 
     * 
     * @var     string 
     * @access  public
     */
    var $css;

    /**
     * Template for CSS data 
     *
     * Values supplied via the getStyle method are applied via vsprintf to this template to generate the local CSS
     * 
     * @var     string 
     * @access  public
     */
    var $css_template;

    /**
     * css_template_vars 
     * 
     * @var     array   a set of style selectors required by the designated css_template to create valid local CSS.  See {@link AMP_Menu::style} for details. 
     * @access  public
     */
    var $css_template_vars;
    //}}}
    
    // {{{ attributes: HTML templates
    /**
     * template used to generate the complete menu element HTML
     * 
     * @var string 
     * @access public
     */
    var $template;
    /**
     * template used to generate the link HTML 
     * 
     * @var string
     * @access public
     */
    var $core_template = "<a class=\"AMPmenu\" href=\"%1\$s\">%2\$s</a>";
    /**
     * template used to generate HTML for composite elements
     * 
     * @var mixed
     * @access public
     */
    var $folder_template;
    // }}}

    // {{{ public methods: Initialization 
    /**
     * stub constructor 
     * 
     * @param   AMP_Menu    &$menu  root Menu object 
     * @param   array       $def    array of elements 'id', 'href', and 'label' 
     * @access public
     * @ignore
     * @return void
     */
    function AMP_MenuComponent( &$menu, $def ) {
        $this->init( $menu, $def );
    }

    /**
     * Initializes the MenuComponent 
     * 
     * @param   AMP_Menu    &$menu  root Menu object 
     * @param   array       $def    array of elements 'id', 'href', and 'label' 
     * @access public
     * @return void
     */
    function init( &$menu, $def ) {
        $retain_values = array( "id", "href", "label" );
        foreach ($retain_values as $retain) {
            if (isset($def[$retain]))  $this->$retain = $def[$retain];
        }

        $this->menu 	= & $menu;
        if (method_exists( $this, '_register_def' )) $this->_register_def( $def );

        if (!$this->hasChildComponent()) $this->setChildComponent( get_class($this) );
    }

    /**
     * initializes child MenuComponents for the current object based on a supplied array 
     * 
     * @param   array   &$menu_array    An array of menu def arrays, containing the id of the current MenuComponent as a key 
     * @param   boolean $recursive      True if the routine should initialize all sub arrays, or only the top level of the supplied menu_array 
     * @access  public
     * @return  void
     */
    function buildMenuSub ( &$menu_array , $recursive=true  ) {
        if (!( isset( $menu_array[$this->id] ) && is_array( $menu_array[$this->id] ) )) return false;

        foreach ($menu_array[$this->id] as $menu_id => $menu_def) {
            $menu_def['id'] = $menu_id;
            if ($child = & $this->addChild( new $this->_child_component ( $this->menu , $menu_def ) )) {
                #print get_class($this) . 'is building kid ' . $menu_def['label'] .'<BR>';
                if ($recursive) $child->buildMenuSub ( $menu_array );
            }
        }
    }
    // }}}

    // {{{ public methods: Output 
    /**
     * Returns rendered output for the MenuComponent and (optionally) subcomponents. 
     * 
     * @param   boolean     $returnChildren     Includes rendered output for child components if true. 
     * @access  public
     * @return  string      Rendered output for the MenuComponent and (optionally) subcomponents. 
     */
    function output($returnChildren=false) {
        $output = sprintf($this->template, $this->id, $this->make_core());
        if ($returnChildren) $output .= $this->outputChildren(); 

        return $output;
    }

    /**
     * Returns the rendered output for the element core  
     * 
     * @access public
     * @return  string   results of applying {@link AMP_MenuComponent::href href} and {@link AMP_MenuComponent::label label} values to the {@link AMP_MenuComponent::core_template core_template}.
     */
    function make_core() {
        return sprintf($this->core_template, $this->href, $this->label);
    }

    /**
     * Returns rendered output for all child MenuComponents, optionally including all child generations via recursion. 
     * 
     * @param   boolean     $returnChildren     (default is True) if True, returns output for all child generations. if False, only output of the the immediate child MenuComponents is returned
     * @access  public
     * @return  string      Rendered output for child MenuComponents 
     */
    function outputChildren($returnChildren=true) {
            return $this->doChildren('output', $returnChildren);
    }

    // }}} 

// {{{ public methods:  Managing style information 

    /**
     * returns the style information which applies to the object 
     * 
     * @access public
     * @return array    style information to be used by the current object 
     */
    function getStyle() {
        $parent = &$this->menu;
        if ($this->parent) $parent = &$this->parent;

        if (empty($this->style)) return $parent->getStyle();
        
        return array_merge( $parent->getStyle(), $this->style);
    }
            
    /**
     * return the generated CSS for the element 
     * 
     * @param boolean $recursive    if true, generated CSS for all child components is included 
     * @access public
     * @return string       inline css values 
     */
    function getCSS($recursive = true) {
            $output = "";
            if ($recursive) $output .= $this->doChildren("getCSS");
            
            return $this->css . $output;
    }

    /**
     * creates the generated CSS for the object if a {@link AMP_MenuComponent::css_template} is defined.
     * 
     * @param boolean   $recursive  if True (default) setCSS command is passed on to child Components 
     * @access public
     * @return void
     */
    function setCSS ($recursive = true) {

        if (isset($this->css_template) && is_array($this->css_template_vars)) {
            $this->css=$this->evalCSS();				
        }

        if ($recursive) $this->doChildren('setCSS');
    }

    /**
     * combines the current style values with the {@link AMP_MenuComponent::css_template} and returns CSS 
     * 
     * @access public
     * @return string   CSS generated from the css_template and style values 
     */
    function evalCSS () {
        if (($styleset = array_combine_key($this->css_template_vars, $this->getStyle()))) {
            if (isset($styleset['id'])) $styleset['id']= $this->id;
            return vsprintf($this->css_template, $styleset);
        }
        return false;
    }

// }}}

}

?>
