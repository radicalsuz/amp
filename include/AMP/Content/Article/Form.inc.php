<?php

require_once( 'AMP/System/Form/XML.inc.php');

class Article_Form extends AMPSystem_Form_XML {

    var $name_field = 'title';

    function Article_Form( ) {
        $name = "article";
        $this->init( $name );
    }

    function setDynamicValues() {
        $this->addTranslation( 'section',      '_checkNewSection',  'get');
        $this->addTranslation( 'link',         '_checkToolLink',    'get');

        $this->addTranslation( 'image_upload', '_manageUpload',     'get');
        $this->addTranslation( 'picture',      '_checkUploadImage', 'get');

        $this->addTranslation( 'doc_upload',   '_manageUpload',     'get');
        $this->addTranslation( 'doc',          '_checkUploadFile',  'get');

        $this->setFieldValueSet( 'doc', AMPfile_list( 'downloads'));
        $this->HTMLEditorSetup( );
        $this->_initTabDisplay( );
    }

    function _configHTMLEditor( &$editor ){
        $editor->height = '600px';
    }

    function _initTabDisplay( ){
        $header = &AMP_getHeader( );
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
        $commentList = &$this->_getCommentList( $this->getIdValue( ));
        $fields['comment_list']['default'] = $commentList->output( );
        return $fields;
    }

    function &_getCommentList( $id ){
        if ( isset( $this->_commentList )) return $this->_commentList;
        if ( !$id ) return AMP_TEXT_SEARCH_NO_MATCHES;
        require_once( 'AMP/Content/Article/Comment/List.inc.php');

        $commentList = &new ArticleComment_List( AMP_Registry::getDbcon( ));
        $commentList->applySearch( array( 'article' => $id ));
        $commentList->suppressMessages( );
        $commentList->suppressAddlink( );
        $commentList->suppressToolbar( );
        $commentList->setEditLinkTarget( 'blank' );
        $this->_commentList = &$commentList;
        return $commentList;
    }
}
?>
