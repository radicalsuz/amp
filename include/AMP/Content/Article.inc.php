<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
//require_once ( 'AMP/Content/Article/Version.inc.php' );
require_once ( 'AMP/Content/Article/Comments.inc.php' );
require_once ( 'AMP/Content/Article/DocumentLink.inc.php' );
require_once ( 'AMP/Content/Image.inc.php' );

define ('AMP_CONTENT_STATUS_LIVE', 1);
define ('AMP_CONTENT_STATUS_DRAFT', 0);

class Article extends AMPSystem_Data_Item {

    var $datatable = "articles";
    var $name_field = "title";

    function Article( &$dbcon, $id = null ) {
        $this->init ($dbcon, $id);
    }

    function getParent() {
        return $this->getData( 'type' );
    }

    function getSection() {
        return $this->getParent();
    }

    function getClass() {
        return $this->getData( 'class' );
    }

    function getTitle() {
        return $this->getData( 'title' );
    }

    function getAuthor() {
        return $this->getData( 'author' );
    }

    function getBlurb() {
        return $this->getData( 'shortdesc' );
    }

    function getRedirect() {
        if (!$this->getData( 'linkover' )) return false;
        if (! ($target = $this->getData( 'link' ))) return false;
        return $target;
    }
    
    function getContact() {
        return $this->getData( 'contact' );
    }
    function getSource() {
        if( $source = $this->getData( 'source' )) return $source;
        return $this->getSourceURL() ;
    }

    function getBody() {
        return $this->getData( 'body' );
    }

    function getSourceURL() {
        return $this->getData( 'sourceurl' );
    }

    function getImageFileName() {
        if (!$this->getData( 'picuse' )) return false;
        return $this->getData( 'picture' );
    }

    function &getImageRef() {
        if (! ($img_path = $this->getImageFileName())) return false;
        $image = &new Content_Image();
        $image->setData( $this->getImageData() );
        return $image;
    }

    function getImageData() {
        return array(   'filename'  =>  $this->getImageFileName(),
                        'caption'   =>  $this->getData( 'piccap' ),
                        'alignment' =>  $this->getData( 'alignment' ),
                        'alttag'    =>  $this->getData( 'alttag' ),
                        'image_size'=>  $this->getData( 'pselection' ) );
    }

    function allowsComments() {
        return $this->getData( 'comments' );
    }

    function &getComments() {
        if (!$this->allowsComments()) return false;
        return new ArticleComments( $this->dbcon, $this->id );
    }

    function getDocumentLink() {
        return $this->getData('doc');
    }

    function getDocLinkType() {
        return $this->getData('doctype');
    }

    function &getDocLinkRef() {
        if (!($doc = $this->getDocumentLink() )) return false;
        $doclink = &new DocumentLink();
        $doclink->setFileName( $doc, $this->getDocLinkType() );
        return $doclink;
    }




    function isNews() {
        if (!$this->getClass()) return false;
        if ($this->getClass()== AMP_CONTENT_CLASS_NEWS) return true;
        if ($this->getClass()== AMP_CONTENT_CLASS_MORENEWS) return true;
        return false;
    }

    function isPressRelease() {
        if (!$this->getClass()) return false;
        return ($this->getClass()== AMP_CONTENT_CLASS_PRESSRELEASE);
    }

    function isLive() {
        return ($this->getData('publish')==AMP_CONTENT_STATUS_LIVE);
    }

    function adjustSetData( $data ) {
        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
    }

    function readVersion( $version_id ) {
        $version = &new Article_Version( $this->dbcon, $version_id );
        if (!$version->hasData()) return false;

        $this->setData( $version->getData() );
    }

}
?>
