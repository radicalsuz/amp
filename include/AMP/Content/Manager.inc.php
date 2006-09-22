<?php

if (!defined( 'AMP_CONTENT_BUFFER_CONTAINER_ID' )) define ('AMP_CONTENT_BUFFER_CONTAINER_ID', false );
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_FLASH' )) define ('AMP_CONTENT_DISPLAY_KEY_FLASH', "flash");
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_INTRO' )) define ('AMP_CONTENT_DISPLAY_KEY_INTRO', "intro");
if (!defined( 'AMP_CONTENT_DISPLAY_KEY_BUFFER' )) define ('AMP_CONTENT_DISPLAY_KEY_BUFFER', "buffer");

/**
 * Controller for the main body of the page 
 *
 * Defines the sequence of content items making up the main body of the current page.  
 * Any number of individual "displays" can be added using the {@link AMPContent_Manager::addDisplay() addDisplay} method.  
 * To get the output produced by these displays, use the {@link AMPContent_Manager::output() output} method.  Unless otherwise specified
 * ( see {@link AMPContent_Manager::setDisplayOrder() setDisplayOrder} ), display output is returned in the order the displays were added to the AMPContent_Manager.
 * 
 * Output from this controller is used by {@link AMPContent_Page} to replace the [-body-] tag of the template.
 * 
 * @uses AMPDisplay_HTML
 * @package Content 
 * @since 3.5.3 
 * @version 3.5.8 
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @see AMPContent_Page
 */
class AMPContent_Manager {

// {{{ private properties: _displays, _display_order, _displays_executed 

    /**
     * The local collection of display objects added via {@link addDisplay}.
     * 
     * @var array
     * @since 3.5.3 
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
     * @since 3.5.3 
     * @see setDisplayOrder
     */
    var $_display_order = array( AMP_CONTENT_DISPLAY_KEY_FLASH, AMP_CONTENT_DISPLAY_KEY_INTRO, AMP_CONTENT_DISPLAY_KEY_BUFFER );

    /**
     * Tracks which displays have been executed. ( internal flag )
     *
     * Display names present as keys in this array and having a non-false value will not be executed by {@link _doDisplays}.  
     * Names are added when passed as an optional argument to {@link _doDisplay}.
     * 
     * @var array
     * @since 3.5.3 
     * @access protected
     * @see resetDisplay
     */

    var $_displays_executed = array( );

    /**
     * Tracks which displays should have their output cached upon execution, and under what key 
     * 
     * Displays are stored in the format array( display_key => cache_key ).  Requests for caching
     * are registered with the cache method.
     *
     * @var array
     * @since 3.5.9
     * @access protected
     */
    var $_displays_cacheable = array( );

    /**
     * Creates any required enveloping markup for displays 
     * 
     * Usually an instance of AMPDisplay_HTML
     * @var object  
     * @access protected
     * @since 3.5.8
     */
    var $_renderer;

// }}}

// {{{ public core methods: instance, output, execute

    /**
     * AMPContent_Manager 
     *
     * Constructor doesn't do anything
     *
     * @ignore
     * @access public
     * @return AMPContent_Manager 
     */
    function AMPContent_Manager() {
        $this->__construct( );
    }

    function __construct( ) {
        $this->_renderer = AMP_get_renderer( );
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
     * @since 3.5.3
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
     * @since 3.5.3
     * @return string 
     */
    function execute() {
        return $this->output();
    }

    /**
     * Returns an instance of the global Content Manager.
     * 
     * @access public
     * @since 3.5.3
     * @return AMPContent_Manager 
     */
    function &instance() {
        static $manager = false;
        if (!$manager) $manager = new AMPContent_Manager();
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
     * @param   object   $display   display object, must support the 'execute' method to return output
     * @param   string   $name      string key for ordering displays, a number is assigned if null 
     * @since   3.5.3
     * @access  public
     * @return  void
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
     * 1 flash,  or constant ( AMP_CONTENT_DISPLAY_KEY_FLASH )
     * 2 intro,  or constant ( AMP_CONTENT_DISPLAY_KEY_INTRO )
     * 3 buffer, or constant ( AMP_CONTENT_DISPLAY_KEY_BUFFER )
     *
     * If your script needs to support these displays, please include the constant keys in your specified order.
     * 
     * @param   array   $order      an array of display names corresponding to values added using the {@link addDisplay} method.  Unmatched values will be ignored.
     * @access  public
     * @since   3.5.3
     * @return  void 
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
     * @param   string  $name   string key for the display to be reset - if null, all displays are reset
     * @access  public
     * @since   3.5.3
     * @return  void
     */
    function resetDisplay( $name=null ) {
        if ( !isset( $name )) return ( $this->_displays_executed = array() );
        if ( !isset( $this->_displays_executed[ $name ] )) return;
        unset( $this->_displays_executed[ $name ]);
        return;
    }

    /**
     * &getDisplay 
     * 
     * @param string $name  string key for the display to be retrieved
     * @access public
     * @since  3.5.9
     * @return mixed Display Object 
     */
    function &getDisplay( $name ){
        $empty_value = false;
        if ( !isset( $this->_displays[ $name ])) return $empty_value;
        return $this->_displays[ $name ];
    }

    /**
     * add 
     * convenience alias for addDisplay
     * 
     * @param & $display 
     * @param mixed $display_key 
     * @access public
     * @since  3.5.9
     * @return void
     */
    function add( &$display, $display_key = null ){
        return $this->addDisplay( $display, $display_key );
    }

    /**
     * retrieve 
     * convenience alias for getDisplay
     * 
     * @param string $display_key 
     * @access public
     * @since  3.5.9
     * @return mixed Display Object 
     */
    function retrieve( $display_key ){
        return $this->getDisplay( $display_key );
    }

    function cache( $display_key, $cache_key = false ) {
        if ( !$cache_key ) $cache_key = $display_key;
        $this->_displays_cacheable[ $display_key ] = $cache_key;
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
     * @param   array   $order      array of string keys for displays to be executed 
     * @access  protected
     * @since   3.5.3
     * @return  string  output from executed displays
     */
    function _doDisplays( $order=array() ) {
        if ( empty( $this->_displays )) return false;
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
     * @param   object  $display    Display object to be executed
     * @param   string  $name       String key of display object 
     * @since   3.5.3
     * @access  protected
     * @return  string  Output from the display 
     */
    function _doDisplay( &$display, $name = null ) {
        if ( !isset( $name ) )  return $display->execute();
        
        if ( isset( $this->_displays_executed[ $name ]) && $this->_displays_executed[ $name ]) return false;
        $this->_displays_executed[ $name ] = true;
        
        if ( $name ) {
            $display_method = '_doDisplay' . ucfirst( $name );
            if ( method_exists( $this, $display_method )) {
                return $this->$display_method( $display );
            }

        }
        
        //cache the display output for standard displays
        if ( isset( $this->_displays_cacheable[ $name ] )){
            $output = $display->execute( );
            AMP_cache_set( $this->_displays_cacheable[ $name ], $output );
            return $output;
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
     * @param   object      $display    Display to be executed
     * @since   3.5.3
     * @access  protected
     * @return  string      Output from display 
     */
    function _doDisplayBuffer( &$display ) {
        if (!AMP_CONTENT_BUFFER_CONTAINER_ID ) return $display->execute() ;
        return $this->_renderer->inDiv( $display->execute(), array( 'id' => AMP_CONTENT_BUFFER_CONTAINER_ID ));
    }

// }}}

}
?>
