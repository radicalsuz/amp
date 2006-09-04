<?php

/**
 * Abstract class for representing hierarchical objects 
 * 
 * @package System
 * @version 3.5.4
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMPSystem_Tree {

// {{{ attributes
    /**
     * Reference to the parent object 
     * 
     * @var AMP_MenuComponent 
     * @access public
     */
    var $parent;

    /**
     * Stores references to child objects, keyed by ID 
     * 
     * @var array 
     * @access public
     */
    var $children;

    /**
     * unique designator for the object, used for API access
     * 
     * @var string 
     * @access public
     */
    var $id;

    /**
     * string to be appended to id values when attempting to create a valid unique id ;
     * 
     * @var string 
     * @access protected
     */
    var $_id_auto_suffix;

    /**
     * Class to be generated as default child component 
     * 
     * @var string 
     * @access protected
     */
    var $_child_component;
// }}}

// {{{ methods: Initialization
    function AMPSystem_Tree() {

    }

    function setChildComponent( $classname ) {
        if (!class_exists( $classname )) return false;
        $this->_child_component = $classname;
    }

    function hasChildComponent() {
        return (isset($this->_child_component) && ($this->_child_component));
    }
// }}}

    // {{{ public methods: creating the hierarchy


    /**
     * sets the parent of the current object 
     * 
     * @param   object  &$item  References the object to be installed as parent
     * @access  public
     * @return  void
     */
    function setParent( &$item ) {
        $this->parent = & $item;
    }

    /**
     * adds a child to the object 
     * 
     * @param object    &$item the child object to be appended
     * @param string    $id    unique designator for the child
     * @access public
     * @return void
     */
    function &addChild( &$item, $id=null ) {
        if ( ( !isset( $id )) && isset( $item->id )) $id = $item->id;
        if (isset($this->children[$id])) return $this->addChildWithSameId( $item, $id ); 
        
        $item->setParent( $this );
        return ($this->children[$id] = &$item);
        
    }

    /**
     * the action to take when an addChild call is made with an ID that is already designated 
     * 
     * @param object    $item   child object to be added 
     * @param string    $id     unique id value as previously passed ;
     * @access public
     * @return void
     */
    function &addChildWithSameId( $item, $id ) {
        //if the designated id value is already determined, add '1' to the end of the
        //ID and try again
        $new_id = $id . $this->_id_auto_suffix;
        return $this->addChild( $item, $id );

    }
    // }}}
    // {{{ public methods: polling the hierarchy

    /**
     * Returns the quantity of child objects held by the current object
     * 
     * @access public
     * @return boolean 
     */
    function hasChildren() {
        if (!(isset($this->children) )) return false;
        return count($this->children);
    }

    /**
     * alias of hasChildren 
     * 
     * @access public
     * @return  integer     number of immediate children possessed by the current object 
     */
    function countChildren( ) {
        return $this->hasChildren( );
    }

    /**
     * returns the immediate children of the object in an array keyed by id 
     * 
     * @access public
     * @return array    the immediate children of the object in an array keyed by id  
     */
    function &getChildren() {
        $referenceArray = array();
        foreach ($this->children as $id => $aChild) {
            $referenceArray[$id] = &$this->children[$id];
        }
        return $referenceArray;
    }

    function &getChild($id) {
        $empty_value = false;
        if (!isset($id)) return $empty_value; 
        if ($id == $this->id) return $this;
        if (!$this->hasChildren()) return $empty_value; 

        $myChildren = &$this->getChildren();
        foreach ($myChildren as $key => $aChild) {
            if ($result = &$myChildren[$key]->getChild($id)) return $result;
        }
        return $empty_value;
    }

    // }}} 
//  {{{ public methods: action on the hierarchy
    /**
     * doChildren 
     * 
     * @param string    $method_name    method to be carried out on all child objects that support it
     * @param array     $args           arguments to be passed to the called method 
     * @access public
     * @return string   concatenated values of all returned data 
     */
    function doChildren ($method_name, $args = array(true)) {
        $output = "";
        if (!$this->hasChildren()) return $output; 

        $myChildren = & $this->getChildren();
        if (!is_array($args)) $args = array( $args );

        foreach ($myChildren as $key=>$aChild) {
            if (!method_exists($aChild, $method_name)) continue;
            $output .= call_user_func_array( array( &$myChildren[$key], $method_name), $args );
        }
        return $output;
    }
// }}}

}
?>
