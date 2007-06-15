<?php
require_once( 'AMP/System/Tree.inc.php' );

class AMP_System_Menu_Display {
    var $_renderer;
    var $_tree;

    var $menu_id = 'listMenuRoot';
    var $api_version = 2;

    function AMP_System_Menu_Display( ) {
        $this->__construct( );
    }

    function __construct( ) {
        $this->_renderer = &AMP_get_renderer( );
        $this->_init_tree( );
        $this->_init_header( );
    }

    function execute( ) {
        $output = $this->render_branch( $this->_tree );
        return $this->_renderer->simple_ul( $output, array( 'id' => $this->menu_id, 'class' => 'menulist' ));
    }

    function _init_header( ) {
        $header = &AMP_get_header( );
        $header->addStylesheet( 'scripts/listmenu_fallback.css', 'fsmenu-fallback' );
        $header->addStylesheet( 'scripts/listmenu_h.css' );
        $header->addJavaScript( 'scripts/fsmenu_commented.js' );
        $header->addJavaScript( 'scripts/fsmenu_config.js' );

        $basic_script = 
"                var arrow = null;
                if (document.createElement && document.documentElement)
                {
                     arrow = document.createElement('span');
                     arrow.appendChild(document.createTextNode('>'));
                     arrow.className = 'subind';
                }
                listMenu.activateMenu(\"".$this->menu_id."\", arrow);\n";
        $header->addJavascriptOnLoad( $basic_script );
    }

    function _init_tree( ) {
        require_once( 'AMP/System/Map.inc.php');
        $map_source = &AMPSystem_Map::instance( );
        $map = $map_source->getMenu( );
        $home = $map['home'];
        unset( $map['home'] );

        $this->_tree = &new AMP_Menu_Tree( );
        foreach( $home as $key => $def ) {
            $branch = new AMP_Menu_Tree( );
            $branch->id = $key;
            $branch->label = $def['label'];
            $branch->href =  isset( $def['href']) ? $def['href'] : '#';
            if ( isset( $def['child'] ) && $def['child'] && isset( $map[ $def['child']]) ) {
                $this->_init_branch( $branch, $map, $def['child'] );
            }
            $this->_tree->addChild( $branch );
            unset( $branch );
        }

    }

    function _init_branch( &$parent , $map, $branch_name ) {
        foreach( $map[$branch_name] as $key => $def ) {
            if (isset($def['separator']) && $def['separator']) {
                $this->insert_separator( $parent, $key );
            }            
            $branch = new AMP_Menu_Tree( );
            $branch->id = $key;
            $branch->label = $def['label'];
            $branch->href =  isset( $def['href']) ? $def['href'] : '#';
            if ( isset( $def['child'] ) && $def['child'] && isset( $map[ $def['child']]) ) {
                $this->_init_branch( $branch, $map,  $def['child'] );
            }
            $parent->addChild( $branch, $key );
            unset( $branch );
        }

    }

    function insert_separator( &$parent, $key ) {
        $branch = new AMP_Menu_Tree( );
        $branch->id = $key . '_sep';
        $branch->label = '<HR>';
        $branch->href =  '#';
        $parent->addChild( $branch );
        unset( $branch );
    }

    function render_branch( &$branch ) {
        $output = '';

        foreach( $branch->getChildren( ) as $child_branch ) {

            if ( !( $child_branch->hasChildren( ) && ( $child_output = $this->render_branch( $child_branch )))) {
                $output .= $this->render_item( $child_branch );
                continue; 
            }
            $child_format = $this->_renderer->simple_ul( $child_output );
            $output .= $this->_renderer->simple_li( $this->_renderer->link( $child_branch->href, $child_branch->label ) . $child_format );
            
        }
        return $output;
    }

    function render_item( $child_branch ) {
        return $this->_renderer->simple_li( $this->_renderer->link( $child_branch->href, $child_branch->label ));
    }
}

class AMP_Menu_Tree extends AMPSystem_Tree {
    var $label;
    var $href;
    var $_child_component = 'AMP_Menu_Tree';

    function AMP_Menu_Tree( ) {
        $this->__construct( );
    }
}


?>
