<?php

class ComponentLookup {

	function &instance( $classname ) {
		$super_map = &ComponentMapSet::instance();
		return $super_map->findOwner( $classname );
	}

    function store( &$map ) {
		$super_map = &ComponentMapSet::instance();
        $super_map->add( $map );

    }

    function available( ){
        return false;
    }
}

class ComponentMapSet {
	var $mapset;
    var $live_maps = array( );

	function indexMaps() {
		$this->mapset = array();
        $all_classes = array_map( 'strtolower', get_declared_classes( ));
        $this->mapset = array_filter( $all_classes, array( $this, 'is_map' ));
        /*
		$class_set = get_declared_classes();
		foreach( $class_set as $name ) {
            if (strtolower( $name ) == 'componentmapset') continue;
			if (strpos( strtolower($name), 'componentmap') !== 0) continue;
			$this->mapset[] = $name;
		}
        */
	}

    function &instance( ) {
        static $map_of_maps = false;
        if ( $map_of_maps ) return $map_of_maps;
        $map_of_maps = new ComponentMapSet( );
        return $map_of_maps;
    }

    function add( &$map ) {
        $this->live_maps[ strtolower( get_class( $map ))] = &$map;
    }

    function is_map( $classname ) {
        if ($classname  == 'componentmapset') return false;
        return (strpos( $classname, 'componentmap') === 0);
    }

	function &findOwner( $classname ) {
        $empty_value = false;
        foreach( $this->live_maps as $map_key => $map_copy ) {
            if( $map_copy->findComponent( $classname )) return $this->live_maps[$map_key];
        }

		$this->indexMaps();
		foreach ($this->mapset as $mapname) {
			$map = &new $mapname();
			if (!method_exists( $map, 'findComponent' )) continue;
			if ( $map->findComponent( $classname)) return $map;
		}
		return $empty_value;
	}
}
?>
