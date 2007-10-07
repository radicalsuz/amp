<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Badge/ComponentMap.inc.php');

class AMP_Content_Badge_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function AMP_Content_Badge_Form( ) {
        $name = 'badges';
        $this->init( $name, 'POST', AMP_SYSTEM_URL_BADGE );
    }

    function _after_init( ) {
        $this->addTranslation( 'navlink', 'render_nav_links', 'set');
    }

    function render_nav_links( $data, $fieldname ) {
        if ( !( isset( $data['id']) && $data['id'])) return false;
        $linked_navs = AMP_lookup( 'navs_by_badge', $data['id']);
        if ( !$linked_navs ) return false; 

        $renderer = AMP_get_renderer( );
        foreach( $linked_navs as $id => $name ) {
            $links[$id] = $renderer->link( AMP_url_update( AMP_SYSTEM_URL_NAV, array( 'id' => $id )), $name );
        }
        return 'Linked Navs:' . $renderer->UL( $links );

    }

}
?>
