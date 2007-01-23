<?php

/* * * * * * * * * * *
 *
 *  AMPSystem_BaseTemplate
 *
 *  defines the appearance of the system interface
 *
 *  AMP 3.5.0
 *  2005-07-07
 *  Author: austin@radicaldesigns.org
 *
 *
 * * * * * * **/
require_once( 'AMP/System/Nav/Manager.inc.php' );

class AMPSystem_BaseTemplate {
    
    var $title;
    var $form_id;
    var $modid;

    var $nav_name;

	var $_use_form_nav = true;
    var $_header;
    var $_menu;

    function AMPSystem_BaseTemplate() {
        $this->__construct( );
    }

    function __construct( ){
        require_once( 'AMP/System/Header.inc.php');
        $this->page_title = AMP_SITE_NAME . ' Administration';
        $this->_header = &AMPSystem_Header::instance( );
        $this->_init_menu( );

    }

    function _init_menu( ){
        require_once( AMP_SYSTEM_MENU_PATH );
        if ( AMP_SYSTEM_MENU_PATH == 'AMP/System/Menu.inc.php') {
            $this->_menu = & new AMPSystem_Menu( true );
            $this->_menu->init_header( );
            return;
        }

        $this->_menu = & new AMP_System_Menu_Display( );
    }

    function &instance() {
        static $basetemplate = false;
        if (!$basetemplate) $basetemplate = new AMPSystem_BaseTemplate();
        return $basetemplate;
    }

    function &getHeader( ){
        return $this->_header;
    }

    function execute( $content ){
        return  $this->outputHeader( )
                . $content
                . $this->outputFooter( );
    }

    ####################################
    ### Public Configuration Methods ###
    ####################################

    function setTool( $modid ) {
        $module_names = AMPSystem_Lookup::instance('Modules');
        $form_lookup = AMPSystem_Lookup::instance('FormsbyTool');
        $this->title =      isset($module_names[$modid]) ?  $module_names[$modid]   : false;
        $this->form_id =    isset ($form_lookup[$modid]) ?  $form_lookup[$modid]    : false;
        $this->modid =$modid;
    }

    function setForm( $form_id ) {
        $this->form_id =    $form_id;

    }

    function setToolName( $nav_name ) {
        if ($nav_name == 'module') return $this->setToolName('tools');
        $this->nav_name = $nav_name;
    }

    function setNavs( $nav_set ){
        $this->nav_name = $nav_set;
    }

	function useFormNav( $usenav = null ) {
		if(isset($usenav)) {
			$this->_use_form_nav = $usenav;
		}
		return $this->_use_form_nav;
	}

    #############################
    ### Public Output Methods ###
    #############################


    function outputHeader() {
        return $this->_HTML_pageHeader() . $this->_HTML_bodyTemplate();
    }

    function outputFooter() {
        return $this->_HTML_systemFooter();
    }


    #####################################
    ### Private Output Helper Methods ###
    #####################################

    function _systemFooterText() {
        return  sprintf( AMP_TEXT_SYSTEM_INTERFACE_FOOTER, AMP_SYSTEM_VERSION_ID, AMP_SITE_NAME, AMP_SITE_ADMIN);
    }


    function _HTML_systemFooter() {
        return "</fieldset>\n</div></td>\n</tr></table>\n" .
                "<p id=\"footer\">\n".
                $this->_HTML_systemFooterText().
                "</p>\n</body>\n</html>";

    }
    function _HTML_systemFooterText() {
        return converttext( $this->_systemFooterText() );
    }


    function _HTML_browserTitle() {
        return '<title>' . $this->page_title . '</title>';
    }

    function _HTML_docType() {
        
        return '<meta http-equiv="Content-Type" content="text/html; charset=' . AMP_SITE_CONTENT_ENCODING .'">';
    }

    function _HTML_styleSheets() {
        if (empty($this->css_files)) return false;
        $output = "";
        foreach ($this->css_files as $css) {
            $output .='<link rel="stylesheet" href="'.$css.'" type="text/css">' ."\n";
        }
        return $output;
    }
    function _HTML_javaScripts() {
        if (empty($this->script_files)) return false;
        $output = "";
        foreach ( $this->script_files as $script ) {
            $output .='<script type="text/javascript" src = "'.$script.'"></script>' ."\n";
        }
        return $output;
    }

    function _HTML_systemMenu() {
        return $this->_menu->execute();
        
    }


    function _HTML_pageHeader() {
        return $this->_header->output( );
        /*
        $output = "<html>\n<head>\n" . $this->_HTML_browserTitle();
        $output .= $this->_HTML_docType();
        $output .= $this->_HTML_styleSheets();
        $output .= $this->_HTML_javaScripts();
        return $output . "</head>\n";
        */
    }

    function _HTML_systemLogo() {
        $pictype = 'png';
        if (getBrowser() == 'win/ie') $pictype = 'gif';
        return '<nobr><img src="/system/images/amp-megaphone.'. $pictype .'" align ="middle" style="padding-right:15px">'."\n";
    }

    function _HTML_systemTitle() {
        return '<span class="toptitle"><a href="' . AMP_SITE_URL .'" class="toptitle">' . AMP_SITE_NAME . '</a> Administration</span></nobr>';
    }

    function _HTML_topLinks() {
        return '<p class = "toplinks">'
                . '<a href="index.php"  class="toplinks" >Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' 
                . '<a href="flushcache.php" id="cache_clear_link" onclick="window.clear_AMP_cache( );return false;" class="toplinks" >Clear&nbsp;Cache</a>&nbsp;&nbsp;&nbsp;&nbsp;'
                . 'User: ' . $_SERVER['REMOTE_USER'] . '&nbsp;&nbsp;&nbsp;&nbsp;'  
                . '<a href="logout.php"  class="toplinks" >Logout</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'
                . '</p>';
    }

    function _HTML_systemHeader() {
        $output  = '<table width="100%" border="0" cellpadding="0" cellspacing="0" bgcolor="#006699">'."\n".
                   '<tr id="header"><td>';
        $output .= $this->_HTML_systemLogo();
        $output .= $this->_HTML_systemTitle();
        $output .= '</td><td align="right" valign="middle" bgcolor="#006699" class="toplinks">';
        $output .= $this->_HTML_topLinks();
        
        $output .= '</td></tr><tr><td id="navlinks" colspan="2">';
        $output .= $this->_HTML_systemMenu();
        $output .= '</td></tr></table>';

        return $output;
    }
    
    function _HTML_bodyTemplate() {
        $output =' <body>'."\n".
            '<table cellpadding="0" cellspacing="0" width="100%" align="center">'."\n".
            '<tr bordercolor="#FFFFFF" bgcolor="#dedede" valign="top">'.
            '<td colspan="4" id="pagetitle">'."\n";

        $output .= $this->_HTML_systemHeader();
        $output .=  '</td></tr><tr><td bgcolor="#dedede" width="160" valign="top">';
        $output .= $this->_HTML_systemNav();
        $output .=  '<br/><br/><img src ="images/spacer.gif" width = "165" height="1"></td>'."\n".
                    '<td valign="top" bgcolor="#FFFFFF" width="100%"><div>' . "\n" .
                    '<fieldset class="system_main_content">';
                    //'<fieldset style=" border: 1px solid grey; margin:20px; padding:10px;">';
        return $output;
    }

    function _HTML_systemNav() {
        $navEngine = &new AMPSystem_NavManager();
        if (isset($this->modid)) $navEngine->setToolId( $this->modid );

        if ($this->form_id && $this->useFormNav()) {
            $navEngine->request( 'form', $this->form_id );
            return $navEngine->execute( );
        } 

        $nav_set = is_array( $this->nav_name ) ? $this->nav_name : array( $this->nav_name );
        foreach( $nav_set as $nav_name ){
            $navEngine->request( $nav_name );
        }
        return $navEngine->execute( );
    }

}
?>
