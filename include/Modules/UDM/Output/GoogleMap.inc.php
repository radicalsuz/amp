<?php

class UserDataPlugin_GoogleMap_Output extends UserDataPlugin {

    var $options = array( 
        'map_lat' => array( 
                'default' => 37.043358,
                'type'    => 'text',
                'available'   => true ,
                'label'   => 'Center Latitude'),
        'map_lon',
        'map_zoom',
        'map_width',
        'map_height',
        'map_geolocation',
        'map_config_id',
        'map_zip' => array( 
            'default'   => null )
        'map_display_admin' => array( 
            'default' => false)
        );


    function UserDataPlugin_GoogleMap_Output( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
    }

    function execute( $options = null ) {
        $options = array_merge( $this->getOptions( ), $options );
        if ( !$options['map_display_admin'] && $this->admin) return false;

        require_once( 'AMP/Geo/Map.php');

        if ( isset( $options['map_zip'])) return $this->_display_zip( $options ) ;
        if ( isset( $options['map_geolocation'])) return $this->_display_geolocation( $options ) ;

        return $this->_display_basic( $options );
    }

    function _display_basic( $options ){
        $map_display = &new Maps( $this->dbcon, $options['map_config_id'] );
        return $map_display->google_map( $options['map_height'], $options['map_width'], $options['map_zoom'], $options['map_lat'], $options['map_lon']) ;

    }

    function _display_zip( $options ){
		$geo = new Geo($dbcon);
		$geo->Zip = $options['map_zip'];
		$geo->zip_lookup();
		$options['map_lat'] =$geo->lat;
		$options['map_lon'] =$geo->long;
        return $this->_display_basic( $options );
    }

    function _display_geolocation( $options ){

    }
}
?>
