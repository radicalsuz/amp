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

class AMPSystem_Page_Display {

    var $page;
    var $itemtype;

    function AMPSystem_PageDisplay( &$page ) {
        $this->page = &$page;
    }

    function pagetitle( $item = null, $verb) {
        if (!isset($verb)) $verb = $this->page->action;
        if (!isset($item)) $item = $this->itemtype;
        $plural_actions = array('View');
        if ((array_search( $verb, $plural_actions )!==FALSE) && substr($item, -1)!='s' ) $item.='s';
        return "<H2>$verb $item</H2>";
    }

    function execute() {
        include ('header.php');

        print $this->pagetitle();
        print $this->showMessages();
        
        foreach( $this->page->show as $item ) {
            print $this->page->$item->output();
        }

        include ('footer.php');
    }

    function showMessages() {

        $output = "";

        foreach ($this->page->getResults() as $result) {
            if (!$result) continue;
            $output.= '<span class="page_result">'.$result."</span><BR>\n";
        }

        foreach ($this->page->getErrors() as $error) {
            if (!$error) continue;
            $output.= '<span class="page_error">'.$error."</span><BR>\n";
        }

        return $output;
    }
}
?>
