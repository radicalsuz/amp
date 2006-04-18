<?php
require_once ('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Index_Output extends UserDataPlugin {
    var $available=true;
    var $description= "Index by State";
    var $options = array( 
        'index_field' => array( 
            'type'      =>  'text',
            'default'   =>  'State',
            'available' =>  'true',
            'label'     =>  'Index by Field'),
        'index_name' => array( 
            'type'      =>  'text',
            'default'   =>  'State',
            'available' =>  'true',
            'label'     =>  'Index Name')
        );

    var $_region_lookups = array( 
        'State' => array( 'instance' => 'Regions_US_and_Canada'),
        'Country' => array( 'instance' => 'Regions_World'));

    function UserDataPlugin_Index_Output (&$udm, $plugin_instance=null) {   
        $this->init($udm, $instance);
    }

    function execute ($options=null) {
        $options = array_merge( $this->getOptions( ), $options);

        require_once( 'AMP/UserData/Lookups.inc.php');
		$index_title = AMP_Pluralize( $this->udm->name )." By ".$options['index_name'];
        $index_set = &FormLookup_Variant::instance( $options['index_field'], $this->udm->instance );

		#$index['state']['sql'].="SELECT count(userdata.id) as qty, userdata.State as item_key, states.statename as item_name from userdata, states WHERE userdata.State=states.state and modin=".$_REQUEST['modin']." GROUP BY userdata.State ";
        $translated_values = isset( $this->_region_lookups[$options['index_field']]) ?
                                AMPSystem_Lookup::locate( $this->_region_lookups[$options['index_field'] ]) :
                                AMPSystem_Lookup::locate( array( 'instance' => AMP_Pluralize( $options['index_field'])));

        require_once( 'AMP/Content/Display/HTML.inc.php');
        $renderer = &new AMPDisplay_HTML;
        $output = $renderer->bold( $index_title )
                    . $renderer->newline( );
        foreach ($index_set as $index_value => $index_count) {
            $display_value = 
                ( $translated_values && isset( $translated_values[$index_value] )) 
                    ? $translated_values[ $index_value ] 
                    : $index_value;
            $display_value .= ' (' . $index_count . ')';

            $link_value = AMP_URL_AddVars( $_SERVER[ 'PHP_SELF' ], array( $options['index_field'] => ( strtolower( $options['index_field'] ) . '=' . $index_value ), 'modin' => ( 'modin=' . $this->udm->instance ) ));
            $output .= $renderer->link( $link_value, $display_value )
                        . $renderer->newline( );
            #$output .= '<a href="'.$_SERVER['PHP_SELF'].'?'.$options['index_field'].'='.$index_value.'&modin='.$_REQUEST['modin'].'">'.$index_item['item_name'].'</a> ('.$index_item['qty'].')<BR>';
        }
                    
		return $output;
    }
        
    
    
	
    
}    
    
?>
