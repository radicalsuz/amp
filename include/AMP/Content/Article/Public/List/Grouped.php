<?php

require_once( 'AMP/Content/Article/Public/List.php');

class Article_Public_List_Grouped extends Article_Public_List {
    var $_group_displays = array( );
    var $_suppress_pager = true;
    var $_pager_active = true;
    var $_pager_limit = 20;

    function Article_Public_List_Grouped( $container  = false, $criteria = array( ), $limit = null ) {
        $this->__construct( $container, $criteria, $limit );
    }

    function _init_criteria( ) {
        $this->_init_group_displays( );
    }

    function _init_tag_displays( ) {
        $tag = ( isset( $this->_source_criteria['tag']) && $this->_source_criteria['tag'] ) ? $this->_source_criteria['tag'] :false;
        if ( !( $tag && is_array( $tag ))) return false; 

        $tag_display_criteria = $this->_source_criteria;
        /* tags will show all content unless this is enabled
        $class = ( isset( $this->_source_criteria['class']) && $this->_source_criteria['class']) ? $this->_source_criteria['class'] : false; 
        if ( $class && is_array( $class )) {
            unset( $tag_display_criteria['class'] );
            $tag_display_criteria['not_class'] = $class;
        }
        */

        require_once( 'AMP/Content/Tag/Tag.php');
        foreach( $tag as $tag_group) {
            $source = new AMP_Content_Tag( AMP_Registry::getDbcon( ), $tag_group );
            $tag_display = new $this->_grouped_list_class( ( $placeholder = array( )), $tag_display_criteria, AMP_CONTENT_LIST_LIMIT_DEFAULT ); 

            $tag_display->set_container( $source );
            $tag_display->_class_pager=  'AMP_Display_Pager_Morelinkplus';
            $tag_display->_path_pager=   'AMP/Display/Pager/Morelinkplus.php';
            $tag_display->set_pager_limit( $this->_pager_limit, 'first' );
            $tag_display->set_pager_request( array( 'tag' => $tag_group, 'list' => 'tag' ));
            $this->_group_displays['tags'][ $tag_group ] = $tag_display;
        }

    }

    function _init_class_displays( ) {
        $class = ( isset( $this->_source_criteria['class']) && $this->_source_criteria['class']) ? $this->_source_criteria['class'] : false; 
        if ( !( $class && is_array( $class ))) return false; 

        $class_display_criteria = $this->_source_criteria;
        // class groups dont show content that also has a tag to be listed
        $tag = ( isset( $this->_source_criteria['tag']) && $this->_source_criteria['tag'] ) ? $this->_source_criteria['tag'] :false;
        if ( $tag && is_array( $tag ) ) {
            unset( $class_display_criteria['tag'] );
            $class_display_criteria['not_tag'] = $tag;
        }
        require_once( 'AMP/Content/Class.inc.php');

        foreach( $class as $class_group) {
            $source = new ContentClass( AMP_Registry::getDbcon( ), $class_group );
            $class_display_criteria['class'] = $class_group;
            $class_display  = new $this->_grouped_list_class( ( $null = 0 ), $class_display_criteria, $source->getListItemLimit( ) ); 

            $class_display->set_container( $source );
            $class_display->_class_pager=  'AMP_Display_Pager_Morelinkplus';
            $class_display->_path_pager=   'AMP/Display/Pager/Morelinkplus.php';
            $class_display->set_pager_limit( $this->_pager_limit, 'first' );
            $class_display->set_pager_request( array( 'class' => $class_group, 'list' => 'class' ));
            $this->_group_displays['classes'][$class_group ] = $class_display;
        }

    }

    function _init_group_displays( ) {
        $list_class = defined( 'AMP_SECTION_DISPLAY_GROUPED') ? AMP_SECTION_DISPLAY_GROUPED : false;
        if ( $list_class && !class_exists( $list_class )) {
            trigger_error( sprintf( AMP_TEXT_ERROR_NOT_DEFINED, 'AMP', $list_class ));
        }
        if ( !( $list_class && class_exists( $list_class ))) {
            $list_class = AMP_SECTION_DISPLAY_DEFAULT;
        }
        $this->_grouped_list_class = $list_class;
        $this->_init_class_displays( );
        $this->_init_tag_displays( );
    }

    function execute( ) {
        if ( empty( $this->_group_displays )) return parent::execute( );
        $output = '';
        foreach( $this->_group_displays  as $display_type => $display_set ) {
            $names = AMP_lookup($display_type);
            foreach( $display_set as $key => $display ) {
				if (empty($display) || ( !$display->qty( ))) continue;
                if ( $names && isset( $names[$key] ) && $names[$key] ) {
                    $output .= $this->render_subheader_format( AMP_TEXT_RECENT.$names[$key]);
                }
                $output .= $display->execute( );
            }
        }
        return $this->_renderBlock( $output );

    }

}
?>
