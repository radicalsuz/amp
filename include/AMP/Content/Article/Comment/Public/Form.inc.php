<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Article/Comment/Public/ComponentMap.inc.php');
require_once( 'AMP/Content/Article/Comment/ArticleComment.php' );

class Article_Comment_Public_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';
    var $allow_copy = false; 
    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'save' => array(
                'type' => 'submit',
                'label' => 'Save Comment'),
            )
    ));


    function Article_Comment_Public_Form( ) {
        $name = 'comments';
        $this->init( $name );
    }

    function _after_init(  ) {
        $this->addTranslation( 'spam', '_spamchecker', 'get' );
    }

    function _spamchecker( $data, $fieldname ) {
        if( !AKISMET_KEY ) return false;

        $comment_frame = new ArticleComment( AMP_Registry::getDbcon(  ) );
        $comment_frame->mergeData( $data );
        $akismet = $comment_frame->to_akismet(  );
        
        if ( !$akismet ) return false;
        return $akismet->isSpam(  );
    }

    /*
    function validate(  ) {
        $base = parent::validate(  );
        if( !$base || !AKISMET_KEY ) return $base;

        $comment_data = $this->getValues(  );
        $comment_data['website'] = $comment_data['author_url'];
        $comment_data['body'] = $comment_data['comment'];
        $comment_data['permalink'] = ( isset( $comment_data['article_id'] ) && $comment_data['article_id'] ) ? 
                                        AMP_url_update( AMP_CONTENT_URL_ARTICLE, array( 'id' => $comment_data['article_id'] ) ) : false;
        if ( !$comment_data['permalink'] ) {
            $comment_data['permalink'] = ( isset( $comment_data['userdata_id'] ) && $comment_data['userdata_id'] ) ? 
                                            AMP_url_update( AMP_CONTENT_URL_FORM_DISPLAY, array( 'uid' => $comment_data['userdata_id'] ) ) : false;
        }
        $akismet_comment = array_elements_by_key( array( 'author', 'email', 'website', 'body', 'permalink' ), $comment_data );
        require_once( 'akismet/akismet.class.php' );
        $akismet = new Akismet( AMP_SITE_URL, AKISMET_KEY, $akismet_comment );
        if ( $akismet->isError( AKISMET_SERVER_NOT_FOUND ) ) {
            trigger_error( 'Akismet: Server Not Found' );
            return $base;
        }
        if ( $akismet->isError( AKISMET_RESPONSE_FAILED ) ) {
            trigger_error( 'Akismet: Response Failed' );
            return $base;
        }
        if ( $akismet->isError( AKISMET_INVALID_KEY ) ) {
            trigger_error( 'Akismet: Invalid Key' );
            return $base;
        }
        if ( $akismet->isSpam(  ) ) {
            $flash = &AMP_System_Flash::instance(  );
            $flash->add_error( 'no spam please' );
            return false;
        }

        return $base;
    }
    */

}
?>
