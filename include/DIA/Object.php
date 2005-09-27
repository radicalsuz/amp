<?php

require_once('DIA/API.php');
require_once('XML/Unserializer.php');

class DIA_Object {

	var $_interface;

	var $_key;
	var $_table = 'supporter';
	var $_properties;

	function DIA_Object($key = null, $properties = null, $interface = null) {
		$this->init($key, $properties, $interface);
	}

	function init($key = null, $properties = null, $interface = null) {
		$this->_key = $key;
		$this->set($properties);
		if(!isset($interface)) {
			$interface = DIA_API::getDefaultAPI();
		}
		$this->interface($interface);
	}

	//factory method
	function &create($type = null, $key = null, $properties = null, $interface = null) {
		if(!isset($type)) $type = 'Object';
		$type = ucfirst($type);
		if(include_once($type.'.php')) {
			$classname = 'DIA_'.$type;
			return new $classname($key, $properties, $interface);
		}
	}

	function set($properties) {
		if(!(isset($properties) && is_array($properties))) return;

		foreach($properties as $name => $value) {
			$this->_properties[$name] = $value;
		}
	}

	function &get($properties = null) {
		if(!isset($properties)) return $this->_properties;

		if(is_array($properties)) {
			foreach($properties as $name) {
				$return[$name] = $this->_properties[$name];
			}
			return $return;
		}

		return $this->_properties[$property];
	}
		
	function read() {
		$api =& $this->interface();
		$xml = $api->get($this->getTable(), array('key' => $this->getKey()));
		$xmlparser =& new XML_Unserializer();
		$status = $xmlparser->unserialize($xml);
		$data = $xmlparser->getUnserializedData();
//data = array(supporter => array( item => array(data), count => 1));
		$this->_properties = $data[$this->getTable()]['item'];
	}

	function save() {
		$api =& $this->interface();
		return $api->saveObject($this);
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
