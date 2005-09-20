<?php

define ( 'MEMCACHE_KEY_CONTENT' , 'PageContent' );
if (!defined( 'AMP_CONTENT_BUFFER_CONTAINER_ID' )) define ('AMP_CONTENT_BUFFER_CONTAINER_ID', false );
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_INTRO' )) define ('AMP_CONTENT_DISPLAY_KEY_INTRO', "intro");
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_BUFFER' )) define ('AMP_CONTENT_DISPLAY_KEY_BUFFER', "buffer");

require_once( 'AMP/Content/Display/HTML.inc.php' );
/**
 * AMPContent_Manager 
 *
 * The AMPContent_Manager is a controller which manages the sequence and execution of the defined 
 * displays for the page.  Each display component is specified using the 
 * {@link AMPContent_Manager::addDisplay() addDisplay} method.  Final output is retrieved using the 
 * {@link AMPContent_Manager::output() output} method.
 * 
 * Output from this controller is used by {@link AMPContent_Page} to replace the [-body-] tag of the template.
 * 
 * @uses AMPDisplay_HTML
 * @package Content 
 * @since 3.5.3 
 * @version 3.5.3 
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see AMPContent_Page
 */
class AMPContent_Manager extends AMPDisplay_HTML {

// {{{ private attributes: _displays, _display_order, _displays_executed 

    /**
     * The local collection of display objects added via {@link addDisplay}.
     * 
     * @var array
     * @access protected
     */
    var $_displays = array();

    /**
     * Specifies a sequence of displays to be executed in order
     * 
     * Default values are:
     * 1 constant( AMP_CONTENT_DISPLAY_KEY_INTRO ) = "intro"
     * 2 constant( AMP_CONTENT_DISPLAY_KEY_BUFFER) = "buffer"
     * - "intro" is used by {@link AMP/BaseModuleIntro.php} to assign an IntroText for the current Module.
     * - "buffer" is used by {@link AMP/BaseFooter.php} to store whatever output has been buffered for the current request.
     *
     * @var array
     * @access protected
     * @see setDisplayOrder
     */
    var $_display_order = array( AMP_CONTENT_DISPLAY_KEY_INTRO, AMP_CONTENT_DISPLAY_KEY_BUFFER );

    /**
     * Tracks which displays have been executed. ( internal flag )
     *
     * Display names present as keys in this array and having a non-false value will not be executed by {@link _doDisplays}.  
     * Names are added when passed as an optional argument to {@link _doDisplay}.
     * 
     * @var array
     * @access protected
     * @see resetDisplay
     */

    var $_displays_executed = array( );

// }}}

// {{{ public core methods: instance, output, execute

    /**
     * AMPContent_Manager 
     *
     * Constructor is inactive
     *
     * @ignore
     * @access public
     * @return AMPContent_Manager 
     */
    function AMPContent_Manager() {
    }

    /**
     * Returns sequenced output from all registered displays.
     *
     * Output is returned in the following sequence:
     * - Displays executed in the order specified in {@link setDisplayOrder} or the default {@link _display_order}
     * - The remaining display collection executes in the order each item was added to the collection via {@link addDisplay} 
     * 
     * If no displays have been defined, this method returns false.
     * 
     * @access public
     * @return string 
     */
    function output() {
        $output=$this->_doDisplays( $this->_display_order ).
                $this->_doDisplays();
        return $output;
    }

    /**
     * alias for output method
     *
     * @ignore
     * @access public
     * @return string 
     */
    function execute() {
        return $this->output();
    }

    /**
     * Returns an instance of the global Content Manager.
     * 
     * @access public
     * @return AMPContent_Manager 
     */
    function &instance() {
        static $manager = false;
        if (!$manager) $manager = new AMPContent_Manager;
        return $manager;
    }

// }}}

// {{{ public display assignment methods:  addDisplay, setDisplayOrder, resetDisplay

    /**
     * Adds a display to the current collection.  
     *
     * Takes an optional second parameter, name.
     * If a display with the specified name is already present in the collection, the new
     * item will replace it. Displays added without a name will be assigned a numeric index.  
     * 
     * @param object - must support the execute method to return output
     * @param string 
     * @access public
     * @return void
     */
    function addDisplay( &$display, $name = null ) {
        if (isset($name)) {
            $this->_displays[ $name ] = &$display;
            return true;
        }
        if ( !$display ) return false;
        $this->_displays[] = &$display;
    }

    /**
     * Determine the order in which a set of named displays will execute 
     *
     * The passed array will replace the default ordering of 
     * 1 intro,  or constant ( AMP_CONTENT_DISPLAY_KEY_INTRO )
     * 2 buffer, or constant ( AMP_CONTENT_DISPLAY_KEY_BUFFER )
     *
     * If your script needs to support these displays, please include the constant keys in your specified order.
     * 
     * @param array - an array of display names corresponding to values added using the {@link addDisplay} method.  Unmatched values will be ignored.
     * @access public
     * @return void 
     */
    function setDisplayOrder( $order=array() ) {
        if ( !is_array( $order)) return;
        $this->_display_order = $order;
    }

    /**
     * Clears the "executed" flag for registered displays.
     *
     * If an optional name parameter is passed, only the specified display will be cleared.
     * 
     * @param string 
     * @access public
     * @return void
     */
    function resetDisplay( $name=null ) {
        if ( !isset( $name )) return ( $this->_displays_executed = array() );
        if ( !isset( $this->_displays_executed[ $name ] )) return;
        unset( $this->_displays_executed[ $name ]);
        return;
    }

//  }}}

// {{{ private output methods: _doDisplays, _doDisplay, _doDisplayBuffer

    /**
     * Executes the current collection of display objects and returns the results as a string
     * 
     * If the optional order parameter is passed, this method will execute only displays with corresponding
     * names.  Order is expected to be an array of display names matching those designated via the {@link addDisplay} method.
     * If no order parameter is passed, all registered displays will be executed that have not been executed previously, 
     * in the order they were appended to the collection.
     *
     * @param array 
     * @access protected
     * @return string 
     */
    function _doDisplays( $order=array() ) {
        if (empty($this->_displays)) return false;
        if ( empty( $order )) $order = array_keys( $this->_displays );

        $output = "";
        foreach ($order as $display_name ) {
            if (!isset ( $this->_displays[ $display_name ] )) continue;
            if (!is_object( $this->_displays[ $display_name ] )) continue;
            $output .= $this->_doDisplay( $this->_displays[ $display_name ], $display_name );
        }

        return $output;
    }

    /**
     * Returns the output of a display object
     *
     * If the optional name parameter is passed, the {@link _displays_executed} flag is set. If this flag is already
     * set for the specified name, a value of false is returned.
     *
     * If a local method is found having the form _doDisplay[Name], that method is performed on the $display object
     * and the result returned. ( see {@link _doDisplayBuffer} for an example )
     *
     * If no such method is found, the "execute" method of the display object is called and the result returned.
     * 
     * @param object 
     * @param string 
     * @access protected
     * @return string 
     */
    function _doDisplay( &$display, $name = null ) {
        if ( !isset( $name ) )  return $display->execute();
        
        if ( isset( $this->_displays_executed[ $name ]) && $this->_displays_executed[ $name ]) return false;
        $this->_displays_executed[ $name ] = true;
        
        if ( $name ) {
            $display_method = '_doDisplay' . ucfirst( $name );
            if ( method_exists( $this, $display_method )) return $this->$display_method( $display );

        }
        
        return $display->execute();
        
    }

    /**
     * Returns the output of the display object, modifying the value in some configurations
     *
     * smartMeme/CSS feature: If the constant( AMP_CONTENT_BUFFER_CONTAINER_ID ) is defined, this method 
     * wraps the returned value in a div with the CSS "id" attribute specified by AMP_CONTENT_BUFFER_CONTAINER_ID.
     *
     * If constant( AMP_CONTENT_BUFFER_CONTAINER_ID ) is false, the "execute" method of the display object 
     * is called and the result returned.
     * 
     * @param object 
     * @access protected
     * @return string 
     */
    function _doDisplayBuffer( &$display ) {
        if (!AMP_CONTENT_BUFFER_CONTAINER_ID ) return $display->execute() ;
        return $this->_HTML_inDiv( $display->execute(), array( 'id' => AMP_CONTENT_BUFFER_CONTAINER_ID ));
    }

// }}}

}
?>
