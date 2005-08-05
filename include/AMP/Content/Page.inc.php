<?php

require_once('AMP/Content/Article.inc.php');
require_once('AMP/Content/Section.inc.php');
require_once('AMP/System/IntroText.inc.php');
require_once('AMP/Content/Template.inc.php' );
require_once('AMP/Content/Header.inc.php' );
require_once('AMP/Content/Page/Display.inc.php');
require_once('AMP/Content/Manager.inc.php');

class AMPContent_Page {
    //page components
    var $registry;
    var $template;
    var $map;
    var $header;
    var $contentManager;

    //current content ids
    var $article_id;
    var $intro_id;
    var $class_id;
    var $section_id;

    //content types
    var $article;
    var $section;
    var $introtext;
    var $class;

    var $region_id;

    //can't do nothing without dbcon
    var $dbcon;

    function AMPContent_Page( &$dbcon ) {
        $this->init( $dbcon );
    }

    function init( &$dbcon ) {
        $this->dbcon = &$dbcon;
        
        $this->registry =           & AMP_Registry::instance();
        $this->map =                & AMPContent_Map::instance();
        $this->header =         & new AMPContent_Header( $this );
        $this->contentManager = & new AMPContent_Manager();
    }

    function &instance() {
        static $page = false;
        if (!$page) $page = new AMPContent_Page( AMP_Registry::getDbcon() );
        return $page;
    }

    function output( $display_type = null) {
        $display = &new AMPContent_PageDisplay( $this );
        return $display->execute( $display_type );
    }

    function requiresLogin() { 
        if ( $result = $this->map->readAncestors( $this->section_id, 'secure' )) {
            $this->registry->setEntry( AMP_REGISTRY_CONTENT_SECURE, $result );
            $GLOBALS['MM_secure'] = $result;
        }
        return false;
    }

    ################################################
    ### public content object assignment methods ###
    ################################################


    function setIntroText( $intro_id ) {
        if ($intro_id == AMP_CONTENT_INTRO_ID_DEFAULT ) return $this->_setNullIntro();
        $introtext = &new AMPSystem_IntroText( $this->dbcon, $intro_id );
        if (!$introtext->hasData()) return $this->_setNullItem();

        $this->globalizeIntroVars( $introtext );
        $this->introtext = &$introtext;
        if ($template = $introtext->getTemplate()) $this->template_id = $template;
        if ($section = $introtext->getSection())  {
            $this->section_id = $template;
            $this->globalizePageVars();
        }
    }

    function setArticle( $article_id ) {
        $article= &new Article( $this->dbcon, $article_id );
        if (!$article->hasData()) ampredirect( AMP_CONTENT_URL_SEARCH );

        if ($target = $articleinfo->getRedirect() ) ampredirect($target);
        $this->section_id = $article->getParent();
        $this->class_id = $article->getClass();
        $this->globalizeArticleVars( $article );
        $this->globalizePageVars();
        $this->article = &$article;
    }

    function setTemplate( $template_id ) {
        $template = & new AMPContent_Template( $this->dbcon, $template_id );
        $template->setPage( $this );
        if (!$template->hasData()) return false;

        $this->template = &$template;
        $this->globalizeTemplateVars( $this->template );
    }

    function setSection( $section_id ) {
        $section = &new Section($this->dbcon, $section_id);
        if (!$section->hasData() ) return false;
        if ($target = $section->getRedirect()) ampredirect( $target );
        if ($template = $section->getTemplate()) $this->template_id = $template;

        $this->section = &$section;
        $this->section_id = $section->id;

        $this->globalizePageVars();
        $this->globalizeSectionVars( $section );
        return true;
    }

    function setClass( $class_id ) {
        $this->class_id = $class_id;
        $this->class = &new ContentClass( $this->dbcon, $class_id );
        $this->globalizePageVars();
    }

    function setRegion( $region_id ) {
        $this->region_id = $region_id ;
        $this->globalizeRegionVars();
    }

    function _setNullItem() {
        return false;
    }

    ###########################################
    ### public make-sure assignment methods ###
    ###########################################

    function initTemplate() {
        $this->setTemplate( $this->getTemplateId());
        $this->_initStyleSheets();
    }

    function initSection() {
        if (isset($this->section) && $this->section) return true;
        return $this->setSection( $this->getSectionId() );
    }

    function getDefaultTemplate() {
        return $this->registry->getEntry( AMP_REGISTRY_CONTENT_TEMPLATE_ID_DEFAULT );
    }

    function getTemplateId () {
        if (isset($this->template_id) && $this->template_id) return $this->template_id;
        if ($this->section_id == AMP_CONTENT_MAP_ROOT_SECTION) {
            $this->template_id = $this->getDefaultTemplate();
            return $this->template_id;
        }
        $this->template_id = $this->registry->getEntry( AMP_REGISTRY_CONTENT_TEMPLATE_ID_DEFAULT );
        return $this->template_id;
    }

    function getSectionId() {
        if (isset($this->section_id) && $this->section_id ) return $this->section_id;
        $this->section_id = AMP_CONTENT_MAP_ROOT_SECTION;
        return $this->section_id;
    }

    function getIntroId() {
        if (!isset($this->introtext)) return false;
        return $this->introtext->id;
    }


    ##############################
    ###  StyleSheet accessors  ###
    ##############################


    function addStyleSheets( $css ) {
        $stylesheet_array = array( $css );
        if (strpos($css, ",")!==FALSE) $stylesheet_array = split( "[ ]?,[ ]?", $css );
        foreach ($stylesheet_array as $sheet_url ) {
            $this->header->addStyleSheet( $sheet_url );
        }
    }

    function _initStyleSheets() {
        if (!($css = $this->map->readAncestors( $this->section_id, 'css' ))) {
            $css = $this->template->getCSS();
        }
        $this->addStyleSheets( $css );
    }

    #####################################################
    ### public legacy content html assignment methods ###
    #####################################################

    function setContent( $html ) {
        $this->contentManager->setBody( $html );
    }

    function addtoContent( $html ) {
        $this->contentManager->appendBody( $html );
    }

    function addtoContentFooter( $html ) {
        $this->contentManager->appendFooter( $html );
    }

    ############################
    ###  PAGETYPE accessors  ###
    ############################

    function isList() {
        if (!isset($this->listType)) return false;
        return $this->listType;
    }

    function isArticle() {
        if (isset($this->article)) return true;
    }

    function isTool() {
        if (isset($this->introtext)) return true;
    }

    function setListType( $list_type ) {
        $this->listType = $list_type;
    }

    ############################################
    ### private legacy compatibility methods ###
    ############################################

    function globalizeIntroVars( &$introtext ) {
        $GLOBALS['intro_id'] = $introtext->id;

        if ($title = $introtext->getTitle() ) {
            $GLOBALS['MM_title'] = $GLOBALS['mod_name'] = $title;
            $this->registry->setEntry( AMP_REGISTRY_CONTENT_PAGE_TITLE, $title );
        }

        if ($section_id =  $introtext->getSection()) {
            $GLOBALS['MM_type'] = $section_id;
        }
        
    }

    function globalizePageVars() {
        $this->registry->setEntry( AMP_REGISTRY_CONTENT_SECTION_ID, $this->section_id );
        $this->registry->setEntry( AMP_REGISTRY_CONTENT_CLASS_ID, $this->class_id );

        $GLOBALS['MM_id'] = $this->article_id;
        $GLOBALS['MM_type'] = $this->section_id;
        $GLOBALS['MM_class'] = $this->class_id;
    }

    function globalizeArticleVars( &$articleinfo ) {
        $this->registry->setArticle( $articleinfo );
        $this->registry->setEntry( AMP_REGISTRY_CONTENT_PAGE_TITLE, $articleinfo->getTitle() );

        $GLOBALS['MM_class'] =$articleinfo->getClass();
        $GLOBALS['MM_type'] =$articleinfo->getParent();
        $GLOBALS['MM_author'] = $articleinfo->getAuthor();
        $GLOBALS['MM_title'] = $articleinfo->getTitle();
        $GLOBALS['MM_shortdesc'] = $articleinfo->getBlurb();
    }

    function globalizeSectionVars( &$section ) {
        $this->registry->setSection( $section );
        if ((!$this->registry->getEntry( AMP_REGISTRY_CONTENT_PAGE_TITLE )) && ($this->section_id != AMP_CONTENT_MAP_ROOT_SECTION) ) {
            $this->registry->setEntry( AMP_REGISTRY_CONTENT_PAGE_TITLE , $section->getName() );
            $GLOBALS['MM_title'] = $section->getName();
        }

        $GLOBALS['MM_typename'] = $section->getName();
        $GLOBALS['MM_parent'] = $section->getParent();
    }

    function globalizeTemplateVars( &$template ) {
        $this->registry->setTemplate( $template );
        $template->globalizeNavLayout();

        $GLOBALS['NAV_IMG_PATH'] = $template->getNavImagePath();
        $GLOBALS['NAV_REPEAT'] = $template->getNavRepeat();
        $GLOBALS['htmltemplate'] = $template->getHtmlTemplate();
    }

    function globablizeRegionVars() {
        $this->registry->setEntry( 'AMP_CONTENT_LIST_REGION', $this->region_id );
        $GLOBALS['MM_region'] = $this->region_id;
    }

}
?>