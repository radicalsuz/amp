<?php

require_once ('AMP/System/Lookups.inc.php');
require_once ( 'AMP/Content/Config.inc.php');

class AMPContent_Lookup extends AMPSystem_Lookup {

    function AMPContent_Lookup () {
        $this->init();
    }

    function &instance( $type, $instance_var = null, $lookup_baseclass="AMPContentLookup" ) {
        return parent::instance( $type, $instance_var, $lookup_baseclass );
    }


}

class AMPContentLookup_Class extends AMPContent_Lookup {
    var $datatable = "class";
    var $result_field = "class";

    function AMPContentLookup_Class() {
        $this->init();
    }
}

class AMPContentLookup_Classes extends AMPContentLookup_Class {
    function AMPContentLookup_Classes() {
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
        require_once ('AMP/Content/Article/Set.inc.php');
        $articleset = & new ArticleSet ( AMP_Registry::getDbcon() );
        $articleset->addCriteria( Article::makeCriteriaAllowed( ));
        if (!( $counts = $articleset->getGroupedIndex( 'class' ))) return false;

        $class_set = & AMPContent_Lookup::instance( 'class' );
        $this->dataset = array_combine_key( array_keys( $counts ), $class_set );
        asort( $this->dataset );
    }

    function available( ){
        return false;
    }
}

class AMPContentLookup_CommentsByArticle {
    
    var $dataset;
        
    function AMPContentLookup_CommentsByArticle() {
        require_once ( 'AMP/Content/Article/Comments.inc.php' );
        $commentset = & new ArticleCommentSet ( AMP_Registry::getDbcon() );
        $this->dataset =  $commentset->getGroupedIndex( 'articleid' );
    }

    function available( ){
        return false;
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

class AMPContentLookup_SectionTotals extends AMPContent_Lookup {
    var $datatable = 'articles';
    var $result_field = 'count( id ) as qty';
    var $id_field = 'type';
    var $criteria = "!isnull( type ) group by type";

    function AMPContentLookup_SectionTotals( ){
        $this->init( );
        //$this->_mergeRelatedArticles( );
    }

    // merge related doesn't make the right totals for some reason
    function _mergeRelatedArticles( ){
        $related = &AMPContent_Lookup::instance( 'sectionRelatedTotals');
        foreach( $related as $section_id => $count ) {
            if ( !isset( $this->dataset[$section_id])) {
                $this->dataset[$section_id] = '*';
            }
            $this->dataset[$section_id] = $this->dataset[$section_id] . '*';
        }
    }
}

class AMPContentLookup_SectionRelatedTotals extends AMPContent_Lookup {
    var $datatable = 'articlereltype';
    var $result_field = 'count( articleid ) as qty';
    var $id_field = 'typeid';
    var $criteria = "!isnull( typeid ) group by typeid";

    function AMPContentLookup_SectionRelatedTotals( ){
        $this->init( );
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
        if ( !isset( $partial_name )) return parent::instance( 'author');

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
        if ( !isset( $partial_name )) return parent::instance( 'source');

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
    var $sortby = 'textorder,type';

    function AMPContentLookup_SectionParents() {
        $this->criteria = 'id != ' . AMP_CONTENT_SECTION_ID_TOOL_PAGES;
        $this->result_field = "if ( id=". AMP_CONTENT_MAP_ROOT_SECTION .", 0, parent) as parent";
        $this->init();
    }
}

class AMPContentLookup_SectionMap {
    var $dataset;

    function AMPContentLookup_SectionMap() {
        require_once( 'AMP/Content/Map.inc.php');
        $mapsource = &AMPContent_Map::instance( );
        $this->dataset = $mapsource->selectOptions( );

    }
    function available( ){
        return false;
    }
    
}

class AMPContentLookup_LinkTypeMap {
    var $dataset;

    function AMPContentLookup_LinkTypeMap() {
        require_once( 'AMP/System/Data/Tree.php' );
        require_once( 'AMP/Content/Link/Type/Type.php' );

        $link_type = &new Link_Type( AMP_Registry::getDbcon( ));
        $link_map_source = &new AMP_System_Data_Tree( $link_type );
        $this->dataset = $link_map_source->select_options( );
    }

    function available( ){
        return false;
    }
}

class AMPContentLookup_GalleryMap {
    var $dataset;

    function AMPContentLookup_GalleryMap() {
        require_once( 'AMP/System/Data/Tree.php' );
        require_once( 'Modules/Gallery/Gallery.php' );

        $gallery = &new Gallery( AMP_Registry::getDbcon( ));
        $gallery_map_source = &new AMP_System_Data_Tree( $gallery );
        $this->dataset = $gallery_map_source->select_options( );
    }

    function available( ){
        return false;
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

class AMPSystemLookup_Status extends AMPConstantLookup_Status {
    function AMPSystemLookup_Status ( ) {
        $this->__construct( );
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
    function available( ){
        return false;
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
    function available( ){
        return false;
    }
}
class AMPContentLookup_SectionsByArticle extends AMPContent_Lookup {
    var $datatable = 'articlereltype';
    var $result_field = 'articleid';
    var $id_field = 'typeid';

    function AMPContentLookup_SectionsByArticle( $article_id = null ){
        if ( isset( $article_id )) $this->criteria .= $this->_makeCriteriaArticle( $article_id );
        $this->init( );
    }

    function _makeCriteriaArticle( $article_id ) {
        return "articleid =" . $article_id ;
    }

    function &instance( $article_id ) {
        static $lookup = array( );

        if ( !isset( $lookup[ $article_id ])){
            $lookup[$article_id] = new AMPContentLookup_SectionsByArticle ( $article_id );
        } 

        return $lookup[$article_id]->dataset;
    }

    function clear_cache( $article_id ) {
        static $lookup = array( );

        if ( !isset( $lookup[ $article_id ])){
            $lookup[$article_id] = new AMPContentLookup_SectionsByArticle ( $article_id );
        } 

        $factory = & AMPSystem_LookupFactory::instance();
        $factory->clearCache( $lookup[$article_id] );
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

    function _addCriteriaSection( $section_id ){
        $this->criteria = "typeid =" . $section_id ;
    }

    function &instance( $section_id ) {
        static $lookup = array( );
        if (!isset( $lookup[$section_id])) {
            $lookup[ $section_id ] = new AMPContentLookup_RelatedArticles ( $section_id );
        }

        return $lookup[$section_id]->dataset;
    }

    function clear_cache( $section_id ) {
        static $lookup = array( );

        if ( !isset( $lookup[ $section_id ])){
            $lookup[$section_id] = new AMPContentLookup_RelatedArticles ( $section_id );
        } 

        $factory = & AMPSystem_LookupFactory::instance();
        $factory->clearCache( $lookup[ $section_id ] );
    }

    function available( ){
        return false;
    }
}

class AMPContentLookup_Articles extends AMPContent_Lookup{
    var $datatable = 'articles';
    var $result_field = 'Concat( if ( datecreated != "", DATE( datecreated ), "0000-00-00"), " : ", left( title, 50 )) as articlename ';
    var $id_field = "id";
    var $sortby = 'datecreated DESC, title';
    var $criteria = 'trim( title ) != "" and !isnull( title ) and publish=1';
    var $_base_criteria = 'trim( title ) != "" and !isnull( title ) and publish=1';
    
    function AMPContentLookup_Articles( ){
        $this->__construct( );
    }

    function __construct( ) {
        
        $this->add_allowed( );
        $this->init( );
        $this->_clean_titles( );
    }

    function add_allowed( ) {
        $allowed_sections = array_keys( AMP_lookup( 'sectionMap'));
        array_unshift( $allowed_sections, AMP_CONTENT_MAP_ROOT_SECTION );
        $this->criteria = $this->_base_criteria . ' AND type in( '.join( ',', $allowed_sections ) . ')';
    }

    function _clean_titles( ) {
        foreach( $this->dataset as $id => $title ){
            $this->dataset[$id] = strip_tags( $title );
        }

    }
}

class AMPContentLookup_AllowedArticles extends AMPContent_Lookup{
    var $datatable = 'articles';
    var $result_field = 'title';

    function AMPContentLookup_AllowedArticles( ){
        $this->__construct( );
    }

    function __construct( ) {
        
        $this->add_criteria_allowed( );
        $this->init( );
    }

    function add_criteria_allowed( ) {
        $allowed_sections = array_keys( AMP_lookup( 'sectionMap'));
        array_unshift( $allowed_sections, AMP_CONTENT_MAP_ROOT_SECTION );
        $this->criteria = 'type in( '.join( ',', $allowed_sections ) . ')';
    }
}

class AMPSystemLookup_Articles extends AMPContentLookup_Articles{
    function AMPSystemLookup_Articles( ) {
        $this->__construct( );
    }
}
class AMPContentLookup_Event extends AMPContent_Lookup {
    var $datatable = 'calendar';
    var $result_field = 'left( event, 50) as short_event';
    var $id_field = "id";
    var $sortby = 'event';
    var $criteria = 'trim( event ) != "" and !isnull( event )';

    function AMPContentLookup_Event( $partial_title ){
        $this->criteria = isset( $partial_title ) ?
                            $this->_getCriteriaPartialTitle( $partial_title ) :
                            $this->_getBaseCriteria( );
        $this->init( );
        if ( empty( $this->dataset )) {
            return false;
        }
        foreach( $this->dataset as $id => $title ){
            $this->dataset[$id] = strip_tags( $title );
        }
    }

    function _getCriteriaPartialTitle( $partial_title ) {
        return join( ' AND ', array( "event LIKE '" . $partial_title . "%'" , $this->_getBaseCriteria( ) ));
    }

    function setCriteriaPartialTitle( $partial_title ) {
        $this->criteria = $this->_getCriteriaPartialName( $partial_title );
    }

    function _getBaseCriteria( ) {
        return 'trim( event ) != "" and !isnull( event )';
    }

    function &instance( $partial_title=null) {
        if ( !isset( $partial_title )) return parent::instance( 'event');

        static $lookup = false;
        if ( !$lookup ) {
            $lookup = new AMPContentLookup_Event( $partial_title );
        } else {
            $lookup->setCriteriaPartialName( $partial_title );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class AMPContentLookup_Title extends AMPContent_Lookup {
    var $datatable = 'articles';
    var $result_field = 'left( title, 50) as short_title';
    var $id_field = "id";
    var $sortby = 'title';
    var $criteria = 'trim( title ) != "" and !isnull( title ) and type != 2';
    
    function AMPContentLookup_Title( $partial_title ){
        $this->criteria = isset( $partial_title ) ?
                            $this->_getCriteriaPartialTitle( $partial_title ) :
                            $this->_getBaseCriteria( );
        $this->init( );
        foreach( $this->dataset as $id => $title ){
            $this->dataset[$id] = strip_tags( $title );
        }
    }

    function _getCriteriaPartialTitle( $partial_title ) {
        return join( ' AND ', array( "title LIKE '" . $partial_title . "%'" , $this->_getBaseCriteria( ) ));
    }

    function setCriteriaPartialTitle( $partial_title ) {
        $this->criteria = $this->_getCriteriaPartialName( $partial_title );
    }

    function _getBaseCriteria( ) {
        return 'trim( title ) != "" and !isnull( title ) and type != 2';
    }

    function &instance( $partial_title=null) {
        if ( !isset( $partial_title )) return parent::instance( 'title');

        static $lookup = false;
        if ( !$lookup ) {
            $lookup = new AMPContentLookup_Title( $partial_title );
        } else {
            $lookup->setCriteriaPartialName( $partial_title );
            $lookup->init();
        }
        return $lookup->dataset;
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

// deprecated for 3.5.9 -- new nav layout data structure 
class AMPContentLookup_SectionContentNavigationCount extends AMPContent_Lookup {
    var $datatable = 'nav';
    var $result_field = 'count( id ) as totalnavs';
    var $id_field = 'typeid';
    var $criteria = "!isnull( typeid ) group by typeid";

    function AMPContentLookup_SectionContentNavigationCount( ){
        $this->init( );
    }
}

// deprecated for 3.5.9 -- new nav layout data structure 
class AMPContentLookup_SectionListsNavigationCount extends AMPContent_Lookup {
    var $datatable = 'nav';
    var $result_field = 'count( id ) as totalnavs';
    var $id_field = 'typelist';
    var $criteria = "!isnull( typelist ) group by typelist";

    function AMPContentLookup_SectionListsNavigationCount( ){
        $this->init( );
    }
}

// deprecated for 3.5.9 -- new nav layout data structure 
class AMPContentLookup_ClassListsNavigationCount extends AMPContent_Lookup {
    var $datatable = 'nav';
    var $result_field = 'count( id ) as totalnavs';
    var $id_field = 'classlist';
    var $criteria = "!isnull( classlist ) group by classlist";

    function AMPContentLookup_ClassListsNavigationCount( ){
        $this->init( );
    }
}

// deprecated for 3.5.9 -- new nav layout data structure 
class AMPContentLookup_IntrotextsNavigationCount extends AMPContent_Lookup {
    var $datatable = 'nav';
    var $result_field = 'count( id ) as totalnavs';
    var $id_field = 'moduleid';
    var $criteria = "!isnull( moduleid ) group by moduleid";

    function AMPContentLookup_IntrotextsNavigationCount( ){
        $this->init( );
    }
}

class AMPContentLookup_ArticlesbyDocument extends AMPContent_Lookup {
    var $datatable = 'articles';
    var $result_field = 'doc';
    var $criteria = '!isnull( doc ) and doc !=""';

    function AMPContentLookup_ArticlesbyDocument( ){
        $this->init( );
    }
}

class AMPContentLookup_LinkTypes extends AMPContent_Lookup{
    var $datatable = 'linktype';
    var $result_field = 'name';
    var $criteria = '!isnull( publish ) AND publish=1';

    function AMPContentLookup_LinkTypes( ) {
        $this->init( );
    }
}

class AMPContentLookup_RelatedLinks extends AMPContent_Lookup {
    var $datatable = 'linksreltype';
    var $result_field = 'typeid';
    var $id_field = 'linkid';

    function AMPContentLookup_RelatedLinks ( $section_id = null ){
        if ( isset( $section_id )) $this->_addCriteriaSection( $section_id );
        $this->init( );
    }

    function _addCriteriaSection( $article_id ){
        $this->criteria = "typeid =" . $article_id ;
    }

    function &instance( $section_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPContentLookup_RelatedLinks ( $section_id );
        } else {
            $lookup->_addCriteriaSection( $section_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
    function available( ){
        return false;
    }
}
class AMPContentLookup_SectionsByLink extends AMPContent_Lookup {
    var $datatable = 'linksreltype';
    var $result_field = 'linkid';
    var $id_field = 'typeid';

    function AMPContentLookup_SectionsByLink( $link_id = null ){
        if ( isset( $link_id )) $this->_addCriteriaLink( $link_id );
        $this->init( );
    }

    function _addCriteriaLink( $link_id ) {
        $this->criteria = "linkid =" . $link_id ;
    }

    function &instance( $link_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPContentLookup_SectionsByLink ( $link_id );
        } else {
            $lookup->_addCriteriaLink( $link_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }

    function available( ){
        return false;
    }
}

class AMPContentLookup_LinksBySection extends AMPContent_Lookup {
    var $datatable = 'linksreltype';
    var $result_field = 'typeid';
    var $id_field = 'linkid';

    function AMPContentLookup_LinksBySection( $section_id= null ){
        if ( isset( $section_id )) $this->_addCriteriaSection( $section_id );
        $this->init( );
    }

    function _addCriteriaSection( $section_id ){
        $this->criteria = "typeid =" . $section_id ;
    }

    function &instance( $section_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new AMPContentLookup_LinksBySection( $section_id );
        } else {
            $lookup->_addCriteriaSection( $section_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
    function available( ){
        return false;
    }
}

class AMPContentLookup_RSS_Subscriptions extends AMPContent_Lookup {
    var $datatable = 'px_feeds';
    var $result_field = 'title';

    function AMPContentLookup_RSS_Subscriptions( ){
        $this->init( );
    }
}

class AMPContentLookup_FaqTypes extends AMPContent_Lookup {
    var $datatable = 'faqtype';
    var $result_field = 'type';
    var $criteria = 'uselink=1';

    function AMPContentLookup_FaqTypes( ){
        $this->init( );
    }
}


class AMPContentLookup_StylesheetLocationSections extends AMPContent_Lookup {
    var $datatable = 'articletype';
    var $result_field = 'css';
    var $criteria = 'css != "" and !isnull( css ) ';
    var $_base_criteria = 'css != "" and !isnull( css ) ';
    var $sortby = 'type';

    function AMPContentLookup_StylesheetLocationSections( $sheet_name = null ){
        if ( isset( $sheet_name )) $this->addCriteriaStylesheet( $sheet_name );
        $this->init( );
    }

    function addCriteriaStylesheet( $sheet_name ){
        $this->criteria =  $this->_base_criteria . ' and css like \'%'. $sheet_name .'%\'';
    }

    function &instance( $sheet_name ) {
        static $sheet_locations = array( );
        if ( isset( $sheet_locations[ $sheet_name] )) return $sheet_locations[ $sheet_name ];
        $lookup = &new AMPContentLookup_StylesheetLocationSections( $sheet_name );
        $sheet_locations[ $sheet_name ] = $lookup->dataset;
        return $lookup->dataset;
    }
}

class AMPContentLookup_StylesheetLocationTemplates extends AMPContent_Lookup {
    var $datatable = 'template';
    var $result_field = 'css';
    var $criteria = 'css != "" and !isnull( css ) ';
    var $_base_criteria = 'css != "" and !isnull( css ) ';
    var $sortby = 'name';

    function AMPContentLookup_StylesheetLocationTemplates( $sheet_name = null ){
        if ( isset( $sheet_name )) $this->addCriteriaStylesheet( $sheet_name );
        $this->init( );
    }

    function addCriteriaStylesheet( $sheet_name ){
        $this->criteria =  $this->_base_criteria . ' and css like \'%'. $sheet_name .'%\'';
    }

    function &instance( $sheet_name ) {
        static $sheet_locations = array( );
        if ( isset( $sheet_locations[ $sheet_name] )) return $sheet_locations[ $sheet_name ];
        $lookup = &new AMPContentLookup_StylesheetLocationTemplates( $sheet_name );
        $sheet_locations[ $sheet_name ] = $lookup->dataset;
        return $lookup->dataset;
    }
}

class AMPContentLookup_Navs extends AMPContent_Lookup {
    var $datatable = 'navtbl';
    var $result_field = 'name';
    var $sortby = 'name';

    function AMPContentLookup_Navs( ) {
        $this->init( );
    }
}

class AMPContentLookup_NavPositions extends AMPContent_Lookup {
    var $dataset = array( 
        'L1' => 'Left Side, Position 1',
        'L2' => 'Left Side, Position 2',
        'L3' => 'Left Side, Position 3',
        'L4' => 'Left Side, Position 4',
        'L5' => 'Left Side, Position 5',
        'L6' => 'Left Side, Position 6',
        'L7' => 'Left Side, Position 7',
        'L8' => 'Left Side, Position 8',
        'L9' => 'Left Side, Position 9',
        'R1' => 'Right Side, Position 1',
        'R2' => 'Right Side, Position 2',
        'R3' => 'Right Side, Position 3',
        'R4' => 'Right Side, Position 4',
        'R5' => 'Right Side, Position 5',
        'R6' => 'Right Side, Position 6',
        'R7' => 'Right Side, Position 7',
        'R8' => 'Right Side, Position 8',
        'R9' => 'Right Side, Position 9',
        );
    function AMPContentLookup_NavPositions( ){
        //interface
    }
}

class AMPContentLookup_NavLayoutsByIntrotext extends AMPContent_Lookup {
    var $datatable = 'nav_layouts';
    var $result_field = 'introtext_id';
    var $criteria = 'introtext_id != "" AND !isnull( introtext_id )';

    function AMPContentLookup_NavLayoutsByIntrotext( ){
        $this->init( );
    }
}

class AMPContentLookup_NavLayoutsBySectionList extends AMPContent_Lookup {
    var $datatable = 'nav_layouts';
    var $result_field = 'section_id_list';
    var $criteria = 'section_id_list != "" AND !isnull( section_id_list )';

    function AMPContentLookup_NavLayoutsBySectionList ( ){
        $this->init( );
    }
}

class AMPContentLookup_NavLayoutsBySection extends AMPContent_Lookup {
    var $datatable = 'nav_layouts';
    var $result_field = 'section_id';
    var $criteria = 'section_id != "" AND !isnull( section_id )';

    function AMPContentLookup_NavLayoutsBySection ( ){
        $this->init( );
    }
}

class AMPContentLookup_NavLayoutsByClass extends AMPContent_Lookup {
    var $datatable = 'nav_layouts';
    var $result_field = 'class_id';
    var $criteria = 'class_id != "" AND !isnull( class_id )';

    function AMPContentLookup_NavLayoutsByClass ( ){
        $this->init( );
    }
}

class AMPContentLookup_NavLayouts extends AMPContent_Lookup {
    var $datatable = 'nav_layouts';
    var $result_field = 'name';
    var $sortby = 'name';

    function AMPContentLookup_NavLayouts ( ){
        $this->add_criteria_allowed( );
        $this->init( );
    }

    function add_criteria_allowed( ) {
        require_once( 'AMP/Content/Nav/Layout/Layout.php');
        $this->criteria = AMP_Content_Nav_Layout::makeCriteriaAllowed( );
    }
}

class AMPContentLookup_CustomNavLayouts extends AMPContent_Lookup {
    var $datatable = 'nav_layouts';
    var $result_field = 'name';
    var $criteria = 'id not in ( 1, 2 )';
    var $_base_criteria = 'id not in ( 1, 2 )';
    var $sortby = 'name';

    function AMPContentLookup_CustomNavLayouts ( ){
        $this->add_allowed_criteria( );
        $this->init( );
    }

    function add_allowed_criteria( ) {
        require_once( 'AMP/Content/Nav/Layout/Layout.php');
        $this->criteria = $this->_base_criteria . ' AND ' . AMP_Content_Nav_Layout::makeCriteriaAllowed( );
    }
}

class AMPContentLookup_NavLayoutLocationCount extends AMPContent_Lookup {
    var $datatable = 'nav';
    var $result_field = 'count( id ) as totalnavs';
    var $id_field = 'layout_id';
    var $criteria = "!isnull( layout_id ) group by layout_id";

    function AMPContentLookup_NavLayoutLocationCount( ){
        $this->init( );
    }
}

class AMPContentLookup_IntroTexts extends AMPContent_Lookup {
    var $datatable = "moduletext";
    var $result_field = "name";
    var $sortby = "name";

    function AMPContentLookup_IntroTexts () {
        $this->init();
    }
}

class AMPConstantLookup_ToolLinks extends AMPConstant_Lookup {
    var $_prefix_values = 'AMP_CONTENT_URL';
    var $_prefix_labels = 'AMP_TEXT_CONTENT_PAGE';

    function AMPConstantLookup_ToolLinks() {
        $this->init();
    }

    function _sort_default( ){
        asort( $this->dataset );
    }

    function _swapLabels ( $new_labels ) {
        if (!$new_labels || empty( $new_labels )) return false;
        $new_dataset = array( );
        foreach ($new_labels as $label_key => $label_value ) {
            $applied_key = array_search( $label_key, $this->dataset );
            if ($applied_key === FALSE ) continue;
            $new_dataset[ $applied_key ] = $label_value;
        }
        $this->dataset = $new_dataset;
    }
}

class AMPContentLookup_SectionsLive extends AMPContent_Lookup {
    var $datatable = 'articletype';
    var $result_field = 'type';
    var $criteria = 'usenav=1';

    function AMPContentLookup_SectionsLive( ){
        $this->init( );
    }
}

class AMPSystemLookup_ArticlesBySection extends AMPSystem_Lookup {
    var $datatable= 'articles';
    var $result_field = 'title';

    function AMPSystemLookup_ArticlesBySection( $section_id ) {
        $this->__construct( $section_id );
    }

    function __construct( $section_id ) {
        $this->_init_sort( );
        $this->_addCriteriaSection( $section_id );
        $this->init( );
    }

    function _init_sort ( ) {
        $this->_sort =
            "if(isnull(pageorder) or pageorder='', ". AMP_SORT_MAX.", pageorder) ASC, date DESC, id DESC" ;
    }

    function _addCriteriaSection( $section_id ) {
        require_once( 'AMP/Content/Article.inc.php');
        $article = new Article( AMP_Registry::getDbcon( ));
        $this->criteria = $article->makeCriteriaSection( $section_id );
    }

}

class AMPSystemLookup_ArticlesBySectionLive extends AMPSystemLookup_ArticlesBySection {
    function AMPSystemLookup_ArticlesBySectionLive( $section_id ) {
        $this->__construct( $section_id );
    }

    function __construct( $section_id ) {
        $this->_init_sort( );
        $this->_addCriteriaSection( $section_id );
        $this->_addCriteriaDisplayable( );
        $this->init( $section_id );
    }

    function _addCriteriaDisplayable( ) {
        $article = new Article( AMP_Registry::getDbcon( ));
        $this->criteria .= ' AND ' . $article->makeCriteriaDisplayable( );
    }
}

class AMPSystemLookup_ArticleLinksBySectionLive extends AMPSystemLookup_ArticlesBySectionLive {
    var $result_field = 'if ( isnull( linktext ) or linktext=""), title, linktext) as title';

    function AMPSystemLookup_ArticleLinksBySectionLive( $section_id ) {
        $this->__construct( $section_id );
    }

    function __construct( $section_id ) {
        $this->_init_sort( );
        $this->_addCriteriaSection( $section_id );
        $this->_addCriteriaDisplayable( );
        $this->init( $section_id );
    }
}
?>
