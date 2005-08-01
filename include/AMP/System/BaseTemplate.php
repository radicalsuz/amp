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
require_once( 'AMP/System/Menu.inc.php');

class AMPSystem_BaseTemplate {
    
    var $title;
    var $form_id;
    var $modid;

    var $script_files = array(
        '../scripts/system_header.js',
        'Connections/popcalendar.js',
        '../Connections/functions.js' );

    var $css_files = array( 'system_interface.css' );

    var $page_title;

    var $nav_name;

    function AMPSystem_BaseTemplate() {
        $this->page_title = $GLOBALS['SiteName'] . ' Administration';
        $reg = & AMP_Registry::instance();

        $this->page_encoding = $reg->getEntry( AMP_REGISTRY_SETTING_ENCODING );
    }

    function instance() {
        static $basetemplate = false;
        if (!$basetemplate) $basetemplate = new AMPSystem_BaseTemplate();
        return $basetemplate;
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

    function setToolName( $nav_name ) {
        if ($nav_name == 'module') return $this->setToolName('tools');
        $this->nav_name = $nav_name;
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
        $reg = AMP_Registry::instance();
        $name = $reg->getEntry(AMP_REGISTRY_SETTING_SITENAME);
        $admEmail = $reg->getEntry(AMP_REGISTRY_SETTING_EMAIL_SYSADMIN);
        return  "AMP " . AMP_SYSTEM_VERSION_ID . " for $name\n" .
                "Please report problems to $admEmail";
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
        
        return '<meta http-equiv="Content-Type" content="text/html; charset=' . $this->page_encoding .'">';
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
        $menu = & new AMPSystem_Menu();
        return $menu->output();
        
    }


    function _HTML_pageHeader() {
        $output = "<html>\n<head>\n" . $this->_HTML_browserTitle();
        $output .= $this->_HTML_docType();
        $output .= $this->_HTML_styleSheets();
        $output .= $this->_HTML_javaScripts();
        return $output . "</head>\n";
    }

    function _HTML_systemLogo() {
        $pictype = 'png';
        if (getBrowser() == 'win/ie') $pictype = 'gif';
        return '<nobr><img src="images/amp-megaphone.'. $pictype .'" align ="middle" style="padding-right:15px">'."\n";
    }

    function _HTML_systemTitle() {
        $reg = AMP_Registry::instance();
        $url = $reg->getEntry(AMP_REGISTRY_SETTING_SITEURL);
        $name = $reg->getEntry(AMP_REGISTRY_SETTING_SITENAME);
        return '<span class="toptitle"><a href="' . $url .'" class="toptitle">' . $name . '</a> Administration</span></nobr>';
    }

    function _HTML_topLinks() {
        return '<p class = "toplinks"><a href="index.php"  class="toplinks" >Home</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' .
               'User: ' . $_SERVER['REMOTE_USER'] . '&nbsp;&nbsp;&nbsp;&nbsp;' . 
               '<a href="logout.php"  class="toplinks" >Logout</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</p>';
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
                    '<fieldset style=" border: 1px solid grey; margin:20px; padding:10px;">';
        return $output;
    }

    function _HTML_systemNav() {
        $navEngine = &new AMPSystem_NavManager();
        $nav_name = $this->nav_name;

        if ($this->form_id) {
            $form_name = $navEngine->buildFormNav( $this->form_id );
            if ($form_name) $nav_name = $form_name;
        }

        if (!($output = $navEngine->render( $nav_name ))) {
            $output = $navEngine->render( 'content' );
        }
        return $output;
    }

}
?>
