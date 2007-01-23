<?php
require_once( 'AMP/System/Tree.inc.php' );

class AMP_System_Menu_Display {
    var $_renderer;
    var $_tree;

    var $menu_id = 'listMenuRoot';

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
        //$header->addStylesheet( 'scripts/listmenu_fallback.css' );
        $header->addStylesheet( 'scripts/listmenu_h.css' );
        $header->addJavaScript( 'scripts/fsmenu.js' );
        $header->addJavaScript( 'scripts/fsmenu_config.js' );

        $config_script = 
/*
"                var listMenu = new FSMenu('listMenu', true, 'display', 'block', 'none');
                page.winW=function()
                 { with (this) return Math.max(minW, MS?win.document[db].clientWidth:win.innerWidth) };
                page.winH=function()
                 { with (this) return Math.max(minH, MS?win.document[db].clientHeight:win.innerHeight) };
                page.scrollX=function()
                 { with (this) return MS?win.document[db].scrollLeft:win.pageXOffset };
                page.scrollY=function()
                 { with (this) return MS?win.document[db].scrollTop:win.pageYOffset };

                function repositionMenus(mN) { with (this)
                {
                 var menu = this.menus[mN].lyr;

                 // Showing before measuring corrects MSIE bug.
                 menu.sty.display = 'block';
                 // Reset to and/or store original margins.
                 if (!menu._fsm_origML) menu._fsm_origML = menu.ref.currentStyle ?
                  menu.ref.currentStyle.marginLeft : (menu.sty.marginLeft || 'auto');
                 if (!menu._fsm_origMT) menu._fsm_origMT = menu.ref.currentStyle ?
                  menu.ref.currentStyle.marginTop : (menu.sty.marginTop || 'auto');
                 menu.sty.marginLeft = menu._fsm_origML;
                 menu.sty.marginTop = menu._fsm_origMT;

                 // Calculate absolute position within document.
                 var menuX = 0, menuY = 0,
                  menuW = menu.ref.offsetWidth, menuH = menu.ref.offsetHeight,
                  vpL = page.scrollX(), vpR = vpL + page.winW() - 16,
                  vpT = page.scrollY(), vpB = vpT + page.winH() - 16;
                 var tmp = menu.ref;
                 while (tmp)
                 {
                  menuX += tmp.offsetLeft;
                  menuY += tmp.offsetTop;
                  tmp = tmp.offsetParent;
                 }

                 // Compare position to viewport, reposition accordingly.
                 var mgL = 0, mgT = 0;
                 if (menuX + menuW > vpR) mgL = vpR - menuX - menuW;
                 if (menuX + mgL < vpL) mgL = vpL - menuX;
                 if (menuY + menuH > vpB) mgT = vpB - menuY - menuH;
                 if (menuY + mgT < vpT) mgT = vpT - menuY;

                 if (mgL) menu.sty.marginLeft = mgL + 'px';
                 if (mgT) menu.sty.marginTop = mgT + 'px';
                }};

                // Set this to process menu show events for a given object.
                addEvent(listMenu, 'show', repositionMenus, true);

                // Hide all menus when the document is clicked
                addEvent(document, 'click',  function() {
                 listMenu.hideAll();
                });


                // Here's a second method. This only works in IE 5.5+ on Windows, but it doesn't make
                // select boxes appear and disappear (menus cleanly cover them).

                FSMenu.prototype.ieSelBoxFixShow = function(mN) { with (this)
                {
                 var m = menus[mN];
                 if (!isIE || !window.createPopup) return;
                 if (navigator.userAgent.match(/MSIE ([\d\.]+)/) && parseFloat(RegExp.$1) > 6.5)
                  return;
                 // Create a new transparent IFRAME if needed, and insert under the menu.
                 if (!m.ifr)
                 {
                  m.ifr = document.createElement('iframe');
                  m.ifr.src = 'about:blank';
                  with (m.ifr.style)
                  {
                   position = 'absolute';
                   border = 'none';
                   filter = 'progid:DXImageTransform.Microsoft.Alpha(style=0,opacity=0)';
                  }
                  m.lyr.ref.parentNode.insertBefore(m.ifr, m.lyr.ref);
                 }
                 // Position and show it on each call.
                 with (m.ifr.style)
                 {
                  left = m.lyr.ref.offsetLeft + 'px';
                  top = m.lyr.ref.offsetTop + 'px';
                  width = m.lyr.ref.offsetWidth + 'px';
                  height = m.lyr.ref.offsetHeight + 'px';
                  visibility = 'visible';
                 }
                }};
                FSMenu.prototype.ieSelBoxFixHide = function(mN) { with (this)
                {
                 if (!isIE || !window.createPopup) return;
                 var m = menus[mN];
                 if (m.ifr) m.ifr.style.visibility = 'hidden';
                }};

                addEvent(listMenu, 'show', function(mN) { this.ieSelBoxFixShow(mN) }, 1);
                addEvent(listMenu, 'hide', function(mN) { this.ieSelBoxFixHide(mN) }, 1);

                var arrow = null;
                if (document.createElement && document.documentElement)
                {
                     arrow = document.createElement('span');
                     arrow.appendChild(document.createTextNode('>'));
                     arrow.className = 'subind';
                }
*/"
                addEvent(window, 'load', new Function('listMenu.activateMenu(\"".$this->menu_id."\", arrow)'));";
                
        $header->addJavascriptDynamic( $config_script, 'fsmenu_config' );
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
        //$this->debug( );

    }

    function debug( ) {
        $branches = ( $this->_tree->getChildren( ));
        print count( $branches );
        foreach( $branches as $branch ) {
            print $branch->label . '<BR>';
            $branch_children = $branch->getChildren( );
            print count( $branch_children );
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
