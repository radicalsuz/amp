<?php

define( 'AMP_TEXT_ERROR_CONTENT_TRACKBACK_NO_ARTICLE',
            'I really need an ID for this to work');
define( 'AMP_TEXT_ERROR_CONTENT_TRACKBACK_CLOSED',
		    'Sorry, trackbacks are closed for this item.');
define( 'AMP_TEXT_ERROR_CONTENT_TRACKBACK_EXISTS',
		    'We already have a ping from that URI for this post.');

require_once( 'AMP/System/Data/Item.inc.php');
require_once( 'AMP/System/Data/Set.inc.php');


class ArticleTrackback extends AMPSystem_Data_Item {
    var $datatable = 'comments';
    var $_response;
    var $_incoming_charset = 'ASCII, UTF-8, ISO-8859-1, JIS, EUC-JP, SJIS' ;
    var $_incoming_tags = array( 'url', 'title', 'excerpt', 'blog_name', 'charset');

    function ArticleTrackback( &$dbcon, $id =null ){
        $this->init( $dbcon, $id );
    }

    function setPingData( $data ){
        if ( isset( $data['article_id']))       $this->setArticle( $data['article_id']);
        if ( isset( $data['url']))      $this->setAuthorURL( $data['url']);
        if ( isset( $data['title']))    $this->setTitle( $data['title']);
        if ( isset( $data['excerpt']))  $this->setBody( $data['excerpt']);
        if ( isset( $data['blog_name'])) $this->setAuthorName( $data['blog_name']);
        if ( isset( $data['charset']))  $this->setCharset( $data['charset']);

        return $this->verifyPing( );
    }
    function getAllowedTags( ){
        return $this->_incoming_tags;
    }

    function &getResponse( ){
        $response = &new ArticleTrackback_Response( );
        if ( $this->_response ) $response->setError( $this->_response );
        return $response;
    }

    function setTitle( $title ){
        if ( !$title ) return false;
        return $this->mergeData( array( 'title' => AMP_trimText( $title, 250, false )));
    }

    function setBody( $body ){
        if ( !$body ) return false;
        return $this->mergeData( array( 'body' => AMP_trimText( $body, 255, false )));
    }

    function setAuthorName( $author_name ){
        if ( !$author_name) return false;
        return $this->mergeData( array( 'author' => $author_name ));
    }
    function setAuthorURL( $author_url ){
        if ( !$author_url) return false;
        return $this->mergeData( array( 'author_url' => $author_url ));
    }

    function setCharset( $encoding ){
        $this->_incoming_charset = $encoding;
    }

    function getCharset( ){
        return $this->_incoming_charset;
    }

    function setArticle( $article_id ){
        require_once( 'AMP/Content/Article.inc.php');
        $article = &new Article( $this->dbcon, $article_id );
        if ( !$article->hasData( ) ) return false;
        return $this->mergeData( array( 'articleid'=> $article_id ));
    }

    function &getArticleRef( ) {
        if ( !( $article_id = $this->getArticle( ))) return false;
        require_once( 'AMP/Content/Article.inc.php');
        $article = &new Article( $this->dbcon, $article_id );
        return $article;
    }

    function verifyPing( ){
        $this->_verifyCharset( ) ;
        if ( !$article = &$this->getArticleRef( )) {
            $this->_setResponse( AMP_TEXT_ERROR_CONTENT_TRACKBACK_NO_ARTICLE )  ;
            return false;
        }
        if ( !$article->allowsComments( )) {
            $this->_setResponse( AMP_TEXT_ERROR_CONTENT_TRACKBACK_CLOSED );
            return false;
        }
        if ( $this->duplicateUrlExists(  $this->getAuthorURL( ))) {
            $this->_setResponse(  AMP_TEXT_ERROR_CONTENT_TRACKBACK_EXISTS );
            return false;
        }
        return true;
    }

    function duplicateUrlExists( $test_url ){
        $testSet = &new ArticleTrackbackSet( $this->dbcon, $this->getArticle( ));
        $testSet->addCriteriaUrl( $test_url );
        $testSet->readData( );
        return $testSet->hasData( );
    }

    function _setResponse( $error_message ){
        $this->_response = $error_message;
    }

    function _verifyCharset( ){
        $convertable_items = array( 'title', 'body', 'authorName');

        foreach( $convertable_items as $item ){
            $this->_swapcharSet( $item, $this->_incoming_charset );
        }
        
        return true;
    }
    function _swapcharSet( $item, $source_charset, $target_charset = AMP_SITE_CONTENT_ENCODING ){
        if (!function_exists( 'mb_convert_encoding')) return; 

        $set_method = 'set'.ucfirst( $item );
        $get_method = 'get'.ucfirst( $item );
        $this->$set_method( mb_convert_encoding( $this->$get_method( ), $target_charset, $source_charset ));
    }

    function getArticle( ){
        return $this->getData( 'articleid');
    }

    function getAuthorURL( ){
        return $this->getData( 'author_url');
    }

    function getBody( ){
        return $this->getData( 'comment');
    }

    function getTitle( ){
        return $this->getData( 'title');
    }
    function getAuthorName( ){
        return $this->getData( 'author');
    }

    function validate( ){
        if ( !( $url = $this->getAuthorURL( ))){
            return false;
        }

        curl_init( $url );
        $page_content = curl_exec( );

        $permalink_q = preg_quote( $url, '/' );
        $pattern="/<\s*a.*href\s*=[\"'\s]*".$permalink_q."[\"'\s]*.*>.*<\s*\/\s*a\s*>/i";
          
        return (preg_match($pattern,$page_content));
        
    }

}

class ArticleTrackbackSet extends AMPSystem_Data_Set {
    var $datatable = 'comments';
    
    function ArticleTrackbackSet( &$dbcon, $article_id = null ){
        if ( isset( $article_id)) $this->addCriteriaArticle( $article_id );
        $this->init( $dbcon );
    }

    function addCriteriaArticle( $article_id ){
        $this->addCriteria( 'articleid='.$article_id );
    }

    function addCriteriaUrl( $url ){
        $this->addCriteria( 'author_url = ' . $this->dbcon->qstr( $url ));
    }

}

class ArticleTrackback_Response {
    var $_error_class = 0;
    var $_message = '';

    function ArticleTrackback_Response( $error_value = 0, $message = '' ){
        if ( $error_value ) $this->setErrorClass( $error_value );
        if ( $message ) $this->setMessage( $message );

    }

    function execute( ){
        header('Content-Type: text/xml; charset=' . AMP_SITE_CONTENT_ENCODING );
        if ( !$this->_error_class ) return $this->_XML_success( );
        return $this->_XML_error( );
    }

    function setError( $message ){
        $this->setErrorClass( 1 );
        $this->setMessage( $message );
    }

    function setErrorClass( $error_value ){
        $this->_error_class = $error_value;
    }
    function setMessage( $message_text ){
        $this->_message = $message_text;
    }

    function _XML_error( ){
		return  '<?xml version="1.0" encoding="utf-8"?'.">\n"
		        . "<response>\n"
		        . "<error>". $this->_error_class."</error>\n"
		        . "<message>". $this->_message ."</message>\n"
		        . "</response>";
    }

    function _XML_success( ){
		return  '<?xml version="1.0" encoding="utf-8"?'.">\n"
		        . "<response>\n"
		        . "<error>0</error>\n"
		        . "</response>";
    }

}
?>
