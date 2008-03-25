<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Summation_Output extends UserDataPlugin {

    
    var $options = array( 
        'fields'=>array(
            'label'=>'Fields',
            'type'=>'text',
            'available'=>'true',
            'default'=>''),
        'titles'=>array( 
            'label'=>'Titles',
            'type'=>'text',
            'available'=>'true',
            'default'=>'')
    );

    var $available = true;
    var $fields_to_sum;
    var $titles_to_sum;
    var $sum_values;
    
    function UserDataPlugin_Summation_Output (&$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance);
    }
    function execute ( $options = array( )) {
        $options = array_merge( $this->getOptions( ),$options );
        $search_plugin = $this->udm->getPlugin( 'AMP','Search');
        if ( !$search_plugin) return false;
        $sum_criteria = $search_plugin->criteria;
        $sum_criteria[] = 'TRUE GROUP by modin';
        $this->fields_to_sum = preg_split( '/\s{0,2},\s{0,2}/', $options['fields']);
        $this->titles_to_sum= preg_split( '/\s{0,2},\s{0,2}/', $options['titles']);
        foreach( $this->fields_to_sum as $sum_field  ) {
            $sum_select[] = "sum( ". $sum_field . ") as ".$sum_field."_total";
        }
        $sum_select_string = join( ', ', $sum_select ) . ', modin';
        $results = $search_plugin->return_items( $sum_select_string, $sum_criteria );
        $this->sum_values = $results->fetchRow( );
        return $this->pretty_display( $this->sum_values );
    }

    function pretty_display( $values ) {
        $renderer = AMP_get_renderer( );
        $output = '';
        $i = 0;
        
        // This loop creates a div for each Title, followed by the Sum
        foreach( $this->fields_to_sum as $current_sum ) {
            $output.= $renderer->div($this->titles_to_sum[$i],array('class'=>'sum_title' ));
            $output.= $renderer->div($values[$current_sum.'_total'],array('class'=>'sum_value' ));
            $i++;
        }
        
        // The next two lines wrap the data in two layers of container, for positioning purposes.
        $output= $renderer->div( $output,array( 'class'=>'sum_container'));
        $output= $renderer->div( $output,array( 'class'=>'sum_parent'));
        return $output ;

    }
}

?>
