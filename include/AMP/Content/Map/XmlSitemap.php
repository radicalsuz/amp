<?php
require_once ( 'XML/Serializer.php');

class AMP_Content_Map_XmlSitemap{
    var $options = array(
        "indent"    => "    ",
        "linebreak" => "\n",
        "typeHints" => false,
        "addDecl"   => true,
        "encoding"  => "UTF-8",
        "rootName"   => "urlset",
        "rootAttributes"  => array("xmlns" => "http://www.sitemaps.org/schemas/sitemap/0.9"),
        "defaultTagName" => "url"
    );
var $serializer;
var $section_keys;
var $article_keys;
var $host;
var $urls = array( 
            AMP_CONTENT_URL_FRONTPAGE,
            AMP_CONTENT_URL_RSSFEED_LIST
          );

    function AMP_Content_Map_XmlSitemap( ){
        $this->__construct( );
    }

    function __construct( )  {
        $this->serializer = new XML_Serializer($this->options);

        $this->article_keys = $this->getArticleKeys( );
        $this->section_keys = $this->getSectionKeys( );
        
        $this->host = 'http://'.$_SERVER['SERVER_NAME'].'/';
    }
    
    function getArticleKeys( ){
        return array_keys( AMP_lookup( 'articles_displayable'));
    }
    
    function getSectionKeys( ){
        $sections = AMP_lookup( 'sections_live');
        $protected_sections = AMP_lookup( 'protected_sections');

        unset($sections[AMP_CONTENT_SECTION_ID_ROOT]);
    
        if ($protected_sections){
            return array_diff(array_keys($sections),array_keys($protected_sections));
        }
        return array_keys($sections);
    }
    function buildUrlArray( ){
        foreach($this->section_keys as $id){
            $this->urls[] = AMP_url_update(AMP_CONTENT_URL_LIST_SECTION,array('type'=>$id));
        }

        foreach($this->article_keys as $id){
            $this->urls[] = AMP_url_update(AMP_CONTENT_URL_ARTICLE, array('id'=>$id));
        }

    }
    
    function buildXmlTree( ){
        $this->buildUrlArray( );
        return array_map( array( $this, 'xmlLoc'), $this->urls);
    }
    
    function xmlLoc( $url){
       return array( 'loc' => $this->host.$url); 
    }
    
    function execute( ){
        if ($this->serializer->serialize($this->buildXmlTree( ))) {
        header('Content-type: text/xml');
        return $this->serializer->getSerializedData();
    }
}
}
?>
