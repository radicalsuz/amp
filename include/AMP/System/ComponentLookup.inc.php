<?php

class ComponentLookup {

	function &instance( $classname ) {
		static $super_map = false;
		if (!$super_map) $super_map = new ComponentMapSet();
		return $super_map->findOwner( $classname );
	}
}

class ComponentMapSet {
	var $mapset;

	function indexMaps() {
		$this->mapset = array();
		$class_set = get_declared_classes();
		foreach( $class_set as $name ) {
            if (strtolower( $name ) == 'componentmapset') continue;
			if (strpos( strtolower($name), 'componentmap') !== 0) continue;
			$this->mapset[] = $name;
		}
	}

	function &findOwner( $classname ) {
		$this->indexMaps();
		foreach ($this->mapset as $mapname) {
			$map = &new $mapname();
			if (!method_exists( $map, 'findComponent' )) continue;
			if ( $map->findComponent( $classname)  !== false ) return $map;
		}
		return false;
	}
}
?>
