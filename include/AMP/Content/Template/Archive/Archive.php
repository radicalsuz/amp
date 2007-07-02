<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_Content_Template_Archive extends AMPSystem_Data_Item {

    var $datatable = "template_archives";
    var $name_field = "name";
    var $id_field = 'archive_id';

    function AMP_Content_Template_Archive ( &$dbcon, $id = null ) {
        $this->__construct( $dbcon, $id );
    }

    function get_url_edit( ) {
        return $this->get_system_url( 'TEMPLATE_ARCHIVE' );
    }

    function restore( ) {
        $target_id = $this->getData( 'id');
        if ( !$target_id ) return false;

        require_once( 'AMP/Content/Template.inc.php');
        $template = &new AMPContent_Template( $this->dbcon, $target_id );
        $template->save_version( );

        $template->mergeData( $this->getData( ));
        $result = $template->save( );
        if ( !$result ) return false;

        AMP_flush_common_cache( );
        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( sprintf( AMP_TEXT_DATA_RESTORE_SUCCESS, $this->getName( ) ));

        ampredirect( AMP_url_update( AMP_SYSTEM_URL_TEMPLATE, array( 'id' => $target_id ) ));

        return $result;
    }

    function _sort_default( &$item_set ) {
        $this->sort( $item_set, 'timestamp', AMP_SORT_DESC );
    }

    function getTimestamp( ) {
        return $this->getData( 'archived_at');
    }

    function getTemplate( ) {
        $id = $this->getData( 'id');
        $template_set = AMP_lookup( 'templates');
        if ( !isset( $template_set[$id])) return $id;
        return $id . ': ' . $template_set[$id];
    }
}

?>
