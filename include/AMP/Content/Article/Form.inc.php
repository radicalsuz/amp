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

        $this->addTranslation( 'comments_allowed_until',         '_makeDbDateTime',   'get');
        $this->addTranslation( 'comments_allowed_until',         '_makeNullDate',   'set');

        $this->addTranslation( 'sections_related',   '_getRelatedSections', 'set');
        $this->addTranslation( 'sections_related',   '_getMultiSelectBlanks', 'get');

        $this->addTranslation( 'tags',   '_getTags', 'set');
        $this->addTranslation( 'tags',   '_getMultiSelectBlanks', 'get');
        $this->addTranslation( 'tags',   '_assembleTags', 'get');

        $this->addTranslation( 'transfer_mode_setting','_returnBlankCheckbox',  'get');
        $this->addTranslation( 'transfer_mode_setting','_checkTransferMode',  'get');
        $this->addTranslation( 'transfer_mode_setting','_evalTransferMode',  'set');
        

        if ( AMP_CONTENT_WORKFLOW_ENABLED && isset( $this->fields['publish'])) {

            $this->fields['publish']['label'] = str_replace( 'PUBLISH', 'Status', $this->fields['publish']['label'] );
            $this->fields['publish']['type'] = 'select';
            $this->fields['publish']['values'] = AMP_lookup( 'status' );
            $this->addTranslation( 'notes', 'get_revision_notes', 'get');

            require_once( 'AMP/Content/Article/ComponentMap.inc.php');
            $map = new ComponentMap_Article( );
            if ( !$map->isAllowed( 'publish')) {
                $this->addTranslation( 'publish', 'no_publish_allowed', 'get');
                $this->fields['publish']['values'] = AMP_lookup( 'status_no_publish' );
            } else {
                $this->fields['status_comments_header']['type'] = 'blocktrigger';
                $this->fields['status_comments']['type'] = 'textarea';
            }

        }
        if( !AMP_CONTENT_HUMANIZE_URLS) {
            unset( $this->fields['route_slug'] );
            unset( $this->fields['route_slug_info'] );
        }
        if( !AMP_lookup( 'image_folders')) {
            unset( $this->fields['image_folder'] );
        }


    }

    function _initJavascriptActions( ){
        $header = &AMP_getHeader( );
        $this->_initTabDisplay( $header );
        $this->_initAutoLookups( $header );
        $this->_initMediaThumbnailLookup( $header );
        $this->_initTransferMode( $header );
        $this->_initPhotoLookup( $header );
        $this->_initPhotoSearch( $header );
        $this->_initPrettyUrlCreation( $header );
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

    function _initMediaThumbnailLookup( $header ) {
        $header->addJavascriptOnload( 
<<<EVENTCODE
Event.observe( document.forms['article'].elements['media_html'], 'change', 
find_youtube_url = function( ) {
    if (document.forms['article'].elements['media_thumbnail_url'].value )  {
        return;
    }
    matcher = /src=[^>]*youtube.com\/v\/([^>"'&]+)/
    matches = matcher.exec( document.forms['article'].elements['media_html'].value );
    youtube_id = matches[1];
    if (!youtube_id) return;
    new_url_value = ( "http://img.youtube.com/vi/%s/"+youtube_thumbnail_options[youtube_thumbnail_counter]+".jpg").replace( "%s", youtube_id ); 
    document.forms['article'].elements['media_thumbnail_url'].value = new_url_value;

    youtube_thumbnail_counter++;
    if ( youtube_thumbnail_counter >= youtube_thumbnail_options.length ) youtube_thumbnail_counter = 0;
    $( 'info_block_thumb').update( "<img src='"+new_url_value+"'>");
    $( 'info_block_thumb_close').update( "<img src='"+new_url_value+"'>");
    AMP_show_panel( 'info_block_thumb_close' );
}
);
window.youtube_thumbnail_options = new Array( "default", "1", "2", "3" );
window.youtube_thumbnail_counter = 0;


EVENTCODE
        );

    }

    function _initPhotoLookup( $header ) {
        $init_script = <<<PHOTOCODE
        Event.observe( document.forms['article'].elements['picture'], 'change', function( ) {
    var picture_filename = document.forms['article'].elements['picture'].value;
    var photo_data = photo_data_maker( );

    new Ajax.Request( 
        '/system/image_manager.php', 
        { onSuccess:photo_data.update,
          parameters: {
              action:"read",
              id: picture_filename
          },
          method: 'GET'
                
        });
});

PHOTOCODE;

        $support_script = <<<PHOTOCODE_SUPPORT
function photo_data_maker( ) {
    return {

    update: function( response ) {
        var json_object = eval( response.getResponseHeader( 'X-JSON'));
        //alert( this.form.id );
        if( !( json_object.alt || json_object.caption) ) {
            $( 'picture_data').update( 'No Image Data Found');
            return;
        }
        

        go_button = document.createElement( 'input');
        go_button.type = 'button';
        go_button.value = "Use These";
        go_button.id = "use_ajax_image_data";
        go_button.name = "use_ajax_image_data";
        go_button.style.className = 'photo_data_activate';
        var display_value = "";
        if( json_object.caption != undefined ) {
            display_value += "Caption:"+ json_object.caption + "<br />";
        }
        if ( json_object.alt != undefined ) {
            display_value += "Alt: " + json_object.alt + "<br />" ;
        }
        display_value = "<div class=photocaption>" + display_value + "</div>"; 

        $( 'picture_data').update( display_value );
        $( 'picture_data').appendChild( go_button );
        AMP_show_panel( 'picture_data');
        
        Event.observe( $('use_ajax_image_data'), 'click', function( ) {
            AMP_show_panel( 'image_details');
            if ( $( 'image_details').getStyle('display') == 'none') {
                change_form_block( 'image_details' );
            }
            if( json_object.alt != undefined ) {
                $( 'article').alttag.value = json_object.alt;
            }

            if (json_object.caption != undefined ) {
                document.forms['article'].elements['piccap'].value = json_object.caption;
            }

            $( 'picture_data').update( '');

        });
        
    }
    };
}
PHOTOCODE_SUPPORT;
        $header->addJavascriptOnload(  $init_script, 'photodata' );
        $header->addJavascriptDynamic(  $support_script, 'photodata_support' );

    }

    function _initPhotoSearch( &$header ) {
        $init_script = <<<PHOTO_SEARCH
jq( '#image_folder_search').val( '' );
jq( '#image_folder_search').change(  function ( ) {
    if( jq( this ).val( ) == "") {
        jq( '#picture_selector option').show( );

    } else {
        jq( '#picture_selector option').hide( );
        jq( '#picture_selector option[value^="'+jq(this).val( ) +'/"]').show( );
        jq( '#picture_selector option[value^="'+jq('#picture_selector').val( ) +'"]').show( );
    }
    new Effect.Highlight( jq( '#picture_selector' ).parent( 'td').parent( 'tr')[0] );

});
PHOTO_SEARCH;
        $header->addJavascriptOnload(  $init_script, 'photo_search' );
    }



    function _initPrettyUrlCreation( &$header ){
        if( !AMP_CONTENT_HUMANIZE_URLS ) return;
        $pretty_url_builder = <<<SCRIPT
                if( jq( 'form#article input[name=route_slug]' ).val( ) === "") {
                   jq( 'form#article textarea[name=title]').change(  function( ev ) {
                        var new_val =  jq( this ).val( ).replace( /[\s_]/g,'-').replace( /[^-A-z0-9]/g, '').toLowerCase( );
                        jq( 'form#article input[name=route_slug]' ).val( new_val );
                   });
                }
SCRIPT;
        $conflict_checker = <<<SCRIPT
               jq( 'form#article input[name=route_slug]').change( check_route_ajax );  
               jq( '#manual_route_check').click( check_route_ajax );  
			   function check_route_ajax( ev ) {
                    var system_domain = '%s';
					var target = jq( 'form#article input[name=route_slug]' );
                    jq.getJSON('/system/route_slug_ajax.php?slug_name=' + jq( target ).val() + '&ignore[0][owner_type]=%s&ignore[0][owner_id]=%s', function( result ) {
                        if ( result.conflicts !== undefined && result.conflicts.length == 0 ) {
                            jq( '#route_slug_details' ).html( "URL: " + system_domain + result.clean_url );
                        } else {
                            jq('#route_slug_details').html( "Warning: ");
                            jq.each( result.conflicts, function() {
                                jq('#route_slug_details').append( "This pretty url is already in use on <a href='" + this.owner_edit_url + "'>" + this.owner_type + " #"+ this.owner_id + "</a>" );
                            } );
                            jq('#route_slug_details').append( "<br/>Suggested Available URL: " + system_domain + result.clean_url );
                        }
                    } );
					return false;
               }
SCRIPT;
        $page_load_wrapper = <<<SCRIPT
            jq( function( ) {
                %s
            });
SCRIPT;
        $values = $this->getValues();
        $conflict_check = sprintf( $conflict_checker, AMP_SITE_URL, 'article',  ( isset( $values['id']) ? $values['id'] :'') );
        $header->addJavascriptDynamic( sprintf( $page_load_wrapper, $pretty_url_builder . $conflict_check ));
    }

    function _makeNullDate( $data, $fieldname ) {
        if( !isset( $data[$fieldname])) return '';
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
        $status_selects = array( 'publish', 'class' );

        if ( array_search( $name, $status_selects) !== FALSE ) return $valueset;
        if ( array_search( $name, $required_selects ) === FALSE ) return parent::_selectAddNull( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_CONTENT_SECTION_NAME_ROOT . ' --') + $valueset;
    }

    function _blankValueSet( $valueset, $name ){
        $required_selects = array( 'section', 'new_section_parent');
        if ( array_search( $name, $required_selects ) === FALSE ) return parent::_blankValueSet( $valueset, $name );
        return array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_CONTENT_SECTION_NAME_ROOT . ' --');
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
        if ( AMP_CONTENT_WORKFLOW_ENABLED ) {
            unset( $fields['publish']['per']);
        }

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
        if ( !( isset( $data['new_tags']) && $data['new_tags'])) {
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

    function no_publish_allowed( $data, $fieldname ) {
        if ( !isset( $data[$fieldname])) return false;
        if ( $data[$fieldname] == AMP_CONTENT_STATUS_LIVE ) return AMP_CONTENT_STATUS_DRAFT;
        return $data[$fieldname];
    }

    function get_revision_notes( $data, $fieldname ) {
        $existing_notes = ( isset( $data[$fieldname]) && $data[$fieldname]) ? $data[$fieldname] : false;
        if ( !( isset( $data['status_comments']) && $data['status_comments'])) {
            return $existing_notes;
        }
        $user_names = AMP_lookup( 'users' );
        $current_user = $user_names[AMP_SYSTEM_USER_ID];

        return sprintf( AMP_TEXT_REVISION_COMMENTS_HEADER, date( 'Y-m-d'), $current_user ) . "\n"
                        . $data['status_comments'] . "\n"
                        . str_repeat( '-', 30 ) . "\n"
                        . $existing_notes;

    }



}
?>
