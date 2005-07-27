<?php

class AMPSystem_ComponentMap {

    function getComponents() {
        return $this->components;
    }

    function getFilePaths() {
        return $this->paths;
    }

    function getNavName() {
        return $this->nav_name;
    }

    function getHeading() {
        return $this->heading;
    }
	
	function getPath( $component_name ) {
		if (!isset( $this->paths[ $component_name ] ) ) return false;
		return $this->paths[ $component_name ];
	}
	
	function findComponent( $component_class ) {
		foreach( $this->components as $key => $component_name ) {
			if (strtolower( $component_name ) == $component_class) return $key;
		}
		return false;
	}

}

?>
