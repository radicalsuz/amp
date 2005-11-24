<?php

if(!defined('DIR_SEP')) {
	define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if(!defined('DIA_DIR')) {
	define('DIA_DIR', dirname(__FILE__) . DIR_SEP);
}

if(!defined('DIA_OBJECT_DIR')) define('DIA_OBJECT_DIR', DIA_DIR.'Object'.DIR_SEP);

require_once('DIA/API.php');
require_once('XML/Unserializer.php');

class DIA_Object {

	var $_api;

	var $_key;
	var $_table;
	var $_properties;

	function DIA_Object($key = null, $properties = null, $interface = null) {
		$this->init($key, $properties, $interface);
	}

	function init($key = null, $properties = null, $interface = null) {
		if( !defined('DIA_API_DEBUG') ) {
			define('DIA_API_DEBUG', false);
		}

		$this->_key = $key;
		$this->set($properties);
		if(!isset($interface)) {
			$interface = DIA_API::create();
		}
		$this->interface($interface);
	}

	//factory method
	function &create($type = null, $key = null, $properties = null, $interface = null) {
		if(!isset($type)) $type = 'Object';
		$type = ucfirst($type);
		if(include_once(DIA_OBJECT_DIR.$type.'.php')) {
			$classname = 'DIA_'.$type;
			return new $classname($key, $properties, $interface);
		}
	}

	function setProperty($name, $value) {
		$this->_properties[$name] = $value;
	}

	function getProperty($name) {
		if(isset($this->_properties[$name])) {
			return $this->_properties[$name];
		} else {
			return null;
		}
	}

	function set($properties, $value = null) {
		if(!isset($properties)) return;

		if(!is_array($properties)) {
			if(isset($value)) {
				$properties = array($properties => $value);
			} else {
				$this->error('set called with no value');
				return false;
			}
		}

		foreach($properties as $name => $value) {
			$this->setProperty($name, $value);
		}
	}

	function &get($properties = null) {
		if(!isset($properties)) return $this->_properties;

		if(is_array($properties)) {
			foreach($properties as $name) {
				$return[$name] = $this->getProperty($name);
			}
			return $return;
		}

		return $this->getProperty($property);
	}

	function cacheGet() {
	}
		
	function read() {
		$api =& $this->api();
		$xml = $api->get($this->getTable(), array('key' => $this->getKey()));
		$xmlparser =& new XML_Unserializer();
		$status = $xmlparser->unserialize($xml);
		$data = $xmlparser->getUnserializedData();
//data = array(supporter => array( item => array(data), count => 1));
		$this->_properties = $data[$this->getTable()]['item'];
	}

	function save() {
		$api =& $this->_api;
		return $api->process($this);
	}

	function &interface($api = null) {
		if(isset($api)) {
			$this->_interface =& DIA_API::create($api);
		}
		return $this->_interface;
	}

	function getTable() {
		return $this->_table;
	}

	function getKey() {
		return $this->_key;
	}
}

?>
