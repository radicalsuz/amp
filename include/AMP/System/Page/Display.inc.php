<?php

/* * * * * * * * * * *
 * 
 * AMPSystem_Page_Display
 *
 * unified templating for AMP System-Side pages
 *
 * AMP 3.5.0
 *
 * 2005-06-03
 * Author: austin@radicaldesigns.org
 *
 * * * **/

require_once("AMP/System/BaseTemplate.php");

class AMPSystem_Page_Display {

    var $page;
    var $itemtype;

		var $show_template = true;

    function AMPSystem_Page_Display( &$page ) {
        $this->page = &$page;
    }

    function pagetitle( $item = null, $verb = null) {
        if (!isset($verb)) $verb = $this->page->action;
        if (!isset($item)) $item = $this->itemtype;
        $plural_actions = array('View');
        if ((array_search( $verb, $plural_actions )!==FALSE) && substr($item, -1)!='s' ) $item.='s';
        return "<H2>$verb $item</H2>";
    }


    function execute() {

        $output  = $this->showMessages();
        $output .= $this->pagetitle();
        
        foreach( $this->page->getComponents() as $item => $args ) {
            $output .= $this->page->$item->output( $args );
        }

        return $this->_templateContent( $output );

    }

    function setItemType( $itemtype ) {
        $this->itemtype = $itemtype;
    }

    function setNavName( $nav_name ) {
        $this->nav_name = $nav_name;
    }

    function getNavName() {
        if (isset($this->nav_name)) return $this->nav_name;
        if (isset($GLOBALS['mod_name'])) $this->nav_name = $GLOBALS['mod_name'];
        return $this->nav_name;
    }


    function _templateContent( $content ) {
        if (!$this->show_template) return $content;
        if (isset($GLOBALS['modid'])) $modid = $GLOBALS['modid'];

        $template = & AMPSystem_BaseTemplate::instance();

        if (isset($modid) && $modid) $template->setTool( $modid );
        $template->setToolName( $this->getNavName() );
        
        return $template->outputHeader() .
                        $content .
                        $template->outputFooter();
    }

    function showMessages() {
        $output = "";
        foreach ($this->page->getErrors() as $error ) {
            $output .=  "<span class=\"page_error\">$error</span>\n";
        }
        foreach ($this->page->getResults() as $result ) {
            $output .=  "<span class=\"page_result\">$result</span>\n";
        }
        return $output;
    }
}
?>
