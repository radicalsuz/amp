<?php

require_once( 'AMP/Display/Detail.php');

class Calendar_Public_Display extends AMP_Display_Detail {

    var $_css_class_details = 'eventsubtitle';

    function Calendar_Public_Display( &$source ) {
        $this->__construct( $source );
    }

    function renderItem ( &$source ) {
        $base_description = 
            $this->_renderer->inSpan( $source->getName( ), array( 'class' => $this->_css_class_title ))
            . $this->_renderer->newline( )
            . $this->_renderer->inSpan( 
                                    DoDate( $source->getItemDate( ), 'l, F jS Y')
                                    . ( $source->getItemDate( ) ? $this->_renderer->space( 2 ) : '')
                                    . ( $source->getData( 'time'))
                                    . $this->_renderer->newline( )
                                    . $source->getShortLocation( ),
                                    array( 'class' => $this->_css_class_details)
                                    );

        $blurb = ( $description = $source->getBody( ) ? $description : $source->getBlurb( ));
        if ( $blurb ) {
            $blurb = $this->_renderer->inSpan( $blurb, array( 'class' => $this->_css_class_text ));
        }

        $location = array( );
        $location_segments = array( 'location', 'laddress', 'lcity', 'lzip');
        foreach( $location_segments as $location_data ) {
            $location_value = $source->getData( $location_data );
            if ( !$location_value ) continue;
            $location[$location_data] = $location_value;
        }
        $location['lcity'] = $source->getShortLocation( );
        $location_description = ( isset( $location[ 'location']) ? join( $this->_renderer->newline( ), $location ) : "" );
        if ( $location_description ) {
            $location_description = $this->_renderer->bold( AMP_TEXT_LOCATION . ':' ) 
                                    . $this->_renderer->newline( ) 
                                    . $location_description;
        }

        $contact_output = '';
        $contact_name  = $source->getData( 'contact1' );
        $contact_email = $source->getData( 'email1'   );
        $contact_phone = $source->getData( 'phone1'   );
        if ( $contact_name ) {
            $contact_output .= $this->_renderer->newline( ) . $contact_name ;
        }
        if ( $contact_email ) {
            $rendered_contact_email = AMP_mailto( $contact_email );
            $contact_output .= $this->_renderer->newline( ) . $rendered_contact_email;
        }
        if ( $contact_phone ) {
            $contact_output .= $this->_renderer->newline( ) . $contact_phone;
        }
        if ( $contact_output ) {
            $contact_output =
                    $this->_renderer->bold( AMP_TEXT_CONTACT. ':' ) 
                                    . $this->_renderer->newline( ) 
                                    . $contact_output;
        }
        
        
        $sponsor_output = '';
        if ( $sponsor = $source->getData( 'org')) {
            $sponsor_output = 
                    $this->_renderer->bold( AMP_TEXT_SPONSORED. ':' ) 
                                    . $this->_renderer->newline( ) 
                                    . $sponsor;
        }
    
        $output_segments = array( 'base_description', 'blurb', 'location_description', 'contact_output', 'sponsor_output');
        foreach( $output_segments as $block_name ) {
            if ( !$$block_name ) continue;
            $output_value[ $block_name ] = $$block_name;
        }
        return join( $this->_renderer->newline( 2 ), $output_value );
    }

}


?>
