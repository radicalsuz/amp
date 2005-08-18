<?php
require_once ('AMP/Menu/Menu.inc.php' );

class AMP_Menu_UL_CSS extends AMP_Menu_UL {

    var $_baseComponent = 'AMP_MenuComponent_UL_CSSroot';

    function AMP_Menu_UL_CSS( &$menuset, $name ) {
        $this->init( $menuset, $name );
    }

    /*
    function &buildMenu ( &$menu_array ) {

        //First build the table
        $component = $this->_baseComponentHTML;
        $root_menu = new $component( $this, array('id'=>$this->name, 'label'=>'', 'href'=>'') );

        $root_menu->buildMenuSub ( $menu_array );

        $this->script_set = & new AMP_MenuComponent_CSS_Script ( $this, array('id'=>$this->name, 'label'=>'css_menu_script', 'href'=>'') ); 

        return $root_menu;
    }
*/
    function output($item_name=null) {

        $this->getCSS();

        return ($this->outputCSS() . $this->menuset->output() );
    }

}

class AMP_MenuComponent_UL_CSSroot extends AMP_MenuComponent_UL {

    var $css_template_vars = array( 'color', 'bgcolor', 'color_hover', 'bgcolor_hover', 'id', 'width' );
    var $css_template = 
"UL#listfolder_%5\$s{
    CURSOR: default; POSITION: relative; WIDTH: %6\$s; Z-INDEX: 1000; PADDING-TOP: 20px; PADDING-BOTTOM: 20px;
}
UL#listfolder_%5\$s UL {
    CURSOR: default; LIST-STYLE: none; MARGIN-LEFT: %6\$spx; MARGIN-TOP: -1.5em; 
    PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; PADDING-TOP: 0px; 
    POSITION: absolute; VISIBILITY: hidden; WIDTH: %6\$spx; Z-INDEX: 1020
}
UL#listfolder_%5\$s UL LI {
    WIDTH: %6\$spx;
}
UL#listfolder_%5\$s UL DIV {
    WIDTH: %6\$spx;
}
UL {
    VISIBILITY: visible
}
UL#listfolder_%5\$s UL {
    LEFT: -1px; PADDING-TOP: 1px
}
UL#listfolder_%5\$s LI {
    CURSOR: hand; TEXT-ALIGN: left; WIDTH: %6\$spx;
}
UL#listfolder_%5\$s DIV {
    CURSOR: hand; TEXT-ALIGN: left; WIDTH: %6\$spx;
}
UL#listfolder_%5\$s {
    LIST-STYLE: none; MARGIN: 0px; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; PADDING-TOP: 0px
}
UL#listfolder_%5\$s LI {
    LIST-STYLE: none; MARGIN: 0px; PADDING-BOTTOM: 0px; PADDING-LEFT: 0px; PADDING-RIGHT: 0px; PADDING-TOP: 0px
}
UL#listfolder_%5\$s LI {
    MARGIN-TOP: -1px
}
UL#listfolder_%5\$s DIV {
    MARGIN-TOP: -1px
}
UL#listfolder_%5\$s A {
    BACKGROUND-COLOR: #%2\$s; 
    BORDER-BOTTOM: 1px solid silver; BORDER-LEFT: 1px solid silver; BORDER-RIGHT: 1px solid silver; BORDER-TOP: 1px solid silver; 
    COLOR:#%1\$s; CURSOR: hand; DISPLAY: block; 
    FONT-FAMILY: verdana, arial, sans-serif; FONT-SIZE: 12px; LETTER-SPACING: 0.1em; 
    PADDING-BOTTOM: 3px; PADDING-LEFT: 10px; PADDING-RIGHT: 7px; PADDING-TOP: 3px; POSITION: relative; TEXT-DECORATION: none
}
UL#listfolder_%5\$s A:visited {
    BACKGROUND-COLOR: #%2\$s; 
    BORDER-BOTTOM: 1px solid silver; BORDER-LEFT: 1px solid silver; BORDER-RIGHT: 1px solid silver; BORDER-TOP: 1px solid silver; 
    COLOR:#FFFFFF; CURSOR: hand; DISPLAY: block; 
    FONT-FAMILY: Verdana, Arial, sans-serif; FONT-SIZE: 12px; LETTER-SPACING: 0.1em; 
    PADDING-BOTTOM: 3px; PADDING-LEFT: 10px; PADDING-RIGHT: 7px; PADDING-TOP: 3px; POSITION: relative; TEXT-DECORATION: none
}
UL#listfolder_%5\$s A:hover {
    BACKGROUND-COLOR: #%4\$s; COLOR: #%3\$s}
UL#listfolder_%5\$s A:unknown {
    BACKGROUND-COLOR: #%4\$s; COLOR: #%3\$s}
    ";

var $template_header = "
<!-- this prevents \'events fall through the menu\' bug in win/ie --><!--[if gte IE 5]>
<STYLE type=text/css>
UL#listfolder_%5\$s DIV {
    BACKGROUND-COLOR: #%2\$s;
}
</STYLE>
<![endif]-->
<SCRIPT type=text/javascript src='/scripts/css_menu.js'></SCRIPT>
";

var $template_footer = "
<SCRIPT type=text/javascript>
    menu.txt_color = '#%1\$s';
    menu.bgcolor = '#%2\$s';
    menu.txt_color_hover = '#%3\$s';
    menu.bgcolor_hover = '#%4\$s';

    loadCSSMenu( 'listfolder_%5\$s' );
</SCRIPT>
";

    function AMP_MenuComponent_UL_CSSroot( &$menu, $def ) {
        $this->init( $menu, $def );
    }

    function scriptStyle( $template ) {
        if (($styleset = array_combine_key($this->css_template_vars, $this->getStyle()))) {
            if (isset($styleset['id'])) $styleset['id']= $this->id;
            $openheart= vsprintf($template, $styleset);
            return $openheart;

        }
        return false;
    }

    function output ($returnChildren=false) {
        $menu_output = PARENT::output($returnChildren);
        return $this->scriptStyle( $this->template_header ).
                $menu_output.
                $this->scriptStyle( $this->template_footer );
    }
}
?>
