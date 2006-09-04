<?php

define('AMP_REGISTRY_TEMPLATE','TEMPLATE');
define('AMP_REGISTRY_DBCON','DBCON');
define('AMP_REGISTRY_MEMCACHE_CONNECTION','MEMCACHE_CONNECTION');

define('AMP_REGISTRY_CONTENT_INTRO_ID', 'CONTENT_INTRO_ID' );
define('AMP_REGISTRY_CONTENT_ARTICLE_ID', 'MM_ID' );
define('AMP_REGISTRY_CONTENT_SECURE', 'CONTENT_SECURE' );
define('AMP_REGISTRY_CONTENT_ARTICLE', 'ARTICLE' );
define('AMP_REGISTRY_CONTENT_PAGE_TITLE', 'MM_TITLE' );
define('AMP_REGISTRY_CONTENT_SECTION_ID', 'MM_TYPE' );
define('AMP_REGISTRY_CONTENT_SECTION', 'CURRENT_SECTION' );
define('AMP_REGISTRY_CONTENT_CLASS_ID', 'MM_CLASS' );
define('AMP_REGISTRY_SYSTEM_DATASOURCE_DEFS', 'SYSTEM_DATASOURCE_DEFS' );
define('AMP_REGISTRY_MIMETYPE_CACHE', 'MIMETYPE_CACHE' );
define('AMP_REGISTRY_CONTENT_IMAGE_THUMB_ATTRIBUTES', 'CONTENT_IMAGE_THUMB_ATTRIBUTES' );


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
        $empty_value = false;
        if (!isset($this->_cache_stack[0][$key])) return $empty_value;
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

    function adminEmail( ) {
        return AMP_SYSTEM_BLAST_EMAIL_SENDER;
    }

    function adminName( ) {
        return AMP_SYSTEM_BLAST_EMAIL_SENDER_NAME;
    }

    function __sleep( ){
        $this->setEntry( AMP_REGISTRY_DBCON, false );
    }

    function __wakeup( ){
        global $dbcon;
        $this->setEntry( AMP_REGISTRY_DBCON, $dbcon );
    }
}
?>
