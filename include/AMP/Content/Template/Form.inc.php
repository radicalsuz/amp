<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Template/ComponentMap.inc.php');

class AMP_Content_Template_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function AMP_Content_Template_Form( ) {
        $name = 'template';
        $this->init( $name );
    }

    function adjustFields( $fields ) {
        $blocks = filterConstants( 'AMP_CONTENT_NAV_BLOCK');
        $tokens = array( AMP_CONTENT_TEMPLATE_TOKEN_BODY );
        $renderer = AMP_get_renderer( );

        foreach( $blocks as $block_name => $db_token ) {
            $tokens[] = sprintf( AMP_CONTENT_TEMPLATE_TOKEN_STANDARD, strtolower( $block_name ));
        }
        $fields['template_header2']['default'] = AMP_TEXT_TEMPLATE_ADD_TOKENS . $renderer->newline( ) . join( '&nbsp; ', $tokens );
        return $fields;
    }

}
?>
