<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Redirect/ComponentMap.inc.php');

class AMP_Content_Redirect_Form extends AMPSystem_Form_XML {

    var $name_field = 'old';

    function AMP_Content_Redirect_Form( ) {
        $name = 'redirect';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }

    function _initJavascriptActions( ) {
        $header = &AMP_getHeader( );
        $this->_initPrettyUrlCreation( $header );
    }

    function _initPrettyUrlCreation( &$header ){
        if( !AMP_CONTENT_HUMANIZE_URLS ) return;
        $conflict_checker = <<<SCRIPT
               jq( 'form#redirect input[name=old]').change( check_route_ajax );  
               jq( '#manual_route_check').click( check_route_ajax );  
			   function check_route_ajax( ev ) {
                    var system_domain = '%s';
					var target = jq( 'form#redirect input[name=old]' );
                    jq.getJSON('/system/route_slug_ajax.php?slug_name=' + jq( target ).val(), function( result ) {
                        if ( result.conflicts !== undefined && result.conflicts.length == 0 ) {
                            jq( '#route_slug_details' ).html( "URL: " + system_domain + result.clean_url );
                        } else {
                            jq('#route_slug_details').html( "Warning: ");
                            jq.each( result.conflicts, function() {
                                jq('#route_slug_details').append( "This pretty url is already in use on <a href='" + this.owner_edit_url + "'>" + this.owner_type + " #"+ this.owner_id + "</a>" );
                            } );
                            jq('#route_slug_details').append( "<br/>Suggested Available URL: " + system_domain + result.clean_url );
                        }
                    } );
					return false;
               }
SCRIPT;
        $page_load_wrapper = <<<SCRIPT
            jq( function( ) {
                %s
            });
SCRIPT;
        $values = $this->getValues();
        $conflict_check = sprintf( $conflict_checker, AMP_SITE_URL );
        $header->addJavascriptDynamic( sprintf( $page_load_wrapper, $pretty_url_builder . $conflict_check ));
    }
}
?>
