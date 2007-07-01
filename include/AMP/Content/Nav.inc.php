<?php

define( 'AMP_NAVTYPE_HTML', 'HTML' );
define( 'AMP_NAVTYPE_SQL', 'SQL' );
define( 'AMP_NAVTYPE_RSS', 'RSS' );
define( 'AMP_NAVTYPE_PHP', 'PHP' );

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Nav/Display.inc.php' );
/*
require_once ( 'AMP/Content/Nav/EngineSQL.inc.php' );
require_once ( 'AMP/Content/Nav/EngineRSS.inc.php' );
require_once ( 'AMP/Content/Nav/EngineHTML.inc.php' );
require_once ( 'AMP/Content/Nav/EnginePHP.inc.php' );
*/


class NavigationElement extends AMPSystem_Data_Item {
    var $datatable = "navtbl";
    var $template;
    var $position;

    var $totalCount;
    var $_engine;
    var $_exceedsLimit;
    var $name_field = 'name';
    var $_class_name = 'NavigationElement';

    function NavigationElement( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function init( &$dbcon, $id=null ) {
        parent::init( $dbcon, $id );
        $this->_initEngine();
    }

    function get_contents() {
        if (!isset($this->_engine)) return false;
        return $this->_engine->execute();
    }

    function execute( ) {
        return $this->output( );
    }

    function output() {
        $display = &new NavigationDisplay( $this );
        return $display->execute();
    }


    #################################
    ### navEngine related methods ###
    #################################
    
    function _initEngine() {
        if ($this->getBadgeId()) return $this->setEngine( AMP_NAVTYPE_PHP );
        if ($this->getIncludeFile()) return $this->setEngine( AMP_NAVTYPE_PHP );
        if ($this->getRSS()) return $this->setEngine( AMP_NAVTYPE_RSS );
        if ($this->getSQL()) return $this->setEngine( AMP_NAVTYPE_SQL );
        if ($this->getBaseHTML()) return $this->setEngine( AMP_NAVTYPE_HTML );
        $this->setEngine();
    }

    function setEngine( $engineType=AMP_NAVTYPE_HTML ) {
        $engine_class = 'NavEngine_' . $engineType;

        require_once ( 'AMP/Content/Nav/Engine'.$engineType.'.inc.php' );
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
        return $this->getData('sql_statement');
    }

    function getIncludeFile( ){
        return $this->getData( 'include_file');
    }

    function getBadgeId( ) {
        return $this->getData( 'badge_id' );
    }

    function getIncludeClass( ){
        return $this->getData( 'include_class');
    }

    function getIncludeFunction( ){
        return $this->getData( 'include_function');
    }

    function getBaseHTML() {
        return $this->getData( 'nosqlcode' );
    }

    function getMoreLinkPage() {
        return $this->getData('mfile');
    }

    function getLinkPage() {
        $result = $this->getData('linkfile');
        if ( $result ) return $result;
        return AMP_CONTENT_URL_ARTICLE;
    }

    function getLinkVarName() {
        if (!($name = $this->getData('mvar1'))) return 'id';
        return $name;
    }

    function getSecondLinkValue( ) {
        $result = $this->getData( 'mcall2' );
        if ( 'typeid' != $result ) return $result;
        
        $currentPage = & AMPContent_Page::instance();
        return $currentPage->getSectionId();
    }

    function getLimit() {
        if ( $limit = $this->getData( 'list_limit' )) return $limit;
        return AMP_CONTENT_NAV_LIMIT_DEFAULT;
    }

    function getCssClass() {
        if ($css =  $this->getData('linkextra') ) return $css;
        return AMP_CONTENT_CSS_CLASS_NAVLINK;
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
        $this->mergeData( array( 'list_limit' => $qty ) );
    }

    function setCount( $qty ) {
        $this->totalCount = $qty;
        return $qty;
    }

    function getToolId( ) {
        return $this->getData( 'modid');
    }

    function getToolname( ) {
        $tool_id = $this->getToolId( );
        if ( !$tool_id ) return false;
        $names = AMP_lookup( 'tools');
        if ( !isset( $names[$tool_id ])) return false;
        return $names[$tool_id];
        
    }

    function get_url_edit( ) {
        if ( !( isset( $this->id ) && $this->id )) {
            return AMP_SYSTEM_URL_NAV;
        }
        return AMP_url_update( AMP_SYSTEM_URL_NAV, array( 'id' => $this->id ));
    }


}

?>
