<?php

require_once('AMP/Content/Header.inc.php' );
require_once('AMP/Content/Manager.inc.php');
require_once('AMP/Content/Map.inc.php');

define ('AMP_CONTENT_PAGE_DISPLAY_DEFAULT', 'standard' );
define ('AMP_CONTENT_PAGE_DISPLAY_PRINTERSAFE', 'printerSafe' );
define ('AMP_CONTENT_PAGE_DISPLAY_CONTENT', 'content' );

/**
 * Creates a page of web content 
 *
 * Holds data that is global acrosss the content system.  Locates the page within the framework of the site hierarchy. 
 * 
 * @package Content 
 * @version 3.5.4
 * @since 3.5.3
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class AMPContent_Page {

// {{{    properties      global context references 
    /**
     * Global registry 
     * 
     * @var AMP_Registry
     * @since 3.5.3
     * @access private
     */
    var $_registry;

    /**
     * References the {@link AMPContent_Template template} in use by the current page 
     * 
     * @var AMPContent_Template     
     * @since 3.5.3
     * @access public
     */
    var $template;

    /**
     * References the global {@link AMPContent_Map map} of the site hierarchy 
     * 
     * @var AMPContent_Map          
     * @since 3.5.3
     * @access public
     */
    var $map;

    /**
     * References the current database connection 
     * 
     * @var ADODB_Connection
     * @since 3.5.3
     * @access public
     */
    var $dbcon;

    /**
     * References the {@link AMPContent_Manager Content Manager} object
     * 
     * @var AMPContent_Manager
     * @since 3.5.3
     * @access public
     */
    var $contentManager;
// }}} properties:   page components
// {{{     properties      component IDs

    /**
     * Database ID of the section the page inhabits 
     * 
     * @var integer 
     * @since 3.5.3
     * @access public
     */
    var $section_id;

// }}}     properties      component IDs
// {{{     properties      content components 

    /**
     * References the currently requested {@link Article article}
     * 
     * @var Article
     * @since 3.5.3
     * @access public
     */
    var $article;

    /**
     * References the current parent {@link Section section}
     * 
     * @var Section 
     * @since 3.5.3
     * @access public
     */
    var $section;

    /**
     * References the {@link AMPSystem_IntroText IntroText} of the current page 
     * 
     * @var IntroText 
     * @since 3.5.3
     * @access public
     */
    var $introtext;

    /**
     * References the {@link ContentClass Class} of the current article or list 
     * 
     * @var ContentClass 
     * @since 3.5.3
     * @access public
     */
    var $class;

    /**
     * References the {@link AMPSystem_Region} of the current request 
     * 
     * @var AMPSystem_Region 
     * @since 3.5.3
     * @access public
     */
    var $region;

    /**
     * Refers to the current List Type
     * 
     * @var string 
     * @since 3.5.4
     * @access protected
     */
    var $_listType;

    /**
     * The raw List Type value passed to the page ;
     * 
     * @var string 
     * @since 3.5.4
     * @access private
     */
    var $_legacyListType;

    /**
     * a local object cache
     * 
     * @var array
     * @since 3.5.4
     * @access private
     */
    var $_objectCache;

// }}}     properties     content components 

// {{{     public methods      constructors
    /**
     * Class constructor 
     * 
     * @param   object  $dbcon      An active database connection 
     * @since 3.5.3
     * @access public
     * @ignore
     */
    function AMPContent_Page( &$dbcon ) {
        $this->init( $dbcon );
    }

    /**
     * Private Class Constructor Helper 
     * 
     * @param   object  $dbcon      An active database connection 
     * @since 3.5.3
     * @access protected
     * @return void
     * @ignore
     */
    function init( &$dbcon ) {
        $this->dbcon = &$dbcon;
        $this->_registry =          & AMP_Registry::instance();
        $this->map =                & AMPContent_Map::instance();
        $this->contentManager =     & AMPContent_Manager::instance();
    }

    /**
     * The recommended method for accessing and creating AMPContent_Page.

     * This method will always return the same copy of AMPContent_Page, so that your Page object
     * is the same throughout the request.  To access it, simply include code like
     * <code>$currentPage = &AMPContent_Page::instance();</code>
     * Rather than relying on other files to use the same name for the page variable, you can be sure
     * that you have the correct object and the one that will send output at the end of the request.
     * 
     * @access public
     * @since 3.5.3
     * @return void
     */
    function &instance() {
        /**
         * Local copy of the global Page controller 
         * 
         * @static
         * @var AMPContent_Page 
         */
        static $page = false;
        if (!$page) $page = new AMPContent_Page( AMP_Registry::getDbcon() );
        return $page;
    }

// }}}     methods      constructors
// {{{     public methods      output 
    /**
     * Returns the output for the current page 
     * 
     * @param   string  $display_type   Null for standard output; see {@link AMPContent_PageDisplay::execute()} for other options. 
     * @access  public
     * @since   3.5.3 
     * @return  void
     */
    function output( $display_type = null) {
        require_once('AMP/Content/Page/Display.inc.php');
        $display = &AMPContent_PageDisplay::instance( $this );
        return $display->execute( $display_type );
    }

// }}}     methods     output 

// {{{  public methods         content object assignment 

    /**
     * set the IntroText for use on the current page 
     * 
     * @param   integer     $intro_id       The database id of the IntroText to use 
     * @access  public
     * @since   3.5.3
     * @return  void
     */
    function setIntroText( $intro_id ) {
        require_once('AMP/System/IntroText.inc.php');
        if ($intro_id == AMP_CONTENT_INTRO_ID_DEFAULT ) return $this->_setNullIntro();
        $introtext = &new AMPSystem_IntroText( $this->dbcon, $intro_id );
        if (!$introtext->hasData()) return $this->_setNullItem();

        $this->_globalizeIntroVars( $introtext );
        $this->introtext = &$introtext;
        if ($template = $introtext->getTemplate()) $this->template_id = $template;
        if ($section = $introtext->getSection())  {
            $this->section_id = $section;
            $this->_globalizePageVars();
        }
    }

    /**
     * set the Article for use on the current page 
     * 
     * @param   integer     $article_id     The database id of the Article to use 
     * @access  public
     * @since   3.5.3
     * @return  void
     */
    function setArticle( $article_id ) {
        require_once('AMP/Content/Article.inc.php');
        $article= &new Article( $this->dbcon, $article_id );
        if (!$article->hasData()) ampredirect( AMP_CONTENT_URL_SEARCH );

        if ($target = $article->getRedirect() ) ampredirect($target);

        $this->section_id = $article->getParent();

        $this->_globalizeArticleVars( $article );
        $this->_globalizePageVars();
        $this->article = &$article;
    }

    /**
     * set the Section for use on the current page 
     * 
     * @param   integer     $section_id     The database id of the Section to use 
     * @access  public
     * @since   3.5.3
     * @return  void
     */
    function setSection( $section_id ) {
        require_once('AMP/Content/Section.inc.php');
        $section = &new Section($this->dbcon, $section_id);
        if (!$section->hasData() ) return false;
        if ($target = $section->getRedirect()) ampredirect( $target );
        if (!isset($this->template_id) && ( $template = $section->getTemplate())) $this->template_id = $template;

        $this->section = &$section;
        $this->section_id = $section->id;

        $this->_globalizePageVars();
        $this->_globalizeSectionVars( $section );
        return true;
    }

    /**
     * set the ContentClass for use on the current page 
     * 
     * @param   integer     $class_id       The database id of the ContentClass to use 
     * @access  public
     * @since   3.5.3
     * @return  void
     */
    function setClass( $class_id ) {
        require_once('AMP/Content/Class.inc.php');
        $contentClass = &new ContentClass( $this->dbcon, $class_id );
        if (!$contentClass->hasData()) return false;

        $this->class = &$contentClass;

        $this->section_id = $contentClass->getSection();
        $this->_globalizePageVars();
    }

    /**
     * set the Region for use on the current page 
     * 
     * @param   integer     $region_id  The database id of the AMPSystem_Region to use 
     * @access  public
     * @since   3.5.3
     * @return  void
     */
    function setRegion( $region_id ) {
        require_once('AMP/System/Region.inc.php');
        $region = & new AMPSystem_Region( $this->dbcon, $region_id );
        if (!$region->hasData()) return false;

        $this->region = &$region;

        $this->_globalizeRegionVars();
    }

    /**
     * Defines the page's response when an attempted setContent method fails to initialize the requested item 
     * 
     * @access  protected
     * @since   3.5.3
     * @return  void
     */
    function _setNullItem() {
        return false;
    }
    
// }}}  public methods         content object assignment
// {{{  public methods         content object access

    /**
     * Returns a reference to the current section if one is set 
     * 
     * @access  public
     * @since   3.5.4
     * @return  mixed   Section object if one is set, false otherwise 
     */
    function &getSection() {
        if ( !isset( $this->section )) return false;
        return $this->section;
    }

    /**
     * Returns a reference to the current article if one is set 
     * 
     * @access  public
     * @since   3.5.4
     * @return  mixed   Article object if one is set, false otherwise 
     */
    function &getArticle() {
        if ( !isset( $this->article )) return false;
        return $this->article;
    }

    /**
     * Returns a reference to the current ContentClass if one is set 
     * 
     * @access  public
     * @since   3.5.4
     * @return  mixed   ContentClass object if one is set, false otherwise 
     */
    function &getClass() {
        if ( !isset( $this->class)) return false;
        return $this->class;
    }

    /**
     * Returns a reference to the current introtext if one is set 
     * 
     * @access  public
     * @since   3.5.4
     * @return  mixed   IntroText object if one is set, false otherwise 
     */
    function &getIntroText() {
        if ( !isset( $this->introtext)) return false;
        return $this->introtext;
    }

    /**
     * Adds an object to the page's object cache by key
     * 
     * @access  public
     * @since   3.5.4
     * @return  void
     */
    function addObject($key, &$object) {
		$this->_objectCache[$key] = $object;
	}

    /**
     * Returns a reference to the object in the local cache by key
	 * or false if none exists
     * 
     * @access  public
     * @since   3.5.4
     * @return  mixed	Object by key or false
     */
    function &getObject($key) {
		if(isset($this->_objectCache[$key])) {
			return $this->_objectCache[$key];
		}
		return false;
	}

// }}}  public methods         content object accessors
// {{{  public methods         content object id access
    /**
     * Get the database ID for the current template 
     * 
     * @access public
     * @since   3.5.3
     * @return  integer     integer record ID for current template
     */
    function getTemplateId () {
        if (isset($this->template_id) && $this->template_id) return $this->template_id;
        if ($template_id = $this->map->readAncestors( $this->section_id, 'templateid' ) ) {
            $this->template_id = $template_id;
        }
        if (!isset($this->template_id)) $this->template_id = AMP_CONTENT_TEMPLATE_ID_DEFAULT;
        return $this->template_id;
    }

    /**
     * Get the database ID for the current section 
     * 
     * @access  public
     * @since   3.5.3
     * @return  integer     integer record ID for current section 
     */
    function getSectionId() {
        if (isset($this->section_id) && $this->section_id ) return $this->section_id;
        $this->section_id = AMP_CONTENT_MAP_ROOT_SECTION;
        return $this->section_id;
    }

    /**
     * Get the database ID for the current IntroText 
     * 
     * @access  public
     * @since   3.5.3
     * @return  mixed       integer id value if IntroText exists, false otherwise 
     */
    function getIntroId() {
        if (!isset($this->introtext)) return false;
        return $this->introtext->id;
    }

    /**
     * Returns the database ID of the current Article, or false if no Article is set
     * 
     * @access  public
     * @since   3.5.3
     * @return  mixed   integer id value if Article is set, false otherwise 
     */
    function getArticleId() {
        if (isset($this->article)) return $this->article->id;
        return false;
    }
    /**
     * Get the database ID for the current ContentClass 
     * 
     * @access  public
     * @since   3.5.4
     * @return  mixed       integer id value if Class is set, false otherwise 
     */
    function getClassId() {
        if (!isset($this->class)) return false;
        return $this->class->id;
    }


// }}}  public methods         content id accessors

// {{{  public methods         page identity 

    /**
     * Checks to see if a redirect has been called for the current page 
     * 
     * @access public
     * @since   3.5.3
     * @return boolean  true if the ampredirect has been called for the current page 
     */
    function isRedirected() {
        return (defined('AMP_CONTENT_PAGE_REDIRECT')? AMP_CONTENT_PAGE_REDIRECT : false );
    }

    /**
     * Returns the type of List if the current Page is a list, otherwise false.
     *
     * If the optional $list_type parameter is included in the function call, isList will compare the passed type
     * against its own and return the result
     * 
     * @access  public
     * @since   3.5.3
     * @version 3.5.4
     * @param   string      $list_type      A string value to be compared against the current listType
     * @return  mixed       Returns the type of List if the current Page is a list, true if the current list type matches the passed argument, otherwise false 
     */
    function isList( $list_type = null ) {
        if (!( isset($this->_listType) && $this->_listType)) return false;
        if (   isset( $list_type )) return ( $this->_listType == $this->getBaseListType( $list_type ));
        return $this->_listType;
    }

    /**
     * Returns true if the current page has been assigned an Article 
     * 
     * @access  public
     * @since   3.5.3
     * @return  boolean  Returns true if the current page has been assigned an Article, false otherwise;
     */
    function isArticle() {
        return (isset($this->article));
    }

    /**
     * Returns true if the current page has been assigned an IntroText 
     * 
     * @access  public
     * @since   3.5.3
     * @return  boolean  Returns true if the current page has been assigned an IntroText, false otherwise;
     */
    function isTool() {
        return (isset($this->introtext));
    }

    /**
     * Returns true if the current page has been assigned a Region 
     * 
     * @access  public
     * @since   3.5.3
     * @return  boolean  Returns true if the current page has been assigned a Region, false otherwise;
     */
    function isRegion() {
        return (isset($this->region));
    }
// }}}  public methods         page identity 
// {{{  public methods         list access

    /**
     * Returns the display object for the list if the current Page is a content List
     * 
     * @since 3.5.3
     * @access public
     * @return  mixed   if the page is a list, a subclass of {@link AMPContent_DisplayList_HTML} is returned, otherwise returns false 
     */
    function &getListDisplay() {
        if (!$listType = $this->isList()) return false;

        if ( isset( $this->$listType)  && method_exists( $this->$listType, 'getDisplay' )) {
            return  $this->$listType->getDisplay();
        }

        return false;

    }

    /**
     * Returns the AMP 3.5 + designator for a list type 
     *
     * This method is for legacy compatibility -- it handles the tricky stuff like changing type to section
     * -- feed it an old list type, get back a relevant one.  To add translations, simply create
     * a constant AMP_CONTENT_LISTTYPE_( new list type ), and define it to the ( old list type ) that needs converting.
     * 
     * @access  public
     * @since   3.5.3
     * @param   string      $listType   a listType used by the old content system 
     * @return  string      a listType used by the current content system 
     */
    function getBaseListType( $listType ) {
        /**
         * an array of listTypes defined in the current system 
         * 
         * @static
         * @var mixed
         * @access public
         */
        static $listTypeSet = false; 
        if ( !$listTypeSet ) $listTypeSet = filterConstants( 'AMP_CONTENT_LISTTYPE' );

        if ( $base_type = array_search( $listType, $listTypeSet )) return strtolower( $base_type );
        return false;
    }

    /**
     * Set the listType for the current Page 
     * 
     * @param   string  $list_type  the current
     * @access public
     * @return void
     */
    function setListType( $list_type ) {
        $this->_legacyListType = $list_type;
        if ($list_type == 'classt') $list_type = 'class';
        $this->_listType = $this->getBaseListType( $list_type );
    }

    /**
     * returns the raw value of the list type,  probably as set from the _GET array element 'list'
     * 
     * this is included for legacy support until the navs are upgraded, it is quite useless
     * 
     * @access  public
     * @since   3.5.4
     * @return  string   old nonsensical list type 
     */
    function getLegacyListType() {
        return $this->_legacyListType;
    }

// }}}  public methods         list accessors

// {{{  public methods         location: initLocation, requiresLogin

    /**
     * Sets the default section if no other location applies, checks whether user login is required to view page 
     * 
     * @access public
     * @since   3.5.4
     * @return void
     */
    function initLocation() {
        if ( !isset( $this->section ) && $this->section) $this->setSection( $this->getSectionId() );
        if ( $this->requiresLogin() ) {
            require_once( 'AMP/Auth/Handler.inc.php');
            $AMP_Authen_Handler = &new AMP_Authentication_Handler( AMP_Registry::getDbcon(), 'content' );
            if ( !$AMP_Authen_Handler->is_authenticated() ) $AMP_Authen_Handler->do_login();

        }
    }

    /**
     * Returns true if the current page requires users to login before it can be displayed
     *  
     * @access  public
     * @since   3.5.3
     * @return  boolean     true if the page requires a login, false if not
     */
    function requiresLogin() { 
        if ( $result = $this->map->readAncestors( $this->getSectionId(), 'secure' )) {
            $this->_registry->setEntry( AMP_REGISTRY_CONTENT_SECURE, $result );
            $GLOBALS['MM_secure'] = $result;
            return $result;
        }
        return false;
    }
// }}}  public methods         location confirmation methods

// {{{  private methods        legacy compatibility

    /**#@+
     * @since 3.5.3
     * @access private
     * @return void
     */

    /**
     * Sets global values for intro_id, MM_title, MM_type 
     * 
     * @param   AMPSystem_IntroText      $introtext     The IntroText whose values will be globalized
     */
    function _globalizeIntroVars( &$introtext ) {

        $GLOBALS['intro_id'] = $introtext->id;

        if ($title = $introtext->getTitle() ) {
            $GLOBALS['MM_title'] = $GLOBALS['mod_name'] = $title;
        }

        if ($section_id =  $introtext->getSection()) {
            $GLOBALS['MM_type'] = $section_id;
        }
        
    }

    /**
     * Sets global values for MM_id, MM_type, MM_class
     * 
     */
    function _globalizePageVars() {

        $GLOBALS['MM_id'] = $this->getArticleId( );
        $GLOBALS['MM_type'] = $this->getSectionId();
        $GLOBALS['MM_class'] = $this->getClassId();
    }

    /**
     * globalizeArticleVars 
     * 
     * @param  Article  $articleinfo    The article whose values are to be globalized
     */
    function _globalizeArticleVars( &$articleinfo ) {

        $GLOBALS['MM_class'] =$articleinfo->getClass();
        $GLOBALS['MM_type'] =$articleinfo->getParent();
        $GLOBALS['MM_author'] = $articleinfo->getAuthor();
        $GLOBALS['MM_title'] = $articleinfo->getTitle();
        $GLOBALS['MM_shortdesc'] = $articleinfo->getBlurb();
    }

    /**
     * Sets global values for MM_title, MM_typename, and MM_parent 
     * 
     * @param Section   $section    the Section whose values are to be globalized 
     */
    function _globalizeSectionVars( &$section ) {
        if (!isset($GLOBALS['MM_title']))  $GLOBALS['MM_title'] = $section->getName();
        $GLOBALS['MM_typename'] = $section->getName();
        $GLOBALS['MM_parent'] = $section->getParent();
    }


    /**
     * Sets a global MM_region value 
     * 
     */
    function _globalizeRegionVars() {
        $GLOBALS['MM_region'] = $this->region_id;
    }

    /**#@-*/ 
// }}}  private methods         legacy compatibility

}
?>
