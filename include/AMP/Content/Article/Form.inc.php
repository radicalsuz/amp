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
        $this->addTranslation( 'link',         '_checkToolLink',    'get');

        $this->addTranslation( 'image_upload', '_manageUpload',     'get');
        $this->addTranslation( 'picture',      '_checkUploadImage', 'get');

        $this->addTranslation( 'doc_upload',   '_manageUpload',     'get');
        $this->addTranslation( 'doc',          '_checkUploadFile',  'get');

        $this->addTranslation( 'wysiwyg_setting','_returnBlankCheckbox',  'get');
        $this->addTranslation( 'wysiwyg_setting','_checkWysiwyg',  'get');
        $this->addTranslation( 'wysiwyg_setting','_evalWysiwyg',  'set');

        $this->setFieldValueSet( 'doc', AMPfile_list( 'downloads'));
        //$this->_initJavascriptActions( );
        //$this->HTMLEditorSetup( );
    }

    function _initJavascriptActions( ){
        $header = &AMP_getHeader( );
        $this->_initTabDisplay( $header );
        $this->_initAutoLookups( $header );
        $this->HTMLEditorSetup( );
    }

    function _initAutoLookups( &$header ){
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "author", "author_list", "ajax_request.php", {} );');
        $header->addJavascriptOnload( 'new Ajax.Autocompleter( "source", "source_list", "ajax_request.php", {} );');
        
    }

    function _configHTMLEditor( &$editor ){
        $editor->height = '600px';
    }

    function execute( ){
        $value =PARENT::execute( );
        return $value;
    }

    function _initTabDisplay( &$header ){
        $header->addJavaScript( 'scripts/tabs.js', 'tabs');
        
        $header->addJavascriptOnload( 
            'current_tab = document.getElementById( "tab_0" );'."\n"
            .'if ( current_tab ) Tabs_highlight( current_tab ) ;'
            );
        
    }


    function _selectAddNull( $valueset, $name ) {
        $required_selects = array( 'section', 'new_section_parent');
        if ( array_search( $name, $required_selects ) === FALSE ) return PARENT::_selectAddNull( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --') + $valueset;
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

    function _saveRedirectAlias( $data, $fieldname ){
        if ( ! ( isset( $data['new_alias_name'] ) && $data['new_alias_name'] )) {
            if ( !isset( $data[$fieldname ])) return false;
            return $data[ $fieldname ];
        }
        require_once( 'AMP/Content/Redirect/Redirect.php');

    }

    function adjustFields( $fields ){
        $fields['comment_list']['default'] = $this->_getCommentListOutput( $this->getIdValue( ));
        $fields = array_merge( $fields, $this->_defineCustomFields( ));
        return $fields;
    }

    function _defineCustomFields( ){
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
        if ( !empty( $custom_fields )) {
            $custom_fields = array_merge( $this->_custom_field_header, $custom_fields);
        }
        return $custom_fields;
    }

    function _getCommentListOutput( $id ){
        $commentList = &$this->_getCommentList( $id );
        if ( is_object( $commentList )) return $commentList->execute( );
        return $commentList;
    }

    function &_getCommentList( $id ) {
        if ( isset( $this->_commentList )) return $this->_commentList;
        if ( !$id ) return AMP_TEXT_SEARCH_NO_MATCHES;
        require_once( 'AMP/Content/Article/Comment/List_Basic.inc.php');

        $commentList = &new AMP_Content_Article_Comment_List_Basic( AMP_Registry::getDbcon( ), array( 'article' => $id ));
        $commentList->setEditLinkTarget( 'blank' );
        $commentList->appendAddLinkVar( 'article_id='.$id );
        $this->_commentList = &$commentList;
        return $commentList;
    }

    function _evalWysiwyg( $data, $fieldname ){
        return AMP_USER_CONFIG_USE_WYSIWYG;
    }

    function _checkWysiwyg( $data, $fieldname ) {
        if ( $data['wysiwyg_setting'] == AMP_USER_CONFIG_USE_WYSIWYG ) return true;
        setcookie( 'AMPWYSIWYG', intval( $data['wysiwyg_setting'] ), time( )+( 24*60*60*90 ));
    }

    function HTMLEditorSetup( $fieldname = 'html' ){
        if( !AMP_USER_CONFIG_USE_WYSIWYG ) return false;
        PARENT::HTMLEditorSetup( $fieldname );
    }

    function _formHeader( ){
        $id = $this->getIdValue( );
        if ( !$id ) return false;
        require_once( 'AMP/Content/Article.inc.php');
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
}
?>
