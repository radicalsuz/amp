<?php

require_once( 'AMP/System/Component/Controller/Public.php');

class Article_Comment_Public_Controller extends AMP_System_Component_Controller_Public {
    var $_article_id;

    function Article_Comment_Public_Controller( ){
        $this->init( );
        $this->_article_id = $this->assert_var( 'articleid' );
        if ( !$this->_article_id )
            $this->_article_id = $this->assert_var( 'cid' );
    }

    function display_response( ){
        $renderer = &new AMPDisplay_HTML( );
        $this->message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, AMP_TEXT_YOUR_COMMENT ));
        /*
        print 'pretend redirect to : ' 
                . $renderer->link( 
                        AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, array( 'id='.$this->_article_id ) ),
                        AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, array( 'id='.$this->_article_id ) )
                    );
                */
        ampredirect( AMP_Url_AddVars( AMP_CONTENT_URL_ARTICLE, array( 'id='.$this->_article_id ) ));
    }

}

?>
