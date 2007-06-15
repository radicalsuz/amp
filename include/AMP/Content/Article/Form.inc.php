<?php

require_once( 'AMP/System/Form/XML.inc.php');

class Article_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';
    var $_field_custom_defs = array( 
        'name'  => 'AMP_CONTENT_ARTICLE_CUSTOMFIELD_NAME',
        'label' => 'AMP_CONTENT_ARTICLE_CUSTOMFIELD_LABEL',
        'type'  => 'AMP_CONTENT_ARTICLE_CUSTOMFIELD_TYPE',
        'default'  =>  'AMP_CONTENT_ARTICLE_CUSTOMFIELD_DEFAULT',
    );
    var $_custom_field_path = 'AMP/Content/Article/CustomFields.xml';
    var $_custom_field_limit = 4;
    var $_custom_field_header = array( 
        'custom_field_header' => array( 
            'label' => 'Custom AMP Fields',
            'type'  => 'header'
        )
    );

    function Article_Form( ) {
        $name = "article";
        $this->init( $name, 'POST', AMP_SYSTEM_URL_ARTICLE_EDIT );
    }

    function setDynamicValues() {
        $this->addTranslation( 'section',      '_checkNewSection',  'get');

        $this->addTranslation( 'image_upload', '_manageUpload',     'get');
        $this->addTranslation( 'picture',      '_checkUploadImage', 'get');

        $this->addTranslation( 'doc_upload',   '_manageUpload',     'get');
        $this->addTranslation( 'doc',          '_checkUploadFile',  'get');

        $this->addTranslation( 'link',         '_checkToolLink',    'get');
        $this->addTranslation( 'link',         '_checkDocLink',     'get');

        $this->addTranslation( 'wysiwyg_setting','_returnBlankCheckbox',  'get');
        $this->addTranslation( 'wysiwyg_setting','_checkWysiwyg',  'get');
        $this->addTranslation( 'wysiwyg_setting','_evalWysiwyg',  'set');

        $this->addTranslation( 'date',         '_makeDbDateTime',   'get');
        $this->addTranslation( 'date',         '_makeNullDate',   'set');

        $this->addTranslation( 'sections_related',   '_getRelatedSections', 'set');
        $this->addTranslation( 'sections_related',   '_getMultiSelectBlanks', 'get');

        $this->addTranslation( 'tags',   '_getTags', 'set');
        $this->addTranslation( 'tags',   '_getMultiSelectBlanks', 'get');
        $this->addTranslation( 'tags',   '_assembleTags', 'get');

        $this->addTranslation( 'transfer_mode_setting','_returnBlankCheckbox',  'get');
        $this->addTranslation( 'transfer_mode_setting','_checkTransferMode',  'get');
        $this->addTranslation( 'transfer_mode_setting','_evalTransferMode',  'set');

        //$this->setFieldValueSet( 'doc', AMPfile_list( 'downloads'));
        //$this->_initJavascriptActions( );
        //$this->HTMLEditorSetup( );
    }

    function _initJavascriptActions( ){
        $header = &AMP_getHeader( );
        $this->_initTabDisplay( $header );
        $this->_initAutoLookups( $header );
        $this->_initTransferMode( $header );
        $this->HTMLEditorSetup( );
    }

    function _initTransferMode( &$header ){
        if ( AMP_USER_CONFIG_CONTENT_MODE_TRANSFER ) {
            $header->addJavascriptOnload( 'window.change_all_blocks( );');
        }

    }

    function _initAutoLookups( &$header ){
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "author", "author_list", "ajax_request.php", {} );');
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "source", "source_list", "ajax_request.php", {} );');
        
    }

    function _makeNullDate( $data, $fieldname ) {
        if ( AMP_verifyDateValue( $data[$fieldname]) ) return $data[$fieldname];
        return '';
    }

    function _configHTMLEditor( &$editor ){
        $editor->height = '600';
    }

	/*
    function execute( ){
        $value = parent::execute( );
        return $value;
    }
	 */

    function _initTabDisplay( &$header ){
        $header->addJavaScript( 'scripts/tabs.js', 'tabs');
        
        $header->addJavascriptOnload( 
            'current_tab = document.getElementById( "tab_0" );'."\n"
            .'if ( current_tab ) Tabs_highlight( current_tab ) ;'
            );
        
    }


    function _selectAddNull( $valueset, $name ) {
        $required_selects = array( 'section', 'new_section_parent');
        if ( array_search( $name, $required_selects ) === FALSE ) return parent::_selectAddNull( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --') + $valueset;
    }

    function _blankValueSet( $valueset, $name ){
        $required_selects = array( 'section', 'new_section_parent');
        if ( array_search( $name, $required_selects ) === FALSE ) return parent::_blankValueSet( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --');
    }

    function _checkNewSection( $data, $fieldname ){
        if ( ! ( isset( $data['new_section_name'] ) && $data['new_section_name'] )) {
            if ( !isset( $data[$fieldname ])) return false;
            return $data[ $fieldname ];
        }
        require_once( 'AMP/Content/Section.inc.php');
        $section = &new Section( AMP_Registry::getDbcon( ) );
        $section->setDefaults( );
        $section->setName( $data['new_section_name']);
        $section->setParent( $data['new_section_parent'] );

        if ( !( $result = $section->save( ))) return $data[ $fieldname ];
        $section->publish( );

        $flash = &AMP_System_Flash::instance( );
        $flash->add_message( sprintf( AMP_TEXT_DATA_SAVE_SUCCESS, $section->getName( )));
        return $section->id;

    }

    function _checkUploadImage( $data, $fieldname ){
        if ( ! ( isset( $data['image_upload'] ) && $data['image_upload'] )) {
            if ( !isset( $data[$fieldname ])) return false;
            return $data[ $fieldname ];
        }
        if ( ! ( isset( $data['image_gallery']) && $data['image_gallery'])) {
            return $data['image_upload'];
        }
        require_once( 'Modules/Gallery/Image.inc.php' );
        $gallery_record = &new GalleryImage( AMP_Registry::getDbcon( ) );
        $gallery_data = array( 
            'img'       => $data['image_upload'],
            'caption'   => ( isset( $data['piccap'] ) ? $data['piccap'] : false ), 
            'galleryid' => $data['image_gallery'],
            'section'   => $data['section'],
            'publish'   => true,
            'date'      => date( 'Y-m-d' )
        );

        $gallery_record->setData( $gallery_data );
        $gallery_record->save( );
        return $data['image_upload'];
    }

    function _initUploader( $data, $fieldname, &$upLoader ){
        if ( $fieldname != 'image_upload' ) return;
        $upLoader->setFolder( AMP_CONTENT_URL_IMAGES . AMP_IMAGE_CLASS_ORIGINAL );
    }

    function _checkUploadFile( $data, $fieldname ) {
        if ( ! ( isset( $data['doc_upload'] ) && $data['doc_upload'] )) {
            if ( !isset( $data[$fieldname ])) return false;
            return $data[ $fieldname ];
        }
        return $data['doc_upload'];
    }

    function _checkToolLink( $data, $fieldname ) {
        if ( ! ( isset( $data['tool_page_link'] ) && $data['tool_page_link'] )) {
            if ( !isset( $data[$fieldname ])) return false;
            return $data[ $fieldname ];
        }
        return $data['tool_page_link'];
    }

    function _checkDocLink( $data, $fieldname ) {
        if ( ! ( isset( $data['doc_direct_link'] ) && $data['doc_direct_link'] )) {
            if ( !isset( $data[$fieldname ])) return false;
            return $data[ $fieldname ];
        }
        if ( !( isset( $data['doc'])) && $data['doc']) return $data[$fieldname];
        return AMP_CONTENT_URL_DOCUMENTS . $data['doc'];

    }

    function adjustFields( $fields ){
        $fields['comment_list']['default'] = $this->_getCommentListOutput( $this->getIdValue( ));
        $fields = array_merge( $fields, $this->_defineCustomFields( ));
        return $fields;
    }

    function _defineCustomFields( ){

        $custom_fields = false;
        if ( file_exists_incpath( 'Article_Custom_Fields.xml' )) {
            $custom_fields = $this->_readCustomFields( 'Article_Custom_Fields.xml');
        }
        if ( file_exists_incpath( 'AMP/Content/Article/Custom_Fields.xml' )) {
            $custom_fields = $this->_readCustomFields( 'AMP/Content/Article/Custom_Fields.xml');
        }
        if ( !$custom_fields ) {
            $custom_fields = $this->_defineLegacyCustomFields( );
        }

        if ( !$custom_fields ) return array( );

        $custom_fields = array_merge( $this->_custom_field_header, $custom_fields);
        return $custom_fields;
    }

    function _defineLegacyCustomFields( ){
        //legacy definition method
        $custom_fields = array( );
        for( $n=1; $n<=$this->_custom_field_limit; $n++) {
            $field_name_constant = $this->_field_custom_defs['name'] . '_' . $n;
            if ( !defined( $field_name_constant ) ) continue;
            $current_field = array( );

            foreach( $this->_field_custom_defs as $key => $constant_base ){
                $current_constant = $constant_base . '_' . $n;
                if ( !defined( $current_constant) ) continue;
                $current_field[ $key ] = constant( $current_constant );
            }

            $custom_fields[ constant( $field_name_constant ) ] = $current_field;
        }
        if ( empty( $custom_fields )) return false;
        return $custom_fields;

    }

    function _readCustomFields( $file_name ){
        $fieldsource = & new AMPSystem_XMLEngine( $file_name );
        if ( $fields = $fieldsource->readData() ) return $fields;

        return false;
        
    }

    function _getCommentListOutput( $id ){
        $commentList = &$this->_getCommentList( $id );
        if ( is_object( $commentList )) return $commentList->execute( );
        return $commentList;
    }

    function &_getCommentList( $id ) {
        $empty_value = AMP_TEXT_SEARCH_NO_MATCHES;
        if ( isset( $this->_commentList )) return $this->_commentList;
        if ( !$id ) return $empty_value;
        require_once( 'AMP/Content/Article/Comment/List_Basic.inc.php');

        $commentList = &new AMP_Content_Article_Comment_List_Basic( AMP_Registry::getDbcon( ), array( 'article' => $id ));
        $commentList->setEditLinkTarget( 'blank' );
        $commentList->appendAddLinkVar( 'article_id='.$id );
        $this->_commentList = &$commentList;
        return $commentList;
    }

    function _getRelatedSections( $data, $fieldname ) {
        $id = ( isset( $data['id']) && $data['id']) ? $data['id'] : false;
        if ( !$id ) return false;

        $related_sections = AMPContentLookup_SectionsByArticle::instance( $id );

        if ( !$related_sections ) return false;
        return join( ',', array_keys( $related_sections ) );

    }

    function _getMultiSelectBlanks( $data, $fieldname ) {
        if ( !isset( $data[$fieldname ])) {
            return array( );

        }
        return $data[$fieldname];
    }

    function _getTags( $data, $fieldname ) {
        $id = ( isset( $data['id']) && $data['id']) ? $data['id'] : false;
        if ( !$id ) return false;

        $tags_values = AMPSystem_Lookup::instance( 'tagsByArticle', $id );

        if ( !$tags_values ) return false;
        return join( ',', array_keys( $tags_values) );

    }

    function _assembleTags( $data, $fieldname ) {
        if ( !isset( $data[$fieldname]) || !$data[ $fieldname ] || empty( $data[$fieldname])) {
            return $this->_checkNewTags( $data, $fieldname ) ;
        }
        $created_tags = $this->_checkNewTags( $data, $fieldname );
        $selected_tags = $data[ $fieldname ];
        $all_tags = array_merge( $selected_tags, $created_tags);

        return $all_tags;

    }

    function _checkNewTags( $data, $fieldname ) {
        if ( !isset( $data['new_tags']) && $data['new_tags']) {
            return array( );
        }
        require_once( 'AMP/Content/Tag/Tag.php');
        return AMP_Content_Tag::create_many( $data['new_tags']);
        /*
        $tags_set = preg_split( '/\s?,\s?/', $data['new_tags']);
        $tag_names = AMPSystem_Lookup::instance( 'tags' );
        $simple_tag_names = array( );
        $new_tags_verified = array( );

        foreach( $tag_names as $tag_id => $tag_name ) {
            $simple_tag_names[$tag_id] = strtolower( $tag_name );
        }

        foreach( $tags_set as $raw_new_tag ) {
            $new_tag = trim( $raw_new_tag );
            if ( !$new_tag ) continue;

            //see if an existing tag matches the new one
            $new_tag_id = array_search( strtolower( $new_tag ), $simple_tag_names );

            //create new tag
            if ( !$new_tag_id ) {
                $new_tag_id = $this->_createTag( $new_tag );
            }
            if ( !$new_tag_id ) continue;
            
            //add the id to the results list
            $new_tags_verified[] = $new_tag_id;
        }
        return $new_tags_verified;
        */

    }
/*
    function _createTag( $tag_name ) {
        require_once( 'AMP/Content/Tag/Tag.php');
        $tag = &new AMP_Content_Tag( AMP_Registry::getDbcon( ));
        $tag->setData( array( 'name' => $tag_name ));
        $result = $tag->save( );
        if ( !$result ) return false;
        return $tag->id;
    }
    */

    function _evalWysiwyg( $data, $fieldname ){
        return AMP_USER_CONFIG_USE_WYSIWYG;
    }

    function _checkWysiwyg( $data, $fieldname ) {
        if ( $data['wysiwyg_setting'] == AMP_USER_CONFIG_USE_WYSIWYG ) return true;
        setcookie( 'AMPWYSIWYG', intval( $data['wysiwyg_setting'] ), time( )+( 24*60*60*90 ) );
    }

    function HTMLEditorSetup( $fieldname = 'html' ){
        if( !AMP_USER_CONFIG_USE_WYSIWYG ) return false;
        parent::HTMLEditorSetup( $fieldname );
    }

    function _evalTransferMode( $data, $fieldname ){
        return AMP_USER_CONFIG_CONTENT_MODE_TRANSFER;
    }

    function _checkTransferMode( $data, $fieldname ) {
        if ( $data[ $fieldname ]){
            setcookie( 'AMPContentDefault_section', $data['section'] );
            setcookie( 'AMPContentDefault_class', $data['class'] );
        } else{
            setcookie( 'AMPContentDefault_section', "", time( ) - 3600 );
            setcookie( 'AMPContentDefault_class', "" , time( ) - 3600 );
        }
        if ( $data[$fieldname] == AMP_USER_CONFIG_CONTENT_MODE_TRANSFER ) return true;
        setcookie( 'AMPTransferMode', intval( $data[$fieldname] ), time( )+( 24*60*60*90 ));
    }

    function _formHeader( ){
        $id = $this->getIdValue( );
        if ( !$id ) return false;

        require_once( 'AMP/Content/Article.inc.php' );
        require_once( 'AMP/Content/Article/Display/Info.php');

        $article = &new Article( AMP_Registry::getDbcon( ), $id ) ;
        $display = &new ArticleDisplay_Info( $article );
        return $display->execute( );
    }

    function _formFooter( ){
        $id = $this->getIdValue( );
        if ( !$id ) return false;
        require_once( 'AMP/Content/Article/Version/List.inc.php');
        $list = &new Article_Version_List( AMP_Registry::getDbcon( ), array( 'article' => $id ));
        return $list->execute( );
    }

    function submitted( ){
        if (!isset($_REQUEST['submitAction'])) return false;
        $submitAction = $_REQUEST['submitAction'];
        if (!is_array($submitAction)) return false;

        $accepted_actions = array( 'delete_version' => true , 'restore' => true  );
        $key = key($submitAction);
        if (isset($accepted_actions[$key])) return $key;
        return parent::submitted( );

    }

    
    function validate( ){
        $section_id = isset( $_REQUEST['section']) && $_REQUEST['section'] ? $_REQUEST['section'] : false;//$this->getValues( 'section');
        if ( $section_id && !AMP_allow( 'access', 'section', $section_id )) {
            $flash = AMP_System_Flash::instance( );
            $flash->add_error( sprintf( AMP_TEXT_ERROR_ACTION_NOT_ALLOWED, AMP_TEXT_SAVE ));
            return false;
        }
        return parent::validate( );

    }
    


}
?>
