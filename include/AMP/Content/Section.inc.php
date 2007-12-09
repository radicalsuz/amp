<?php

require_once ( 'AMP/System/Data/Item.inc.php' );
require_once ( 'AMP/Content/Image.inc.php' );
require_once ( 'AMP/Content/Section/Contents/Manager.inc.php' );
require_once ( 'AMP/Content/Article.inc.php');

class Section extends AMPSystem_Data_Item {

    var $datatable = "articletype";
    var $name_field = "type";
    var $_contents;
    var $_class_name = 'Section';
    var $_field_status = 'usenav';
    var $display;

    var $_custom_config = array( 
            'search_custom' => 'AMP_RENDER_SEARCH_LIST_SECTION_%s',
            'item_custom_method' => 'AMP_RENDER_ITEM_LIST_SECTION_%s',
            'header_custom_method' => 'AMP_RENDER_HEADER_LIST_SECTION_%s'
        );

    function Section( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );
    }

    function _after_init( ) {
        foreach( $this->_custom_config as $local_key => $constant_pattern ) {
            $this->_addAllowedKey( $local_key );
        }
    }

    function _afterRead( ) {
        $this->_read_custom_config( );
    }

    function _read_custom_config( ) {
        foreach( $this->_custom_config as $local_key => $constant_pattern ) {
            if( !defined( sprintf( $constant_pattern, $this->id ))) continue;
            $value = constant( sprintf( $constant_pattern, $this->id ));
            if( $value ) $this->mergeData( array( $local_key => $value ));
        }
    }

    /*
    function _adjustSetData( $data ) {
        if( isset( $data['id']) && $data['id'] && ( $data['id'] != $this->id ) ) {
            
        }
        AMP_dump( $data );
        return $data;

    }
    */

    function &getContents() {
        if (isset($this->_contents)) return $this->_contents;

        $this->_contents = &new SectionContents_Manager( $this );
        return $this->_contents;
    }

    function getCriteriaForContent() {
        return join( ' AND ', $this->makeCriteria( $this->getDisplayCriteria( )));
        #$contentsource = & $this->getContents();
        #return $contentsource->getSectionCriteria();
    }

    function &getDisplay() {
        if( !$this->showContentList( )) return  $this->getDisplayIntro( );
        $display_class = $this->getDisplayClass( );
        $display_class_vars = get_class_vars( $display_class );
        if (!isset( $display_class_vars['api_version'] ) || ( $display_class_vars['api_version'] == 1)) {
            $contents = &$this->getContents();
            $this->display = $contents->getDisplay();
            return $this->display;
        }
        $this->display = &new $display_class( 
                                $this,
                                $this->getDisplayCriteria( ),
                                $limit = ((( isset( $_REQUEST['all']) && $_REQUEST['all']) 
                                         ||(isset( $_REQUEST['qty']) && $_REQUEST['qty']))
                                                ? null : $this->getListItemLimit( ))
                                );
        if( $display_method = $this->getCustomItemDisplay( )) {
            $this->display->set_display_method( $display_method );
        }
        return $this->display;
    }

    function getDisplayIntro( ) {
        if( $custom = $this->getCustomHeaderDisplay( ) AND function_exists( $custom )) {
            return AMP_to_buffer( $custom( $this ));
        }
        return $this->getHeaderArticle( )    ;
    }

    function getHeaderArticle( ) {
        if( !( $intro = $this->getHeaderRef( ))) {
            $intro = new Article( AMP_Registry::getDbcon( ));
            $intro->setDefaults( );
            $intro->mergeData(  array( 
                'publish'   => 1, 
                'title'     => $this->getName( ) . $this->getListNameSuffix( ), 
                'body'      => $this->getBlurb( ), 
                'class'     => AMP_CONTENT_CLASS_SECTIONHEADER 
                ));
        }
        return $intro->getDisplay( );

    }

    function display() {
        $display = &$this->getDisplay();
        return $display->execute();
    }

    function &getHeaderRef() {
        $empty_value = false;
        if (!$this->getData( 'header' )) return $empty_value;
        if ($id = $this->getHeaderTextId() ) {
            $result = &new Article( $this->dbcon, $id );
            return $result;
        }

        if (!($headers = &AMPContent_Lookup::instance( 'sectionHeaders' ))) return $empty_value;

        if (isset( $headers[ $this->id ] )) {
            $result = &new Article( $this->dbcon, $headers[ $this->id ] );
            return $result;
        }
        return $empty_value;
    }

    function &getFooterRef() {
        $empty_value = false;
        if (!AMP_CONTENT_CLASS_SECTIONFOOTER) return $empty_value;
        if (!($footers = &AMPContent_Lookup::instance( 'sectionFooters' ))) return $empty_value;

        if (isset( $footers[ $this->id ] )) {
            $result = &new Article( $this->dbcon, $footers[ $this->id ] );
            return $result;
        }
        return $empty_value;
    }

    function getHeaderTextId() {
        if (!$this->getData( 'header' )) return false;
        if (!($id =  $this->getData( 'url' ))) return false;
        if ( $id == 1 ) return false;
        return $id;
    }

    function showContentList() {
        return !($this->getData('usetype'));
    }

    function getParent() {
        return $this->getData( 'parent' );
    }

    function getSecured() {
        return $this->getData( 'secure' );
    }

    function getBlurb() {
        return $this->getData('description' );
    }

    function getSectionDate() {
        if (!($value = $this->getData( 'date2' ))) return false;
        if (!AMP_verifyDateValue( $value )) return false;
        return $value;
    }

    function getItemDate() {
        if ( !$this->isColumn( 'date_display')) return $this->getSectionDate();
        if ( $this->getData( 'date_display' )) return  $this->getSectionDate();
        return false;
    }

    function getTemplate() {
        return $this->getData( 'templateid' );
    }

    function getTitle() {
        //convenience alias
        return $this->getName();
    }

    function getURL() {
        if ($url = $this->getRedirect() ) return $url;
        if (!$this->id ) return false;
        return $this->getURL_default( );
    }

    function getURL_default( ) {
        return AMP_Url_AddVars( AMP_CONTENT_URL_LIST_SECTION, "type=".$this->id );
    }
    
    function getExistingAliases( ){
        if ( !isset( $this->id )) return false;
        require_once( 'AMP/Content/Redirect/Redirect.php' );
        $redirect = &new AMP_Content_Redirect( $this->dbcon );
        return $redirect->search( $redirect->makeCriteria( array( 'target' => $this->getURL_default( ))));
    }

    function getStylesheet() {
        return $this->getData( 'css' );
    }

    function getListItemLimit() {
        return $this->getData( 'up' );
    }

    function getListFilter( ){
        return $this->getData( 'filter' );
    }

    function getListType() {
        return $this->getData( 'listtype');
    }

    function getRedirect() {
        //if (!$this->getData('uselink')) return false;
        if (!( $target = $this->getData('linkurl'))) return false;
        return $target;
    }

    function getHeaderRedirect() {
        if (!($article = &$this->getHeaderRef())) return false;
        return $article->getRedirect();
    }

    function &getImageRef() {
        $empty_value = false;
        if (! ($img_path = $this->getImageFileName())) return $empty_value;
        $image = &new Content_Image( $img_path );
        return $image;
    }

    function get_image_banner( ) {
        return $this->getData( 'flash');
    }

    function getOrder( ){
        return $this->getData( 'textorder');
    }

    function getListOrder( ) {
        return $this->getOrder( );
    }

    function getImageFileName() {
        return $this->getData( 'image2' );
    }


    function _sort_default( &$item_set ){
        #require_once( 'AMP/Content/Map.inc.php');
        #$map = &AMPContent_Map::instance( );
        if ( !( $order_base = AMP_lookup( 'sectionMap'))) return false;
        $order = array_keys( $order_base );
        $item_set = array_combine_key( $order, $item_set );
    }

    function reorder( $new_order_value ){
        if ( $new_order_value == $this->getOrder( )) return false;
        $this->setOrder( $new_order_value );
        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'reorder' );
        AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );
        return $result;

    }
    
    function getNavIndex( ){
        if ( isset( $this->_nav_index )) return $this->_nav_index;
        $navs_lists =       AMPContent_Lookup::instance( 'sectionListsNavigationCount');
        $navs_content =     AMPContent_Lookup::instance( 'sectionContentNavigationCount');
        $this->_nav_index = ( isset( $navs_content[ $this->id ]) ? $navs_content[$this->id] : 0 )
                     + ( isset( $navs_lists[ $this->id ]) ? $navs_lists[ $this->id ] : 0 );
        return $this->_nav_index;
    }

    function _afterSave( ){
        $this->_save_custom_config( );
        if ( $this->id != $this->dbcon->Insert_ID( )) return;
        $this->_create_section_header( );
        $this->_create_permission_values( );
    }

    function _save_custom_config( ) {
        foreach( $this->_custom_config as $local_key => $constant_pattern ) {
            $config_constant = sprintf( $constant_pattern, $this->id );
            $new_value = $this->getData( $local_key );
            //if the constant is defined and doesn't match the new value, redefine it'
            if( defined( $config_constant ) && ( constant( $config_constant ) != $new_value )) {
                AMP_config_write( $config_constant, $new_value ); 
                continue;
            }
            //if a value has been defined and no constant exists, write it to the custom folder
            if( $new_value && !defined( $config_constant ) ) {
                AMP_config_write( $config_constant, $new_value );
            }
        }
        /*
        if( defined( 'AMP_RENDER_SEARCH_LIST_SECTION_'.$this->id)) {
            if( constant( 'AMP_RENDER_SEARCH_LIST_SECTION_' . $this->id ) != $this->getData( 'search_custom')) {
                AMP_config_write( 'AMP_RENDER_SEARCH_LIST_SECTION_' . $this->id , $this->getData( 'search_custom'));
            }
        } elseif ( $this->getData( 'search_custom')) {
            AMP_config_write( 'AMP_RENDER_SEARCH_LIST_SECTION_' . $this->id, $this->getData( 'search_custom'));
        }
        */
        
    }

    function _create_permission_values( ) {
        $groups = AMP_lookup( 'permissionGroups');
        foreach( $groups as $group_id => $name ) {
            $allowed_sections_source = new AMPSystemLookup_SectionsByGroup( $group_id );
            $allowed_sections = $allowed_sections_source->dataset; //AMP_lookup( 'sectionsByGroup', $group_id );
            if ( !$allowed_sections ) {
                //all sections are allowed this group by default
                continue;
            }
            
            //if group has restrictions
            $parent_id = $this->getParent( );
            $current_user = AMP_current_user( );
            if ( $current_user && ( $current_user->getGroup( ) == $group_id )) {
                $allow_new_section = true;
            } elseif ( $parent_id == AMP_CONTENT_MAP_ROOT_SECTION ) {
                $map = AMPContent_Map::instance( );
                $siblings = $map->getChildren( AMP_CONTENT_MAP_ROOT_SECTION );
                $allowed_siblings = array_combine_key( $siblings, $allowed_sections );
                $allow_new_section = ( count( $siblings ) == count( $allowed_sections ));
            } else {
                $allow_new_section = isset( $allowed_sections[ $parent_id ]);
            }

            if ( $allow_new_section ) {
                require_once( 'AMP/System/Permission/Item/Item.php');
                AMP_System_Permission_Item::create_for_group( $group_id, 'access', 'section', $this->id );

            }

        }

        AMP_permission_update( );
    }

    function _create_section_header( ) {
        require_once( 'AMP/Content/Article.inc.php');
        $section_header = &new Article( $this->dbcon );
        $section_header->setDefaults( );
        $article_data = array( 
            'title' =>  $this->getName( ),
            'body' => $this->getBlurb( ),
            'publish'   => true,
            'class'     => AMP_CONTENT_CLASS_SECTIONHEADER,
            'section'      => $this->id );
        $section_header->setData( $article_data );
        return $section_header->save( );

    }

    function setBlurb( $blurb ){
        return $this->mergeData( array( 'description' => $blurb ));
    }

    function setName( $name ){
        return $this->mergeData( array( 'type' => $name ));
    }

    function setParent( $parent_id = AMP_CONTENT_MAP_ROOT_SECTION ) {
        return $this->mergeData( array( 'parent' => $parent_id ));
    }

    function setListType( $listtype = AMP_SECTIONLIST_ARTICLES ) {
        return $this->mergeData( array( 'listtype' => $listtype ));
    }

    function setOrder( $order ) {
        return $this->mergeData( array( 'textorder' => $order ));
    }


    function setDefaults( ) {
        $this->setListType( );
    }

    function move( $section_id ){
        if ( !( $section_id && $section_id != $this->getParent( ))) {
            return false ;
        }

        $this->setParent( $section_id );

        if ( !( $result = $this->save( ))) return false;
        $this->notify( 'update' );
        $this->notify( 'move' );
        AMP_cacheFlush( AMP_CACHE_TOKEN_LOOKUP );

        return $result;

    }

    function get_url_edit( ) {
        if ( !isset( $this->id )) return false;
        return AMP_URL_AddVars( AMP_SYSTEM_URL_SECTION, array( 'id=' . $this->id ));
    }

    function isLive() {
        if ( !parent::isLive( )) return false;

        $map = AMPContent_Map::instance( );
        $ancestors = $map->getAncestors( $this->id );
        $live_set = AMP_lookup( 'sectionsLive' );
        foreach( $ancestors as $parent_id => $parent_name ) {
            if ( !isset( $live_set[ $parent_id ])) {
            return false;

            }
        }

        return true;
    }

    function getStatus( ) {
        return $this->isLive( ) ? AMP_PUBLISH_STATUS_LIVE : AMP_PUBLISH_STATUS_DRAFT;
    }

    function makeCriteriaParent( $parent_id ) {
        if( empty( $parent_id )) return TRUE;
        if( is_array( $parent_id )) {
            return 'parent in( '.join( ',', $parent_id ).' )';
        }
        return 'parent=' . $parent_id;
    }

    function makeCriteriaGrandparent( $section_id ) {
        $map = AMPContent_Map::instance( );
        $parents = $map->getChildren( $section_id );
        if( empty( $parents )) return 'FALSE';
        return $this->makeCriteriaParent( $section_id );

    }

    function makeCriteriaSection( $section_id ) {
        return $this->makeCriteriaParent( $section_id );
    }

    function makeCriteriaDisplayable( ){
        $crit['status'] = $this->makeCriteriaLive(  );
        $crit['allowed'] = $this->makeCriteriaAllowed(  );
        $public = $this->makeCriteriaPublic(  );
        if ( $public ) {
            $crit['public'] = $public;
        }
        return join( ' AND ', $crit );
    }

    function makeCriteriaPublic(  ) {
        $protected_sections = AMPContent_Lookup::instance( 'protectedSections');
        if ( empty( $protected_sections )) return false;
        return 'type not in( '. join( ',', array_keys( $protected_sections) ) .' )';
    }

    function getDisplayCriteria( ) {
        $sections = $this->getData( 'list_by_section');
        $classes = $this->getData( 'list_by_class');
        $tags = $this->getData( 'list_by_tag');
        $global = $this->getData( 'list_is_global');
        
        $display_criteria = array( 'displayable' => 1 );
        if ( !$global ) $display_criteria['section'] = $this->id;
        if ( $classes ) $display_criteria['class'] = preg_split( '/\s?,\s?/', $classes );
        if ( $tags ) $display_criteria['tag'] = preg_split( '/\s?,\s?/', $tags );
        if ( $sections )  {
            $specified_sections = preg_split( '/\s?,\s?/', $sections );
            if ( isset( $display_criteria['section'])) {
                $specified_sections[] = $display_criteria['section'];
            }
            $display_criteria['section'] = $specified_sections; 
        }

        return $display_criteria;
    }

    function getDisplayClass( ) {
        $displays = AMP_lookup( 'section_displays');
        $default = AMP_SECTION_DISPLAY_DEFAULT;
        if( !isset( $displays[$this->id])) return $default;
        if( !class_exists( $displays[$this->id])) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $displays[$this->id]));
            return $default;
        }
        return $displays[$this->id];
    }


    function delete( ){
        return $this->trash( );
    }

    function trash( ) {
        $section_set = $this->getSearchSource( );
        $result = $section_set->updateData( array( 'parent='.AMP_CONTENT_SECTION_ID_TRASH ), 'id=' . $this->id );
        if ( !$result ) return false;

        //send a notice about the child sections
        $map = AMPContent_Map::instance( );
        $children = $map->getDescendants( $this->id );
        $this->trash_contents( $this->id );

        if ( !empty( $children )) {
            foreach( $children as $child_id ) {
                $this->trash_contents( $child_id );
            }
        }
        $this->notify( 'trash', $this->id );
        return ( count( $children ) + 1 );

    }

    function trash_contents( $section_id ) {
        require_once( 'AMP/Content/Article.inc.php');
        $article = new Article( $this->dbcon );
        
        $article_source = $article->getSearchSource( );
        $article_count = $article_source->updateData( array( 'publish='.AMP_CONTENT_STATUS_DRAFT ), $article->makeCriteriaPrimarySection( $section_id ) );

        $article_set = AMP_lookup( 'articlesByPrimarySection', $section_id );
        foreach( $article_set as $id => $title ) {
            $relations_count = Article::drop_all_relations( $id );
        }
        
    }

    function makeCriteriaAllowed( ) {
        $allowed_section_names = AMP_lookup( 'sectionMap');
        if ( !$allowed_section_names ) return 'FALSE'; 

        $allowed_sections = array_keys( $allowed_section_names );
        array_unshift( $allowed_sections, AMP_CONTENT_MAP_ROOT_SECTION );
        return 'parent in ( ' . join( ',',  $allowed_sections ) . ') and id!='. AMP_CONTENT_SECTION_ID_TRASH;
    }

    function getListNameSuffix( ) {
        return AMP_format_date_from_array( AMP_date_from_url( ));
    }

    function getListSort( ) {
        $value = $this->getData( 'list_sort');
        if ( $value ) return $value;
        return 'ordered';
    }

    function getAllowSearchDisplay( ) {
        return $this->getData( 'search_display');
    }

    function getCustomSearch( ) {
        if( !( $this->id && defined( 'AMP_RENDER_SEARCH_LIST_SECTION_' . $this->id) )) return false;
        return constant(  'AMP_RENDER_SEARCH_LIST_SECTION_' . $this->id );
    }

    function getCustomItemDisplay( ) {
        if( !( $this->id && defined( 'AMP_RENDER_ITEM_LIST_SECTION_' . $this->id) )) return false;
        return constant(  'AMP_RENDER_ITEM_LIST_SECTION_' . $this->id );
    }

    function getCustomHeaderDisplay( ) {
        if( !( $this->id && defined( 'AMP_RENDER_HEADER_LIST_SECTION_' . $this->id) )) return false;
        return constant(  'AMP_RENDER_HEADER_LIST_SECTION_' . $this->id );
    }
}
?>
