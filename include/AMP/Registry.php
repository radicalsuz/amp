<?php

define('AMP_REGISTRY_TEMPLATE','TEMPLATE');
define('AMP_REGISTRY_DBCON','DBCON');
define('AMP_REGISTRY_SETTING_ENCODING','SETTING_ENCODING');
define('AMP_REGISTRY_SETTING_SITENAME','SETTING_SITENAME');
define('AMP_REGISTRY_SETTING_SITEURL','SETTING_SITEURL');
define('AMP_REGISTRY_SETTING_EMAIL_SYSADMIN','SETTING_EMAIL_SYSADMIN');
define('AMP_REGISTRY_SETTING_EMAIL_SENDER','SETTING_EMAIL_SENDER');
define('AMP_REGISTRY_SETTING_METADESCRIPTION', 'SETTING_METADESCRIPTION' );
define('AMP_REGISTRY_SETTING_METACONTENT', 'SETTING_METACONTENT' );


define('AMP_REGISTRY_CONTENT_INTRO_ID', 'CONTENT_INTRO_ID' );
define('AMP_REGISTRY_CONTENT_ARTICLE_ID', 'MM_ID' );
define('AMP_REGISTRY_CONTENT_ARTICLE', 'ARTICLE' );
define('AMP_REGISTRY_CONTENT_PAGE_TITLE', 'MM_TITLE' );
define('AMP_REGISTRY_CONTENT_SECTION_ID', 'MM_TYPE' );
define('AMP_REGISTRY_CONTENT_SECTION', 'CURRENT_SECTION' );
define('AMP_REGISTRY_CONTENT_CLASS_ID', 'MM_CLASS' );
define('AMP_REGISTRY_CONTENT_TEMPLATE_ID_DEFAULT' , 'TEMPLATE_ID_DEFAULT' );

//based on the Registry pattern described at
//http://www.phppatterns.com/index.php/article/articleview/75/1/1/
class AMP_Registry {
    var $_cache_stack;
    
    function AMP_Registry() {
        $this->_cache_stack = array(array());
    }
    function setEntry($key, &$item) {
        $this->_cache_stack[0][$key] = &$item;
    }
    function &getEntry($key) {
        if (!isset($this->_cache_stack[0][$key])) return false;
        return $this->_cache_stack[0][$key];
    }
    function isEntry($key) {
        return ($this->getEntry($key) !== null);
    }
    function &instance() {
        static $registry = false;
        if (!$registry) {
            $registry = new AMP_Registry();
        }
        return $registry;
    }

	//save and restore are for testing purposes
    function save() {
        array_unshift($this->_cache_stack, array());
        if (!count($this->_cache_stack)) {
            trigger_error('Registry lost');
        }
    }
    function restore() {
        array_shift($this->_cache_stack);
    }

	//convenient accessor methods
	function setTemplate(&$template) {
		$registry =& $this->instance();
		return $this->setEntry(AMP_REGISTRY_TEMPLATE, $template);
	}

	function &getTemplate() {
		$registry =& $this->instance();
		return $registry->getEntry(AMP_REGISTRY_TEMPLATE);
	}

	function setDbcon(&$dbcon) {
		$registry =& $this->instance();
		return $this->setEntry(AMP_REGISTRY_DBCON, $dbcon);
	}

	function &getDbcon() {
        static $dbcon = false;
        if (!$dbcon) {
            $registry = &AMP_Registry::instance();
            $dbcon = &$registry->getEntry(AMP_REGISTRY_DBCON);
        }
        return $dbcon;
	}

	function setArticle(&$article) {
		$this->setEntry(AMP_REGISTRY_CONTENT_ARTICLE, $article);
		$this->setEntry(AMP_REGISTRY_CONTENT_ARTICLE_ID, $article->id );
        /*
		$this->setEntry(AMP_REGISTRY_CONTENT_PAGE_TITLE, $article->getName() );
		$this->setEntry(AMP_REGISTRY_CONTENT_SECTION_ID, $article->getParent() );
		$this->setEntry(AMP_REGISTRY_CONTENT_SECTION_ID, $article->getClass() );
        */
	}

	function &getArticle() {
		return $this->getEntry(AMP_REGISTRY_CONTENT_ARTICLE);
	}
	function setSection(&$section) {
		$this->setEntry(AMP_REGISTRY_CONTENT_SECTION, $section);
	}

	function &getSection() {
        return $this->getEntry( AMP_REGISTRY_CONTENT_SECTION );
    }
}
?>
