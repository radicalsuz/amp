<?php

require_once( 'AMP/System/Component/Controller/Public.php');

class Article_Comment_Public_Controller extends AMP_System_Component_Controller_Public {
    var $_article_id;
    var $_userdata_id;

    function Article_Comment_Public_Controller( ){
        $this->init( );
        $this->_article_id = $this->assert_var( 'articleid' );
        if ( !$this->_article_id )
            $this->_article_id = $this->assert_var( 'cid' );
        $this->_userdata_id = $this->assert_var( 'userdata_id' );
    }

    function display_response( ){
        $renderer = &new AMPDisplay_HTML( );
        $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, AMP_TEXT_YOUR_COMMENT ));
        if ( $this->_article_id ) {
            AMP_lookup_clear_cached( 'comments_live_by_article', $this->_article_id );
            ampredirect( AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, array( 'id='.$this->_article_id ) ));
        }
        if ( $this->_userdata_id ) {
            require_once( 'AMP/UserData/Lookups.inc.php' );
            $form_id_lookup = &FormLookup::instance( 'modin');
            if ( isset( $form_id_lookup[ $this->_userdata_id ] )) {
                $target_modin = $form_id_lookup[ $this->_userdata_id ];
                ampredirect( AMP_Url_AddVars( AMP_CONTENT_URL_FORM_DISPLAY, array( 'uid='.$this->_userdata_id, 'modin='. $target_modin ) ));
            }
        }
    }

}

?>
