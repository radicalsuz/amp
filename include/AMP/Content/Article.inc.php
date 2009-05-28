<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Image.inc.php' );
require_once ( 'AMP/System/File/Image.php' );
require_once ( 'AMP/Content/Article/Display.inc.php' );
require_once ( 'AMP/Content/Config.inc.php');
require_once ( 'AMP/Content/Article/Public/Includes.php');

/**
 * Article 
 * 
 * @uses AMPSystem_Data_Item
 * @package Content
 * @version 3.5.4
 * @copyright 2005 Radical Designs
 * @author Austin Putman <austin@radicaldesigns.org> 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */
class Article extends AMPSystem_Data_Item {

    var $datatable = "articles";
    var $name_field = "title";
    var $_sort_auto = false;
    var $_class_name = 'Article';

    var $_version_status = false;
    var $_save_with_callbacks = true;

    /**
     * Article 
     * 
     * @param       & $dbcon      a reference to the current database connection 
     * @param mixed $id           the id of the desired article 
     * @access public
     * @return void
     */
    function Article( &$dbcon, $id = null ) {
        $this->init ($dbcon, $id);
        $this->_addAllowedKey( 'new_alias_name' );
        $this->_addAllowedKey( 'sections_related' );
        $this->_addAllowedKey( 'url' );
        $this->_addAllowedKey( 'tags' );
        $this->_addAllowedKey( 'route_slug' );
    }

    function &getDisplay() {
        /*
        $classes = filterConstants( 'AMP_CONTENT_CLASS' );
        $display_def_constant = 'AMP_ARTICLE_DISPLAY_' . array_search( $this->getClass() , $classes );

        $display_class = AMP_ARTICLE_DISPLAY_DEFAULT;
        if (defined( $display_def_constant )) $display_class = constant( $display_def_constant );
        */
        $display_class = $this->getDisplayClass( );

        if (!class_exists( $display_class )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $display_class ));
            $display_class = AMP_ARTICLE_DISPLAY_DEFAULT;
        }
        $result = &new $display_class( $this );
        return $result;
    }

    function getDisplayClass( ) {
        $displays = AMP_lookup( 'article_displays');
        $default = AMP_ARTICLE_DISPLAY_DEFAULT;
        if( !isset( $displays[$this->getClass( )])) return $default;
        if( !class_exists( $displays[$this->getClass( )])) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $displays[$this->getClass( )]));
            return $default;
        }
        return $displays[$this->getClass( )];
    }

    function getParent() {
        return $this->getData( 'type' );
    }

    function getSection() {
        return $this->getParent();
    }

    function getAllSections() {
        //$related_set = &AMPContentLookup_SectionsByArticle::instance( $this->id );
        $related_set = AMP_lookup( 'sectionsByArticle', $this->id ); //&AMPContentLookup_SectionsByArticle::instance( $this->id );
        if ( empty( $related_set )) return array( $this->getParent( ));
        $return_set = array_keys( $related_set );
        $return_set[] = $this->getParent( );
        return $return_set;
    }

    function hasAncestor( $section_id ) {
        $map = AMPContent_Map::instance( );
        $ancestors = $map->getAncestors( $this->getParent( ));
        return isset( $ancestors[$section_id]) ;
    }

    function getClass() {
        return $this->getData( 'class' );
    }

    function getTitle() {
        return $this->getData( 'title' );
    }

    function getSubTitle() {
        return $this->getData( 'subtitle' );
    }

    function getAuthor() {
        return $this->getData( 'author' );
    }

    function getBlurb() {
        return $this->getData( 'blurb' );
    }

    function getRedirect() {
        #if (!$this->getData( 'linkover' )) return false;
        if (! ($target = $this->getData( 'link' ))) return false;
        return $target;
    }

    function getURL() {
        if ($url = $this->getRedirect() ) return $url;
        if (!$this->id ) return false;
        return $this->getURL_default( ) ;
    }

    function getURL_default( ){
        return AMP_route_for( 'article', $this->id );
    }
    
    function getURL_without_pretty_urls( ) {
        return AMP_url_update_without_pretty_urls(  AMP_CONTENT_URL_ARTICLE, array( 'id' => $this-> id )) ;
    }
    function getContact() {
        return $this->getData( 'contact' );
    }
    function getSource() {
        if( $source = $this->getData( 'source' )) return $source;
        return $this->getSourceURL() ;
    }

    function get_source_url(  ) {
        return $this->getSourceURL(  );
    }

    function getBody() {
        return $this->getData( 'body' );
    }

    function getSidebar() {
        return $this->getData( 'navtext' );
    }
	
	function getSidebarClass() {
        return $this->getData( 'sidebar_class' );
    }
	
    function getSourceURL() {
        return $this->getData( 'sourceurl' );
    }

    function getMoreLinkURL() {
        //if (!$this->getData('usemore')) return false;
        return $this->getData( 'morelink' );
    }

    function getImageFileName() {
        #if (!$this->getData( 'picuse' )) return false;
        return $this->getData( 'picture' );
    }

    function getArticleDate() {
        if (!$this->isPublicDate()) return false;
        return $this->getAssignedDate( );
    }

    function getAssignedDate( ){
        $date_value =  $this->getData('date');
        if (!AMP_verifyDateValue( $date_value )) return false;
        return $date_value;
    }

    function isPublicDate() {
        return !($this->getData( 'usedate' ));
    }

    function getItemDate() {
        return $this->getArticleDate();
    }

    function getOrder( ){
        return $this->getData( 'pageorder' );
    }

    function getItemDateChanged() {
        return $this->getData( 'updated');
    }

    function getItemDateCreated() {
        return $this->getData( 'datecreated');
    }

    function &getImageRef() {
        $empty_value = false;
        if (! ($img_path = $this->getImageFileName())) return $empty_value;
        $image = &new Content_Image( $img_path );
        $image->setData( $this->getImageData() );
        return $image;
    }

    function getImageData() {
        return array(   'filename'  =>  $this->getImageFileName(),
                        'caption'   =>  $this->getData( 'piccap' ),
                        'alignment' =>  $this->getData( 'alignment' ),
                        'align'     =>  $this->getData( 'alignment' ),
                        'alttag'    =>  $this->getData( 'alttag' ),
                        'alt'       =>  $this->getData( 'alttag' ),
                        'image_size'=>  $this->getImageClass() );
    }

    function &getImageFile( ) {
		$false = false;
        if( !( $name = $this->getImageFileName( ))) return $false;
        $img_class = $this->getImageClass( );
        if( !$img_class ) $img_class = AMP_IMAGE_CLASS_OPTIMIZED;
        $image = &new AMP_System_File_Image( AMP_image_path( $name, $img_class )) ;
        if ( !$image->getPath( )) return $false;
        $image->set_display_metadata( $this->getImageData( ));
        return $image;
    }

    function getImageClass() {
        return $this->getData( 'pselection' );
    }

    function display_image_in_body( ) {
        return ( $this->getImageClass( ) != 'list_only');
    }

    function getShowInNavs( ){
        return $this->getData( 'uselink');
    }

    function getShowInFrontPage( ){
        return $this->getData( 'fplink');
    }

    function getShowAsNew( ){
        return $this->getData( 'new');
    }

    function getRegion( ){
        return $this->getData( 'state');
    }

    function getLastEditorId( ){
        return $this->getData( 'updatedby' );
    }

    function getCreatorId( ){
        return $this->getData( 'enteredby');
    }

    function allowsComments() {
        if ( $this->getClass( ) == AMP_CONTENT_CLASS_BLOG ) return true;
        return $this->getData( 'comments' );
    }

    function &getComments() {
        $empty_value = false;
        if (!$this->allowsComments()) return $empty_value;
        require_once ( 'AMP/Content/Article/Comments.inc.php' );
        $comment_set = &new ArticleCommentSet( $this->dbcon, $this->id );
        return $comment_set;
        
        //require_once ( 'AMP/Content/Article/Comment/ArticleComment.php' );
        //$comment_source = &new AMP_Content_Article_Comment_List( );
    }

    function getDocumentLink() {
        return $this->getData('doc');
    }

    function getDocLinkType() {
        return $this->getData('doctype');
    }

    function &getDocLinkRef() {
        require_once ( 'AMP/Content/Article/DocumentLink.inc.php' );
        $empty_value = false;
        if (!($doc = $this->getDocumentLink() )) return $empty_value;
        $doclink = &new DocumentLink();
        $doclink->setFile( $doc, $this->getDocLinkType() );
        return $doclink;
    }

    /**
     * isNews 
     * 
     * @access public
     * @return void
     */
    function isNews() {
        if (!$this->getClass()) return false;
        if ($this->getClass()== AMP_CONTENT_CLASS_NEWS) return true;
        if ($this->getClass()== AMP_CONTENT_CLASS_MORENEWS) return true;
        return false;
    }

    /**
     * isPressRelease 
     * 
     * @access public
     * @return void
     */
    function isPressRelease() {
        if (!$this->getClass()) return false;
        return ($this->getClass()== AMP_CONTENT_CLASS_PRESSRELEASE);
    }

    function isLive() {
        return ($this->getData('publish')==AMP_CONTENT_STATUS_LIVE);
    }
    function isDisplayable( ) {
        if( !$this->isLive( )) return false;
        $excluded_classes = AMP_lookup( 'excluded_classes_for_display' );
        if( array_search( $this->getClass( ), $excluded_classes ) !== FALSE ) return false;

		require_once ( 'AMP/Content/Section.inc.php' );
        $section = new Section( AMP_dbcon( ), $this->getSection( ));
        return $section->isDisplayable( );
    }

    function isHtml() {
        return $this->getData( 'html' );
    }

    function _adjustSetData( $data ) {

        $this->legacyFieldname( $data, 'test', 'body' );
        $this->legacyFieldname( $data, 'subtitile', 'subtitle' );
        $this->legacyFieldname( $data, 'shortdesc', 'blurb' );

        $this->legacyFieldname( $data, 'type', 'section' );
        $this->legacyFieldname( $data, 'picture', 'image' );

        if ( isset( $data['link']) && $data['link'] && !isset( $data['linkover'])) {
            $this->mergeData( array( 'linkover' => 1));
        }

        if ( !isset( $data[ 'url' ] ) && ( $article_url = $this->getURL( )) ){
            $this->mergeData( array( 'url' => $article_url ));
        }

    }

    function _afterRead( ) {
        if( AMP_CONTENT_HUMANIZE_URLS) {
            $current_route = AMP_route_for( 'article', $this->id );
            if( $current_route && $current_route != $this->getURL_without_pretty_urls( )) {
                $this->mergeData(  array( 'route_slug' => $current_route ));
            }
        }

    }

    function _save_create_actions( $data ){
        if ( !( isset( $data['datecreated']) && $data['datecreated'] )){
            $data['datecreated'] = date( 'Y-m-d');
        }
        if ( !( isset( $data['enteredby']) && $data['enteredby'])){
            $data['enteredby'] = AMP_SYSTEM_USER_ID ;
        }
        return $data;

    }

    function _save_update_actions( $data ) {
        $data['updatedby'] = AMP_SYSTEM_USER_ID ;
        $data['updated'] = date( 'Y-m-d H:i:s');
        if ( $this->list_action ) {
            $sections_related = $this->_getSectionsRelatedDB( );
        }
        
        return $data;

    }

    function readVersion( $version_id ) {
        require_once ( 'AMP/Content/Article/Version.inc.php' );
        $version = &new Article_Version( $this->dbcon, $version_id );
        if (!$version->hasData()) return false;

        $this->setData( $version->getData() );
        $this->_version_status = true;
    }

    function saveVersion( ){
        require_once ( 'AMP/Content/Article/Version.inc.php' );
        $version = &new Article_Version( $this->dbcon );
        $version->mergeData( $this->getData( ));
        return $version->save( );
    }

    function setDefaults( ){
        $this->mergeData( array( 
            'type'      => AMP_CONTENT_MAP_ROOT_SECTION,
            'linkover'  => 1,
            'uselink'   => 1,
            'class'     => AMP_CONTENT_CLASS_DEFAULT,
            'enteredby' => AMP_SYSTEM_USER_ID,
            'datecreated'   => date( 'Y-m-d' )
        ));
    }

    function getNewAliasName( ){
        return $this->getData( 'new_alias_name' );
    }

    function getSectionsRelated() {
        if ( $internal_value = $this->_getSectionsRelatedBase( )) return $internal_value;
        return $this->_getSectionsRelatedDB( );
    }

    function _getSectionsRelatedBase( ){
        return $this->getData( 'sections_related');
    }

    function _getSectionsRelatedDB( ){
        $db_related_sections = AMP_lookup( 'sectionsByArticle', $this->id );
        $allowed_sections = AMP_lookup( 'sectionMap');
        $related_sections = array_combine_key( array_keys( $allowed_sections ), $db_related_sections );

        if ( !$related_sections ) return false;

        $this->mergeData( array(  'sections_related' => array_keys( $related_sections ) ));
        return array_keys( $related_sections );
    }

    function clearAliasName( ) {
        return $this->mergeData( array( 'new_alias_name' => false ));
    }

    function getExistingAliases( ){
        if ( !isset( $this->id )) return false;
        require_once( 'AMP/Content/Redirect/Redirect.php' );
        $redirect = &new AMP_Content_Redirect( $this->dbcon );
        return $redirect->search( $redirect->makeCriteria( array( 'target' => $this->getURL_default( ))));
    }

    function _afterSave( ){
        if ( $this->_save_with_callbacks ) {
            $this->_save_aliases( );
            $this->_save_route_slug( );
            $this->_save_sections_related( );
            $this->_save_tags( );
        }
    }

    function _save_sections_related( ){
        $sections_related = $this->_getSectionsRelatedBase( );
        $active_related = $this->_getSectionsRelatedDB( ) ;
        if ( !$active_related && !$sections_related ) return false;

        if ( !$sections_related ) $sections_related = array( );
        if ( $active_related ) {
            $deleted_items = array_diff( $active_related, $sections_related );
            $new_items = array_diff( $sections_related, $active_related );
        } else {
            $deleted_items = array( );
            $new_items = $sections_related;
        }
        trigger_error( count( $deleted_items) . ' deleted and ' . count( $new_items ) . ' new items');
        if ( empty( $deleted_items ) && empty( $new_items )) return false;

        require_once( 'AMP/Content/Section/RelatedSet.inc.php');
        $related_section_set = &new SectionRelatedSet( $this->dbcon );

        if ( !empty( $deleted_items )) {
            $delete_crit = $this->_makeRelatedSectionCriteria( $deleted_items );
            $related_section_set->deleteData( $delete_crit );
            foreach( $deleted_items as $section_id ) {
                AMPContentLookup_RelatedArticles::clear_cache( $section_id );
            }
        }
        if ( !empty( $new_items )) {
            foreach( $new_items as $section_id ) {
                $insert_values = array( 'typeid' => $section_id , 'articleid' => $this->id );
                $related_section_set->insertData( $insert_values );
                AMPContentLookup_RelatedArticles::clear_cache( $section_id );
            }

        }
        AMPContentLookup_SectionsByArticle::clear_cache( $this->id );
    }

    function _save_tags( ) {
        if ( $this->isVersion( )) return;
        return AMP_update_tags( $this->_getTagsBase( ), false, $this->id, AMP_SYSTEM_ITEM_TYPE_ARTICLE );
    }

    function getTags( ) {
        $tags = $this->_getTagsBase( );
        if ( !$tags ) {
            return $this->_getTagsDB( );
        }
        return $tags;
    }

    function _getTagsBase( ) {
        return $this->getData( 'tags');
    }

    function _getTagsDB( ) {
        if ( !isset( $this->id ) && $this->id ) return false;
        $tag_lookup = AMPSystem_Lookup::instance( 'tagsByArticle', $this->id );
        if ( $tag_lookup ) {
            $tags = array_keys( $tag_lookup );
            $this->mergeData( array( 'tags' => $tags ));
        }
        return $tag_lookup;

    }

    function _makeRelatedSectionCriteria( $section_id_array ) {
        if ( empty( $section_id_array ) || !is_array( $section_id_array )) return false;
        return 'typeid in ( '.join( ',', $section_id_array ).' ) and articleid=' . $this->id;
    }

    function _save_route_slug( ) {
        if( !AMP_CONTENT_HUMANIZE_URLS) return true;
        $finder = new AMP_Content_RouteSlug( AMP_dbcon( ));
        $slugs = $finder->find( array( 'owner_type' => 'article', 'owner_id' => $this->id));
        $assigned_slug = $this->getData( 'route_slug' );
        if( empty( $slugs ) && !$assigned_slug ) return true;
		$slug_exists = false;

		foreach($slugs as $slug) {
            if( $slug->getName( ) == $assigned_slug ) {
				$slug_exists = true;
				continue;
			}

			$slug->delete( );
        }

		if($slug_exists) return true;

		$slug = $finder;
        $slug->mergeData( array( 'name' => $assigned_slug, 'owner_type' => 'article', 'owner_id' => $this->id ));
        $slug->force_valid_slug( );
        return $slug->save( );
    }

    function _save_aliases( ){
        if ( !( $alias_name = $this->getNewAliasName( ))) return false;
        $alias_name = urlencode( $alias_name );
        require_once( 'AMP/Content/Redirect/Redirect.php' );
        $redirect = &new AMP_Content_Redirect( $this->dbcon );
        $existing_items = $redirect->search( $redirect->makeCriteria( array( 'alias' => $alias_name )));
        if ( $existing_items ){
            foreach( $existing_items as $existing_redirect ){
                $existing_redirect->setTarget( $this->getURL( ));
                $existing_redirect->save( );
            }
            $this->clearAliasName( );
            return true;
        }
        $redirect->setDefaults( );
        $redirect->setAlias( $alias_name );
        $redirect->setTarget( $this->getURL( ));
        $this->clearAliasName( );
        return $redirect->save( );

    }

    function _sort_default( &$item_set ) {
        $this->sort( $item_set, 'defaultOrder');
    }

    function getDefaultOrder( ){
        $item_date = $this->getAssignedDate( );
        $item_timestamp = $item_date ? strtotime ( $item_date ) :  strtotime( AMP_NULL_DATETIME_VALUE_UNIX  );
        $desc_date_value = strtotime( AMP_FUTURE_DATETIME_VALUE_UNIX ) + 100 -  $item_timestamp;
        
        return
                  ':' . intval( !$this->isLive( ))
                . ':' . intval( $this->getParent( ))
                . ':' . ( intval( $this->getOrder( )) ? intval(  $this->getOrder( )) : AMP_SORT_MAX )
                . ':' . $desc_date_value;
    }

    function setOrder( $order ) {
        return $this->mergeData( array( 'pageorder' => $order ));
    }

    function setSection( $section_id ) {
        return $this->mergeData( array( 'type' => $section_id ));
    }

    function setClass( $class_id ) {
        return $this->mergeData( array( 'class' => $class_id ));
    }

    function setRegion ( $region_id ) {
        return $this->mergeData( array( 'state' => $region_id ));
    }


    function reorder( $new_order_value ) {
        if ( $new_order_value == $this->getOrder( )) return false;
        if ( is_array( $new_order_value )) return false;
        $this->setOrder( $new_order_value );
        if ( !( $result = $this->save_without_callbacks( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'reorder' );
        return $result;

    }

    function move( $section_id = false, $class_id = false, $related_section_id = false ) {
        $move_action = false;
        if ( $section_id && $section_id != $this->getSection( )) {
            $this->setSection( $section_id );
            $move_action = true;
        }
        if ( $related_section_id ) {
            $relate_result = $this->relate( $related_section_id );
            $move_action = $move_action || $relate_result; 
        }
        if ( $class_id  && $class_id != $this->getClass( )){
            $this->setClass( $class_id );
            $move_action = true;
        }
        if ( !$move_action ) return false;

        $this->list_action= 'move';
        if ( !( $result = $this->save_without_callbacks( ) or $relate_result )) return false;
        $this->notify( 'update' );
        $this->notify( 'move'   );
        return $result;
    }

    function save_without_callbacks( ) {
        $this->_save_with_callbacks = false;
        $result = $this->save( );
        $this->_save_with_callbacks = true;
        return $result;
    }

    function regionize( $region_id ) {
        if ( $region_id == $this->getRegion( )) return false;
        $this->setRegion( $region_id );

        $this->list_action= 'regionize';
        if ( !( $result = $this->save_without_callbacks( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'regionize' );
        return $result;

    }

    function tag( $tag_id = false, $tag_names = false ) {
        if ( $tag_names ) {
            require_once( 'AMP/Content/Tag/Tag.php');
            $new_tag_set = AMP_Content_Tag::create_many( $tag_names );
            $new_tag_results = true;
            foreach( $new_tag_set as $new_tag_id ) {
                $new_tag_results = $new_tag_results && $this->tag( $new_tag_id );
            }
            if ( !$tag_id ) return $new_tag_results;
        }

        if ( !$tag_id ) return false;
        $related_tags = AMPSystem_Lookup::instance( 'tagsByArticle', $this->id );
        if ( isset( $related_tags[ $tag_id ])) return false;

        require_once( 'AMP/Content/Tag/Item/Item.php');
        $action_item = &new AMP_Content_Tag_Item( AMP_Registry::getDbcon( ));
        $create_values = array( 'item_type' => 'article', 'item_id' => $this->id, 'user_id' => AMP_SYSTEM_USER_ID, 'tag_id' => $tag_id );
        $action_item->setData( $create_values );
        return $action_item->save( );
    }

    function relate( $section_id ) {
        if ( !$section_id ) return false;
        $db_related = $this->_getSectionsRelatedDB( );
        if ( $db_related && ( array_search( $section_id, $db_related ) !== FALSE ) ) return false;

        require_once( 'AMP/Content/Section/RelatedSet.inc.php');
        $related_section_set = &new SectionRelatedSet( $this->dbcon );

        $insert_values = array( 'typeid' => $section_id , 'articleid' => $this->id );
        return $related_section_set->insertData( $insert_values );

    }

    function unrelate( $section_id ) {
        require_once( 'AMP/Content/Section/RelatedSet.inc.php');
        $related_section_set = new SectionRelatedSet( $this->dbcon );
        return $related_section_set->deleteData( 'typeid=' . $section_id . ' AND ' . 'articleid=' . $this->id );

    }

    function drop_all_relations( $article_id ) {
        require_once( 'AMP/Content/Section/RelatedSet.inc.php');
        $related_section_set = new SectionRelatedSet( AMP_Registry::getDbcon( ) );
        return $related_section_set->deleteData( 'articleid=' . $article_id );

    }

    function makeCriteriaSet( $value, $criteria_method ) {
        $all_criteria = array( );
        foreach( $value as $single_value ) {
            $all_criteria[] = $this->$criteria_method( $single_value );
        }
        return "( ( " . join( ' ) OR ( ', $all_criteria ) . ") )";
    }

    function makeCriteriaSection( $section_id ) {
        
        if ( is_string( $section_id ) && strpos( $section_id, ',') != 0 ) {
            $section_id = split( ',', $section_id );
        }
        
        if ( is_array( $section_id )) {
            return $this->makeCriteriaSet( $section_id, 'makeCriteriaSection');
        }
        $related_articles = &AMPContentLookup_RelatedArticles::instance( $section_id );
        if ( !$related_articles ) return $this->makeCriteriaPrimarySection( $section_id ) ;

        return '( ' . $this->_makeCriteriaEquals( 'type', $section_id ) 
                    . ' or id in( ' . join( ',', array_keys( $related_articles ) ) . ' ) )';
    }

    function makeCriteriaRelatedSection( $section_id ) {
        $related_articles = &AMPContentLookup_RelatedArticles::instance( $section_id );
        if( !$related_articles ) return false;
        return 'id in ( '. join( ',', $related_articles ). ')';
    }

    function makeCriteriaParent( $section_id ) {
        return $this->makeCriteriaSection( $section_id );
    }

    function makeCriteriaPrimarySection( $section_id ) {
        return $this->_makeCriteriaEquals( 'type', $section_id ) ;
    }

    function makeCriteriaAllowed( ) {
        $allowed_section_names = AMP_lookup( 'sectionMap');
        if ( !$allowed_section_names ) return 'FALSE'; 

        $allowed_sections = array_keys( $allowed_section_names );
        array_unshift( $allowed_sections, AMP_CONTENT_MAP_ROOT_SECTION );
        return 'type in ( ' . join( ',',  $allowed_sections ) . ')';
    }

	function makeCriteriaFulltext( $search_string ) {
        if( !preg_match( '/[-$<>+(~"*)]/', $search_string )) {
            $search_string = '+'.str_replace( ' ', ' +', $search_string );
        }
		$dbcon = AMP_Registry::getDbcon();
        $fulltext_fields = 	array('title', 'shortdesc', 'test', 'contact', 'source', 'author', 'metadescription', 'metakeywords' );
        return "MATCH ( " . join( ",", $fulltext_fields ) . " ) AGAINST ( ". $dbcon->qstr( $search_string ) ."  IN BOOLEAN MODE )";
		
	}

	function makeCriteriaQ( $search_string ) {
        return $this->makeCriteriaFulltext( $search_string );
    }

    function makeCriteriaTag( $tag_id ) {
        if ( is_string( $tag_id ) && ( strpos( $tag_id, ',') )) {
            $tag_id = split( ',', $tag_id );
        }
        
        if ( is_array( $tag_id )) {
            return $this->makeCriteriaSet( $tag_id, 'makeCriteriaTag');
        }
        $tagged_articles = AMPSystem_Lookup::instance( 'articlesByTag', $tag_id );
        if ( !$tagged_articles || empty( $tagged_articles )) return 'FALSE';
        return 'id in( ' . join( ',', array_keys( $tagged_articles )) . ')';
    }

    function makeCriteriaTagSets( $tagsets ) {
        $tagset_criteria = array_map( array( $this, 'makeCriteriaTag'), $tagsets );
        $tagset_criteria_text  = join( " AND ", $tagset_criteria );
        return "( $tagset_criteria_text )";
    }

    function makeCriteriaFrontpage( $value ) {
        if ( !$value ) return false;
        return 'fplink=1';
    }

    function makeCriteriaNew( $value ) {
        if ( !$value ) return false;
        return 'new=1';
    }

    function makeCriteriaSectionLogic( $section_id ) {
        require_once( 'AMP/Content/Section.inc.php');
        $section = &new Section( AMP_Registry::getDbcon( ), $section_id );
        if ( !$section->hasData( )) {
            return 'TRUE';
        }
        $scope = $section->getDisplayCriteria( );
        unset( $scope['displayable']);
        $value = $this->makeCriteria( $scope );
        if ( $value ) {
            return join( ' AND ', $value );
        }
        return 'TRUE';
    }
    function makeCriteriaSectionLogicPlus( $section_id ) {
        $logic = array( );
        $logic[] = $this->makeCriteriaSectionLogic( $section_id );
        $logic[] = $this->makeCriteriaSection( $section_id );
        $logic = array_filter( $logic );
        return '( '.join( ' OR ', $logic ). ')';
    }
    function makeCriteriaSectionLogicAdmin( $section_id ) {
        return $this->makeCriteriaSectionLogicPlus( $section_id );
        /*
        $logic = $this->makeCriteriaSectionLogic( $section_id );
        $excluded_classes = AMP_lookup( 'excluded_classes_for_display' );
        $admin_class = "class in (" . join( ",", $excluded_classes ) . ")" ;
        $section = $this->makeCriteriaPrimarySection( $section_id );
        return "( $logic OR ( $admin_class AND $section ))";
        */
    }

    function makeCriteriaClass( $class_id ){
        if ( is_string( $class_id ) && strpos( $class_id, ',') != 0 ) {
            $class_id = split( ',', $class_id );
        }
        
        if ( is_array( $class_id )) {
            return "class in (".join( ",", $class_id ).")";
        }
        return $this->_makeCriteriaEquals( 'class', $class_id );
    }

    function makeCriteriaFilter( $filter_def ) {
        $filter_name = is_array( $filter_def ) ? $filter_def['name'] : $filter_def;
        $filter_var = is_array( $filter_def ) ? $filter_def['var'] : null;

        if ( !( $filter = $this->_load_filter( $filter_name, $filter_var ))) return false;
        return $filter->criteria;
    }

    function _load_filter( $filter_name, $filter_var ) {
        $filter_class = 'ContentFilter_' . ucfirst( $filter_name );
        if ( !class_exists( $filter_class )) {
            $filter_filename = ucfirst( $filter_name ) . '.inc.php';
            $filter_path = 'AMP/Content/Article/Filter/'. $filter_filename;
            if ( !file_exists_incpath( $filter_path )) {
                if ( !( $filter_path = file_exists_incpath( $filter_filename ))) {
                    return false;
                }
            }
            include_once( $filter_path );
            if ( !class_exists( $filter_class )) return false;
        }
        $filter = new $filter_class( $filter_var );
        $filter->assign( );
        return $filter;
    }


	function makeCriteriaDate( $date_value ) {
        if ( !is_array( $date_value ) && $date_value ) {
            return "UNIX_TIMESTAMP( date ) = " . $date_value;
        }
        $partial_date_crit = array( );
        if ( isset( $date_value['Y']) && $date_value['Y'] ) {
            $partial_date_crit[] = 'YEAR( date ) = ' .$date_value['Y'];
        }
        if ( isset( $date_value['M']) && $date_value['M'] ) {
            $partial_date_crit[] = 'MONTH( date ) = ' . $date_value['M'];
        }
        if ( isset( $date_value['d']) && $date_value['d'] ) {
            $partial_date_crit[] = 'DAY( date ) = ' . $date_value['d'];
        }
        if ( empty( $partial_date_crit )) return 'TRUE';
        return join( ' AND ', $partial_date_crit );
    }

    function makeCriteriaAfterDate( $date_value ) {
        if ( !is_array( $date_value ) && $date_value ) {
            return "UNIX_TIMESTAMP( date ) >= " . $date_value;
        }
        $partial_date_crit = array( );
        if ( isset( $date_value['Y']) && $date_value['Y'] ) {
            $partial_date_crit[] = 'YEAR( date ) >= ' .$date_value['Y'];
        }
        if ( isset( $date_value['M']) && $date_value['M'] ) {
            $partial_date_crit[] = 'MONTH( date ) >= ' . $date_value['M'];
        }
        if ( empty( $partial_date_crit )) return 'TRUE';
        return join( ' AND ', $partial_date_crit );

    }

    function makeCriteriaType( $section_id ) {
        return $this->makeCriteriaSection( $section_id );
    }

    function makeCriteriaRegion( $region_id ) {
        return $this->_makeCriteriaEquals('state', $region_id ); 
    }

    function makeCriteriaDisplayableFrontpage(  ) {
        $class = $this->makeCriteriaDisplayableClass(  );
        $fp = $this->makeCriteriaDisplayableClassFrontpage( );
        return str_replace( $class, $fp, $this->makeCriteriaDisplayable( ) );
    }

    function makeCriteriaDisplayable(  ) {
        $crit = array(  );
        $crit['class'] = $this->makeCriteriaDisplayableClass(  );
        $crit['status'] = $this->makeCriteriaLive(  );
        $crit['section_status'] = $this->makeCriteriaLiveParent(  );
        $crit['allowed'] = $this->makeCriteriaAllowed(  );
        $crit['public']= $this->makeCriteriaPublicToUser(  );
        return join (  " AND ", array_filter( $crit ));

    }

    function makeCriteriaDisplayableClass(  ) {
        /*
        $excluded_classes = array( 
            AMP_CONTENT_CLASS_SECTIONHEADER,
            AMP_CONTENT_CLASS_USERSUBMITTED,
            AMP_CONTENT_CLASS_FRONTPAGE
            );
        */
        $excluded_classes = AMP_lookup( 'excluded_classes_for_display' );
        return "class not in (" . join( ",", $excluded_classes ) . ")" ;
    }

    function makeCriteriaLiveParent( ){
		if(!($draft_sections = AMP_lookup( 'sections_draft' ))) return FALSE;
        return "type not in( " . join( ",", array_keys( $draft_sections) ) . ")";
    }

    function makeCriteriaDisplayableClassFrontpage( ) {
        $excluded_classes = AMP_lookup( 'excluded_classes_for_display' );
        $excluded_classes_fp = array_diff( $excluded_classes, array( AMP_CONTENT_CLASS_FRONTPAGE ));
        return "class not in (" . join( ",", $excluded_classes_fp ) . ")" ;

    }

    function makeCriteriaStatus( $value ) {
        if ( !( $value || $value==='0')) return false;
        return ( 'publish='.$value ) ;
    }

    function makeCriteriaPublicToUser(  ) {
        if ( AMP_Authenticate( 'content') ) return false;
        return $this->makeCriteriaPublic( );
    }

    function makeCriteriaPublic(  ) {
        $protected_sections = AMPContent_Lookup::instance( 'protectedSections');
        if ( empty( $protected_sections )) return false;
        return 'type not in( '. join( ',', array_keys( $protected_sections) ) .' )';
    }

    function makeCriteriaSectionOrClass( $section_id, $class_id ) {
        $section_crit = $this->makeCriteriaSection( $section_id );
        $class_crit = $this->makeCriteriaClass( $section_id );
        return "( ".$class_crit . " OR " . $section_crit . ")";
    }

    function makeCriteriaNotClass( $class_id ) {
        if ( !$class_id ) return false;
        if ( is_array( $class_id ) && !empty( $class_id )) return ( 'class not in ( ' . join( ',', $class_id ) . ' )');
        return ( 'class!=' . $class_id ) ;
    }

    function makeCriteriaNotTag( $tag_id ) {
        return "!( ".$this->makeCriteriaTag( $tag_id ) . ")";
    }

    function makeCriteriaNotSection( $section_id ) {
        return "!( ".$this->makeCriteriaSection( $section_id ) . ")";
    }

    function makeCriteriaInSectionDescendant( $section_id ) {
        $base_section = $this->makeCriteriaSection( $section_id );
        $map = AMPContent_Map::instance( );
        if ( !( $child_ids = $map->getDescendants( $section_id ))) return $base_section;

        foreach( $child_ids as $child_id ) {
            $child_sections[] = $this->makeCriteriaSection( $child_id );
        }
        $child_sections_criteria = '( '. join( ') OR ( ', $child_sections ) . ')';
        return '( '.$child_sections_criteria. ' OR ' . $base_section . ')';
    }

    function getMetaDescription( ){
        $result = $this->getData( 'metadescription');
        if ( $result ) return $result;

        return $this->getBlurb( );
    }

    function getMetaKeywords( ){
        return $this->getData( 'metakeywords');
    }

    function get_url_edit( ) {
        return AMP_Url_AddVars( AMP_SYSTEM_URL_ARTICLE_EDIT, array( 'id=' . $this->id ) );
    }

    function isVersion( ) {
        return $this->_version_status;
    }

    function getMediaHtml( ) {
        return $this->getData( 'media_html');
    }

    function getMediaThumbnailUrl( ) {
        if ( $url = $this->getData('media_thumbnail_url')) return $url;
        if ( !( $html = $this->getMediaHtml( ))) return false;
        preg_match( '/src=[^>]*youtube.com\/v\/([^>"\'&]+)/', $html, $youtube_id );
        if ( isset($youtube_id[1]) && $youtube_id[1]) {
            return sprintf( AMP_CONTENT_MEDIA_URL_YOUTUBE_THUMBNAIL, $youtube_id[1]);
        }
    }

    function showMediaThumbnail() {
        return !($this->getData('media_list_image'));
    }

    function getMediaUrl( ) {
        if( $url = $this->getData('media_filename' )) return $url;
        if ( !( $html = $this->getMediaHtml( ))) return false;
        preg_match( '/src=[^>]*youtube.com\/v\/([^>"\']+)/', $html, $youtube_id );
        if ( isset($youtube_id[1]) && $youtube_id[1]) {
            return sprintf( AMP_CONTENT_MEDIA_URL_YOUTUBE, $youtube_id[1]);
        }
        return false;
    }

	function export_keys() {
		$do_not_export = array( 'test', 'subtitile', 'shortdesc', 'type', 'picture' );
		$keys = parent::export_keys();
		return array_diff( $keys, $do_not_export );
	}

    function request_revision( $comments = false ) {
        $updated_values = array( 'publish' => AMP_CONTENT_STATUS_REVISION );
        if ( $comments ) {
            $user_names = AMP_lookup( 'users' );
            $current_user = $user_names[AMP_SYSTEM_USER_ID];

            $existing_notes = $this->getData( 'notes' );
            $new_notes = sprintf( AMP_TEXT_REVISION_COMMENTS_HEADER, date( 'Y-m-d'), $current_user ) . "\n"
                        . $comments . "\n"
                        . str_repeat( '-', 30 ) . "\n"
                        . $existing_notes;

            $updated_values['notes'] = $new_notes;
        }
        $this->mergeData( $updated_values ); 

        $this->list_action= 'request_revision';
        $result = $this->save_without_callbacks( );

        $this->notify( 'update' );
        $this->notify( 'to_revision' );

        return $result;

    }

    function getPublish( ){
        return $this->getStatus( ) ;
    }

}


?>
