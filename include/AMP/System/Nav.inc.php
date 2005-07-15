<?php

/* * * * * * * * * * *
 *   AMPSystem_Nav
 *  
 *  System-side Navigation Component 
 *
 *  AMP 3.5.0
 *  2005-07-07
 *  Author: austin@radicaldesigns.org
 *
 *
 *  * * * * * **/

 class AMPSystem_Nav {

    var $label;
    var $href;
    var $items;
    var $permission;

    var $children;

    var $id;
    var $name;

    function AMPSystem_Nav( $desc=null, &$manager ) {
        if (!isset($desc)) return;
        if(isset( $desc['id'])) $this->id = $desc['id'];
        if(isset( $desc['title']))$this->addTitle($desc['title']);
        if(isset( $desc['href'])) $this->href = $desc['href'];
        if(isset( $desc['per']))  $this->permission = $desc['per'];
        if(isset( $desc['item'])) $this->addItems($desc['item']);

        if (isset($desc['child'])) {
            foreach ($desc['child'] as $childname ) {
                $this->addChild( $manager->addNav( $childname, $desc[ $childname ] ));
            }
        }
                
    }

    function addItems( $itemvar ) {
        static $itemcount = 0;

        if (!is_array($itemvar)) $itemset = array( $itemvar );
        else $itemset = $itemvar;

        foreach ($itemset as $item) {
            $id = $this->id . $itemcount++;
            $this->items[$id] = $item;
        }
    }

    function addChild( &$item ) {
        $this->children[] = &$item;
    }

    function checkPermission( $item=null ) {
        $per = false;
        if (isset($item['per'])) $per = $item['per'];
        if (!isset($item)) $per = $this->permission;
        if (!$per) return true;
        return AMP_Authorized( $per);
    }

    function output() {
        if (!$this->checkPermission()) return false;

        $output = $this->_HTML_NavTitle() . $this->_HTML_NavStart();
        if (!is_array($this->items)) return $output . $this->_HTML_NavEnd();

        foreach ($this->items as $navkey => $nav_value) {
            if ( !is_array($nav_value) ) continue;
            if (!$this->checkPermission( $nav_value ) ) continue;
            $output .= $this->_HTML_NavItem( $nav_value );
        }

        return $output . $this->_HTML_NavEnd();
    }


    function addTitle( $title ) {
        $this->label = $title;
    }

    function addItem ( $href, $label, $class = null, $per=null) {
        $new_item = array( 'href' => $href, 'label' => $label);
        if (isset($class)) $new_item['class'] = $class;
        if (isset($per)) $new_item['per'] = $per;
        $this->items[] = $new_item;
    }

    function addToolOptions( $modid, $nav_name ) {
        $this->addItem( 'module_header_list.php?modid='.$modid , 'Public Pages',  'page',       AMP_PERMISSION_TOOLS_INTROTEXT );
        $this->addItem( 'module_controllist.php?modid='.$modid , 'Tool Settings', 'settings',   AMP_PERMISSION_TOOLS_ADMIN );
    }
        

    function _HTML_NavTitle( ) {
       if (!isset($this->label)) return false; 
       return  "<p class='side_banner'>".$this->label."</p>";
    }
    function _HTML_NavSubTitle( $item ) {
       if (!isset($item['title'])) return false; 
       return   $this->_HTML_NavEnd() . 
                "<p class ='sidetitle'>".$item['title']."</p>\n" .
                $this->_HTML_NavStart();
    }

    function _HTML_NavStart() {
        return "\n<ul class=side>";
    }

    function _HTML_NavEnd() {
        return "\n</ul><br clear='all' />";
    }

    function _HTML_NavItem( $item ) {
        
        $class = "side_" . (isset($item['class'])? $item['class']  :  'type');
        return  $this->_HTML_NavSubTitle( $item ) .
                "\n<li class = \"$class\"><a href='".$item['href']."' >".$item['label']."</a></li>";
    }



}
?>
