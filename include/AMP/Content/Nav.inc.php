<?php

if (!defined('AMP_NAVLINK_ALTERNATE_CSS_CLASS')) define( 'AMP_NAVLINK_ALTERNATE_CSS_CLASS', 'sidelist2' );
if (!defined( 'AMP_NAVLINK_CSS_CLASS' )) define ('AMP_NAVLINK_CSS_CLASS', 'sidelist' );

define( 'AMP_NAVTYPE_HTML', 'HTML' );
define( 'AMP_NAVTYPE_SQL', 'SQL' );
define( 'AMP_NAVTYPE_RSS', 'RSS' );

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Nav/Display.inc.php' );
require_once ( 'AMP/Content/Nav/EngineSQL.inc.php' );
require_once ( 'AMP/Content/Nav/EngineRSS.inc.php' );
require_once ( 'AMP/Content/Nav/EngineHTML.inc.php' );


class NavigationElement extends AMPSystem_Data_Item {
    var $datatable = "navtbl";
    var $template;
    var $position;

    var $totalCount;
    var $_engine;
    var $_exceedsLimit;

    function NavigationElement( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function init( &$dbcon, $id=null ) {
        PARENT::init( $dbcon, $id );
        $this->_initEngine();
    }

    function execute() {
        if (!isset($this->_engine)) return false;
        return $this->_engine->execute();
    }

    function output() {
        $display = &new NavigationDisplay( $this );
        return $display->execute();
    }


    #################################
    ### navEngine related methods ###
    #################################
    
    function _initEngine() {
        if ($this->getRSS()) return $this->setEngine( AMP_NAVTYPE_RSS );
        if ($this->getSQL()) return $this->setEngine( AMP_NAVTYPE_SQL );
        if ($this->getBaseHTML()) return $this->setEngine( AMP_NAVTYPE_HTML );
        $this->setEngine();
    }

    function setEngine( $engineType=AMP_NAVTYPE_HTML ) {
        $engine_class = 'NavEngine_' . $engineType;
        if (!class_exists( $engine_class )) return false;
        $this->_engine = &new $engine_class( $this );
    }

    function getEngineType() {
        return $this->_engine->getEngineType();
    }
    

    #################################
    ### public templating methods ###
    #################################
   
    function initTemplate( $position, &$template ) {
        if (!($template_id=$this->getTemplate())) {
            return $this->setTemplateRef( $template, $position );
        }
        $local_template = &new AMPContent_Template( $this->dbcon, $template_id );
        return $this->setTemplateRef( $local_template, $position );
    }

    function setTemplateRef( &$template, $position="l" ) {
        $this->template = &$template;
        $this->position = $position;
    }

    function getTemplate() {
        return $this->getData( 'templateid' );
    }

    #############################
    ### public data accessors ###
    #############################

    function getTitle() {
        $title = $this->getData( 'titletext' );
        if (!isset($this->_engine)) return $title;
        if (!method_exists( $this->_engine, 'processTitle' )) return $title;

        return $this->_engine->processTitle( $title );
    }

    function getMoreLink() {
        if (isset($this->_engine) && method_exists($this->_engine, 'processMoreLink')) {
            return $this->_engine->processMoreLink();
        }
        return $this->getMoreLinkPage();
    }

    function getRSS() {
        return $this->getData('rss');
    }

    function getSQL() {
        return $this->getData('sql');
    }

    function getBaseHTML() {
        return $this->getData( 'nosqlcode' );
    }

    function getMoreLinkPage() {
        return $this->getData('mfile');
    }

    function getLinkPage() {
        return $this->getData('linkfile');
    }

    function getLinkVarName() {
        if (!($name = $this->getData('mvar1'))) return 'id';
        return $name;
    }

    function getLimit() {
        return $this->getData( 'repeat' );
    }

    function getCssClass() {
        if ($css =  $this->getData('linkextra') ) return $css;
        return AMP_NAVLINK_CSS_CLASS;
    }

    function getLinkTextField() {
        if ($fieldname = $this->getData( 'linkfield' )) return $fieldname;
        return 'title';
    }

    function getTitleImage() {
        if (!$this->getData( 'titleti' )) return false;
        return $this->getData( 'titleimg' );
    }

    ###############################
    ### result counting methods ###
    ###############################

    function getTotalCount() {
        if (!(isset($this->totalCount)&&$this->totalCount)) return false;
        return $this->totalCount;
    }

    function exceedsLimit() {
        if (isset($this->_exceedsLimit )) return $this->_exceedsLimit;
        
        if (!(($limit = $this->getLimit()) && ($total = $this->getTotalCount()))) {
            $this->_exceedsLimit = false;
            return $this->_exceedsLimit;
        }
        $this->_exceedsLimit = ( $total > $limit ); 
        return $this->_exceedsLimit;
    }

    function setLimit( $qty ) {
        $this->mergeData( array( 'repeat' => $qty ) );
    }

    function setCount( $qty ) {
        $this->totalCount = $qty;
        return $qty;
    }
}
?>    
