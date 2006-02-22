<?php

require_once ('AMP/Content/Article/Set.inc.php');
require_once ('AMP/System/Lookups.inc.php');

class AMPContent_Lookup extends AMPSystem_Lookup {

    function AMPContent_Lookup () {
        $this->init();
    }

    function &instance( $type, $lookup_baseclass="AMPContentLookup" ) {
        return PARENT::instance( $type, $lookup_baseclass );
    }

}

class AMPContentLookup_Class extends AMPContent_Lookup {
    var $datatable = "class";
    var $result_field = "class";

    function AMPContentLookup_Class() {
        $this->init();
    }
}

class AMPContentLookup_Sections extends AMPContent_Lookup {
    var $datatable = "articletype";
    var $result_field = "type";

    function AMPContentLookup_Sections() {
        $this->init();
    }
}

class AMPContentLookup_ActiveClasses {
    
    var $dataset;
    
    function AMPContentLookup_ActiveClasses() {
        $articleset = & new ArticleSet ( AMP_Registry::getDbcon() );
        if (!( $counts = $articleset->getGroupedIndex( 'class' ))) return false;
        $class_set = & AMPContent_Lookup::instance( 'class' );
        $this->dataset = array_combine_key( array_keys( $counts ), $class_set );
        asort( $this->dataset );
    }
}

class AMPContentLookup_CommentsByArticle {
    
    var $dataset;
        
    function AMPContentLookup_CommentsByArticle() {
        require_once ( 'AMP/Content/Article/Comments.inc.php' );
        $commentset = & new ArticleCommentSet ( AMP_Registry::getDbcon() );
        $this->dataset =  $commentset->getGroupedIndex( 'articleid' );
    }
}

class AMPContentLookup_Hotwords extends AMPContent_Lookup {

    var $datatable = "hotwords";
    var $result_field = " Concat( ' <a href=\"', url, '\">', word, '</a> ') as newlink";
    var $id_field = " Concat( ' ', word, ' ') as hotword";

    function AMPContentLookup_Hotwords() {
        $this->criteria = "publish=1";
        $this->init();
    }
}

class AMPContentLookup_SectionHeaders extends AMPContent_Lookup {

    var $datatable = "articles";
    var $result_field = "Max(id) as art_id";
    var $id_field = "type";
    
    function AMPContentLookup_SectionHeaders() {
        $this->criteria = "class = ".AMP_CONTENT_CLASS_SECTIONHEADER." AND publish=".AMP_CONTENT_STATUS_LIVE . " AND !isnull( type ) group by type";
        $this->init();
    }

}
class AMPContentLookup_SectionFooters extends AMPContent_Lookup {

    var $datatable = "articles";
    var $result_field = "Max(id) as art_id";
    var $id_field = "type";
    
    function AMPContentLookup_SectionFooters() {
        $this->criteria = "class = ".AMP_CONTENT_CLASS_SECTIONFOOTER." AND publish=".AMP_CONTENT_STATUS_LIVE . " group by type";
        $this->init();
    }

}

class AMPContentLookup_Author extends AMPContent_Lookup {

    var $datatable = "articles";
    var $result_field = "author";
    var $sortby = "author";
    var $id_field = "author";
    var $distinct = TRUE;
    
    function AMPContentLookup_Author( $partial_name = null ) {
        $this->criteria = isset( $partial_name ) ?
                            $this->_getCriteriaPartialName( $partial_name ) :
                            $this->_getBaseCriteria( );
        $this->init();
    }

    function _getCriteriaPartialName( $partial_name ) {
        return join( ' AND ', array( "author LIKE '" . $partial_name . "%'" , $this->_getBaseCriteria( ) ));
    }

    function setCriteriaPartialName( $partial_name ) {
        $this->criteria = $this->_getCriteriaPartialName( $partial_name );
    }

    function _getBaseCriteria( ) {
        return "publish=".AMP_CONTENT_STATUS_LIVE . " AND author != ''";
    }

    function &instance( $partial_name=null) {
        if ( !isset( $partial_name )) return PARENT::instance( 'author');

        static $lookup = false;
        if ( !$lookup ) {
            $lookup = new AMPContentLookup_Author( $partial_name );
        } else {
            $lookup->setCriteriaPartialName( $partial_name );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class AMPContentLookup_Source extends AMPContent_Lookup {

    var $datatable = "articles";
    var $result_field = "source";
    var $sortby = "source";
    var $id_field = "source";
    var $distinct = TRUE;
    
    function AMPContentLookup_Source( $partial_name = null ) {
        $this->criteria = isset( $partial_name ) ?
                            $this->_getCriteriaPartialName( $partial_name ) :
                            $this->_getBaseCriteria( );
        $this->init();
    }

    function _getCriteriaPartialName( $partial_name ) {
        return join( ' AND ', array( "source LIKE '" . $partial_name . "%'" , $this->_getBaseCriteria( ) ));
    }

    function setCriteriaPartialName( $partial_name ) {
        $this->criteria = $this->_getCriteriaPartialName( $partial_name );
    }

    function _getBaseCriteria( ) {
        return "publish=".AMP_CONTENT_STATUS_LIVE . " AND source != ''";
    }

    function &instance( $partial_name=null) {
        if ( !isset( $partial_name )) return PARENT::instance( 'source');

        static $lookup = false;
        if ( !$lookup ) {
            $lookup = new AMPContentLookup_Source( $partial_name );
        } else {
            $lookup->setCriteriaPartialName( $partial_name );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class AMPContentLookup_SectionParents extends AMPContent_Lookup {

    var $datatable = "articletype";
    var $result_field = "parent";

    function AMPContentLookup_SectionParents() {
        $this->init();
    }
}

class AMPContentLookup_SectionMap {
    var $dataset;

    function AMPContentLookup_SectionMap() {
        $mapsource = &AMPContent_Map::instance( );
        $this->dataset = &$mapsource->selectOptions( );

    }
    
}

class AMPConstantLookup_Listtypes extends AMPConstant_Lookup {
    var $_prefix_values = "AMP_SECTIONLIST";
    var $_prefix_labels = "AMP_TEXT_SECTIONLIST";

    function AMPConstantLookup_Listtypes() {
        $this->init();
    }

}

class AMPContentLookup_Sidebarclass extends AMPConstant_Lookup {
    var $_prefix_values = "AMP_CONTENT_SIDEBAR_CLASS";

    function AMPContentLookup_Sidebarclass() {
        $this->init();
    }

}

class AMPConstantLookup_Status extends AMPConstant_Lookup {
    var $_prefix_values = "AMP_CONTENT_STATUS";
    #var $_prefix_labels = "AMP_TEXT_SECTIONLIST";

    function AMPConstantLookup_Status() {
        $this->init();
    }

}


class AMPContentLookup_Galleries extends AMPContent_Lookup{
    var $datatable = 'gallerytype';
    var $result_field = 'galleryname';
    var $sortby = 'galleryname';
    
    function AMPContentLookup_Galleries( ){
        $this->init( );
    }

}

class AMPContentLookup_Podcasts extends AMPContent_Lookup{
    var $datatable = 'podcast';
    var $result_field = 'title';
    var $sortby = 'title';
    
    function AMPContentLookup_Podcasts( ){
        $this->init( );
    }

}


class AMPContentLookup_GalleryImages extends AMPContent_Lookup {
    var $datatable = 'gallery';
    var $result_field = 'img';
    var $id_field = 'img';
    var $sortby = 'img';

    function AMPContentLookup_GalleryImages( $gallery_id = null ){
        if ( isset( $gallery_id )) $this->_addCriteriaGallery( $gallery_id );
        $this->init( );
    }

    function _addCriteriaGallery( $gallery_id ){
        $this->criteria = "galleryid =" . $gallery_id;
    }

    function &instance( $gallery_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPContentLookup_GalleryImages( $gallery_id );
        } else {
            $lookup->_addCriteriaGallery( $gallery_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class AMPContentLookup_GalleriesByImage extends AMPContent_Lookup {
    var $datatable = 'gallery';
    var $result_field = 'galleryid';
    var $sortby = 'galleryid';

    function AMPContentLookup_GalleriesByImage( $img_filename ){
        $this->_addCriteriaImage( $img_filename );
        $this->init( );
    }

    function _addCriteriaImage( $img_filename ){
        $dbcon = &AMP_Registry::getDbcon( );
        $this->criteria = 'img =' . $dbcon->qstr( $img_filename );
    }
    function &instance( $img_filename ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPContentLookup_GalleriesByImage( $img_filename );
        } else {
            $lookup->_addCriteriaImage( $img_filename );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}
class AMPContentLookup_SectionsByArticle extends AMPContent_Lookup {
    var $datatable = 'articlereltype';
    var $result_field = 'articleid';
    var $id_field = 'typeid';

    function AMPContentLookup_SectionsByArticle( $article_id = null ){
        if ( isset( $article_id )) $this->_addCriteriaArticle( $article_id );
        $this->init( );
    }

    function _addCriteriaArticle( $article_id ){
        $this->criteria = "articleid =" . $article_id ;
    }

    function &instance( $article_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPContentLookup_SectionsByArticle ( $article_id );
        } else {
            $lookup->_addCriteriaArticle( $article_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class AMPContentLookup_RelatedArticles extends AMPContent_Lookup {
    var $datatable = 'articlereltype';
    var $result_field = 'typeid';
    var $id_field = 'articleid';

    function AMPContentLookup_RelatedArticles( $section_id = null ){
        if ( isset( $section_id )) $this->_addCriteriaSection( $section_id );
        $this->init( );
    }

    function _addCriteriaSection( $article_id ){
        $this->criteria = "typeid =" . $article_id ;
    }

    function &instance( $section_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPContentLookup_SectionsByArticle ( $section_id );
        } else {
            $lookup->_addCriteriaSection( $section_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class AMPContentLookup_Articles extends AMPContent_Lookup{
    var $datatable = 'articles';
    var $result_field = 'Concat( DATE( datecreated ), " : ", left( title, 60 )) as articlename ';
    var $id_field = "id";
    var $sortby = 'datecreated DESC, title';
    var $criteria = 'trim( title ) != "" and !isnull( title )';
    
    function AMPContentLookup_Articles( ){
        $this->init( );
        foreach( $this->dataset as $id => $title ){
            $this->dataset[$id] = strip_tags( $title );
        }
    }
}

class AMPContentLookup_ProtectedSections extends AMPContent_Lookup {
    var $datatable = 'articletype';
    var $result_field = 'secure';
    var $criteria = 'secure = 1';

    function AMPContentLookup_ProtectedSections( ){
        $this->init( );
        if ( empty( $this->dataset )) return;
        $map = &AMPContent_Map::instance( );
        foreach( $this->dataset as $section_id => $secure ){
            if ( !( $children = $map->getDescendants( $section_id ))) continue;
            $this->appendChildren( $children );
        }
    }

    function appendChildren( $child_set ){
        foreach( $child_set as $section_id ){
            $this->dataset[$section_id] = 1;
        }
    }
}

class AMPConstantLookup_ImageClasses extends AMPConstant_Lookup {
    var $_prefix_values = "AMP_IMAGE_CLASS";

    function AMPConstantLookup_ImageClasses() {
        $this->init();
    }

}

?>
