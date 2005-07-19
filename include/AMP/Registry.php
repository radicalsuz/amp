<?php

define('AMP_REGISTRY_TEMPLATE','TEMPLATE');
define('AMP_REGISTRY_DBCON','DBCON');
define('AMP_REGISTRY_SETTING_ENCODING','SETTING_ENCODING');
define('AMP_REGISTRY_SETTING_SITENAME','SETTING_SITENAME');
define('AMP_REGISTRY_SETTING_SITEURL','SETTING_SITEURL');
define('AMP_REGISTRY_SETTING_EMAIL_SYSADMIN','SETTING_EMAIL_SYSADMIN');

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
		$registry =& $this->instance();
		return $registry->getEntry(AMP_REGISTRY_DBCON);
	}
}
?>
