<?php

require_once( 'AMP/System/Data/Item.inc.php');

class ArticleComment extends AMPSystem_Data_Item {

    var $datatable = "comments";
    var $name_field = "comment";
    var $_sort_auto = false;
    var $_class_name = 'ArticleComment';
    var $_notify_admin = false;

    function ArticleComment ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function _blankIdAction( ){
      $this->_notify_admin = true;
    }

    function _afterSave( ){
      if($this->_notify_admin && AMP_CONTENT_COMMENT_NOTIFICATION) {
        require_once( 'AMP/System/Email.inc.php');
        $emailer = &new AMPSystem_Email( );

        $emailer->setTarget( AMP_CONTENT_COMMENT_NOTIFICATION );
        $emailer->setSubject( 'New comment' );
$comment_edit_url = AMP_SITE_URL . 'system/' . $this->get_url_edit();
$article_url = AMP_url_update(AMP_SITE_URL . AMP_CONTENT_URL_ARTICLE, array('id' => $this->getArticleId()) );
$status = $this->getStatus();
        $emailer->setMessage( <<<MESSAGE_BODY
A user submitted a new comment on the following article:

$article_url

The comment's status is currently: $status

Edit the comment here: 

$comment_edit_url
MESSAGE_BODY
);

        $result = $emailer->execute( );
      }
    }

    function getTimestamp( ){
        if ( !$result = $this->getData( 'date' )) return null;
        return strtotime( $result );
    }

    function getDate( ){
        return $this->getData( 'date' );
    }

    function getItemDate( ){
        return $this->getDate( );
    }

    function _sort_default( &$item_set ){
        return $this->sort( $item_set, 'timestamp', AMP_SORT_DESC );
    }


    function makeCriteriaArticle( $article_id ){
        return $this->_makeCriteriaEquals( 'articleid', $article_id );
    }

    function makeCriteriaArticle_id( $article_id ){
        return $this->makeCriteriaArticle( $article_id );
    }

    function makeCriteriaCid( $article_id ){
        return $this->makeCriteriaArticle( $article_id );
    }

    function makeCriteriaUserdata_id( $userdata_id ){
        return $this->_makeCriteriaEquals( 'userdata_id', $userdata_id );
    }

    function makeCriteriaModin( $modin ) {
        require_once( 'AMP/UserData/Lookups.inc.php');
        $form_id_lookup = FormLookup::instance( 'modin' );
        $used_ids = array_search( $form_id_lookup, $modin );
        if ( empty( $used_ids )) return false;
        return 'userdata_id in ' . '( ' . join( ",", $used_ids ) . ')';
    }

    function makeCriteriaDisplayable( ) {
        return join( ' AND ', 
                array(    $this->makeCriteriaLive( )
                        , $this->makeCriteriaSpam( false ))
                );
    }

    function makeCriteriaSpam( $is_spam ) {
        return 'spam=' . (  $is_spam ? "1" : "0" );
    }

    function makeCriteriaStatus( $value ) {
        if( $value == 2 ) return $this->makeCriteriaSpam( true );
        return join( ' AND ', array( $this->makeCriteriaPublish( $value ), $this->makeCriteriaSpam( false )));
    }

    function getArticle( ) {
        return $this->getArticleId(  );
    }
    function getArticleId( ) {
        return $this->getData( 'articleid');
    }

    function &getArticleRef( ) {
        $false = false;
        $article_id = $this->getArticle( );
        if ( !$article_id ) return $false;
        $article = new Article( $this->dbcon, $article_id );
        if ( !$article->hasData( )) {
            return $false;
        }
        return $article;
    }

    //function makeCriteriaAllowed( ) {
        //would love something efficient here, this doesn't work'
        //return 'articleid in( '. join( ',', array_keys( AMP_lookup( 'articles'))) . ')';
    //}

    function getAuthor( ){
        return $this->getData( 'author' );
    }

    function getAuthorUrl( ){
        return $this->getData( 'author_url' );
    }

    function getBody( ) {
        return $this->getData( 'comment' );
    }

    function setDefaults( ) {
        $this->mergeData( array( 
            'author_IP' => $_SERVER['REMOTE_ADDR'],
            'agent'     => $_SERVER['HTTP_USER_AGENT'],
            'publish'   => AMP_CONTENT_COMMENT_DEFAULT_STATUS,
            'user_id'   => ( defined( 'AMP_SYSTEM_USER_ID') ? AMP_SYSTEM_USER_ID : false ),
            'date'      => date( 'Y-m-d H:i:s' )
        ));
    }

    function get_url_edit( ) {
        if ( !$this->id ) return AMP_SYSTEM_URL_ARTICLE_COMMENT;
        return AMP_url_add_vars( AMP_SYSTEM_URL_ARTICLE_COMMENT, array( 'id=' . $this->id ));
    }

    function &to_akismet(  ) {
        $false = false;
        if ( !$this->hasData(  ) ) return $false;
        if ( !defined( 'AKISMET_KEY')) return $false;
        /*
        if ( !defined( 'AKISMET_KEY')) {
            print( 'wheres my key?');
            exit;
        }
        */

        $comment_data = $this->getData(  );
        $comment_data['user_agent'] = $comment_data['agent'];
        $comment_data['user_ip'] = $comment_data['author_IP'];
        $comment_data['website'] = $comment_data['author_url'];
        $comment_data['body'] = $comment_data['comment'];
        $comment_data['permalink'] = ( isset( $comment_data['article_id'] ) && $comment_data['article_id'] ) ? 
                                        AMP_url_update( AMP_SITE_URL . '/' . AMP_CONTENT_URL_ARTICLE, array( 'id' => $comment_data['article_id'] ) ) : false;
        if ( !$comment_data['permalink'] ) {
            $comment_data['permalink'] = ( isset( $comment_data['userdata_id'] ) && $comment_data['userdata_id'] ) ? 
                                            AMP_url_update( AMP_SITE_URL . '/' . AMP_CONTENT_URL_FORM_DISPLAY, array( 'uid' => $comment_data['userdata_id'] ) ) : false;
        }
        $akismet_comment = array_elements_by_key( array( 'author', 'email', 'website', 'body', 'permalink' ), $comment_data );
        require_once( 'akismet/akismet.class.php' );
        $akismet = new Akismet( AMP_SITE_URL, AKISMET_KEY, $akismet_comment );

        if ( $akismet->isError( AKISMET_SERVER_NOT_FOUND ) ) {
            trigger_error( 'Akismet: Server Not Found' );
            return $false;
        }
        if ( $akismet->isError( AKISMET_RESPONSE_FAILED ) ) {
            trigger_error( 'Akismet: Response Failed' );
            return $false;
        }
        if ( $akismet->isError( AKISMET_INVALID_KEY ) ) {
            trigger_error( 'Akismet: Invalid Key' );
            return $false;
        }

        return $akismet;
    }

    function isSpam(  ) {
        return $this->getData( 'spam' );
    }

    function _save_update_actions( $data ) {
        //check if spam status has been changed from saved version
        $last_version = clone( $this );
        $last_version->read( $last_version->id );
        if( $data['spam'] == $last_version->isSpam(  ) ) return $data;
        if( !$akismet = $this->to_akismet(  )) return $data;

        $akismet_method = $data['spam'] ? 'submitSpam' : 'submitHam';
        $akismet->$akismet_method(  );
        return $data;
    }


    function spamify(  ) {
        if ( $this->isSpam(  ) ) return false;
        $this->mergeData( array( 'spam' => 1 ) );
        //spam notice happens automatically in save sequence
        if( !( $result = $this->save(  ) ) ) return false;

        $this->notify( 'update' );
        $this->notify( 'spamify' );
        return $result; 

    }

    function despamify(  ) {
        if ( !$this->isSpam(  ) ) return false;
        $this->mergeData( array( 'spam' => 0 ) );
        //despam notice happens automatically in save sequence
        if( !( $result = $this->save(  ) ) ) return false;

        $this->notify( 'update' );
        $this->notify( 'despamify' );
        return $result; 

    }

    function getStatus(  ) {
        if( $this->getData( 'spam' ) ) return AMP_TEXT_SPAM;
        return $this->isLive(  ) ? AMP_TEXT_CONTENT_STATUS_LIVE : AMP_TEXT_CONTENT_STATUS_DRAFT;
    }

}

?>
