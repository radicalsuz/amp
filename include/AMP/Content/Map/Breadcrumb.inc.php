<?php
/* * * * * * * * * * *
 *
 * AMP_Breadcrumb_Content
 *
 * Creates a view of the current ancestry
 * within the content hierarchy
 *
 * AMP 3.5.0
 * 2005-07-26
 * Author: austin@radicaldesigns.org
 *
 * * * */

require_once( 'AMP/Content/Map.inc.php' );
require_once( 'AMP/Content/Article.inc.php' );
require_once( 'AMP/Content/Section.inc.php' );
require_once( 'AMP/Content/Class.inc.php' );
require_once( 'AMP/System/IntroText.inc.php' );

define( 'AMP_CONTENT_PAGETYPE_ARTICLE', 'article' );
define( 'AMP_CONTENT_PAGETYPE_LIST', 'list' );
define( 'AMP_CONTENT_PAGETYPE_TOOL', 'tool' );

class AMP_Breadcrumb_Content {

    var $dbcon;
    var $separator = "&nbsp;&nbsp;<b>&#187;</b>&nbsp;&nbsp;";
    var $baseURL;
    var $itemHref = "article.php?list=type&type=";
    var $css_class = "breadcrumb";
    var $toplink;
    var $topname= "Home";

    var $map;
    var $top = 1;
    var $current_section;

    var $max_text_length = 35;
    var $content_type;

    var $actions = false;
    var $use_template = false;

    function AMP_Breadcrumb_Content ( &$dbcon ) {
        $this->init( $dbcon );
    }


    #####################
    ###  core methods ###
    #####################

    function init( &$dbcon ) {
        $this->dbcon = &$dbcon;
        $this->setBase();
        $this->_getMap();
    }

    function &instance() {
        static $breadcrumb = false;
        if (!$breadcrumb) $breadcrumb = new AMP_Breadcrumb_Content ( AMP_Registry::getDbcon() );

        return $breadcrumb;
    }

    function execute() {
        if (!$this->hasTemplate()) return $this->_HTML_wrapper ($this->_HTML_output());
        return $this->_HTML_wrapper( $this->_HTML_outputTemplated() );
    }

    function _getMap() {
        $this->map = &AMPContent_Map::instance();
    }

    function setBase( $page = "index.php" ) {
        $reg = &AMP_Registry::instance();
        $this->baseURL="/";
        #$this->baseURL = $reg->getEntry( AMP_REGISTRY_SETTING_SITEURL );
        $this->toplink = $this->baseURL . $page;
        $this->current_section = $this->top;
    }

    #######################################
    ### public location setting methods ###
    #######################################

    function findClass( $class_id ) {
        $location = &new ContentClass( $this->dbcon, $class_id );
        $this->current_section = $section;
        $this->current_element = $this->_trimText( $location->getName() );
        $this->content_type = AMP_CONTENT_PAGETYPE_LIST;
    }

    function findArticle( $article_id ) {
        $location = &new Article( $this->dbcon, $article_id );
        $this->current_section = $location->getParent() ;
        $this->current_element = $this->_trimText( $location->getName() );
        $this->content_type = AMP_CONTENT_PAGETYPE_ARTICLE;
    }

    function findSection( $section_id ) {
        #$location = new Section( $this->dbcon, $section_id );
        $this->current_section = $section_id;
        $this->current_element = "";
        $this->content_type = AMP_CONTENT_PAGETYPE_LIST;
    }

    function findIntroText( $introtext_id ) {
        $location = &new AMPSystem_IntroText( $this->dbcon, $introtext_id );
        $this->current_element = $this->_trimText( $location->getData( 'title' ) );
        $this->current_section = $location->getSection();
        $this->content_type = AMP_CONTENT_PAGETYPE_TOOL;
    }


    ####################################
    ### public configuration methods ###
    ####################################

    function addActions() {
        $this->actions = true;
    }

    function hasActions() {
        return $this->actions;
    }

    function addTemplate() {
        $this->use_template = true;
    }

    function hasTemplate() {
        return $this->use_template;
    }

    function setSeparator( $html ) {
        $this->separator = $html;
    }


    #############################
    ###  private HTML methods ###
    #############################

    function _HTML_wrapper( $output ) {
        return "<!-- BEGIN BREADCRUMB CODE -->\n". $output . "<!-- END BREADCRUMB CODE --><br>\n";
    }


    function _HTML_start() {
        return  
                "<span class='". $this->css_class . "'>" .
                "<a href=\"".$this->toplink. "\" class='" . $this->css_class . "'>" . $this->topname ."</a> ";
    }

    function _HTML_ancestryLinks() {
        $links = $this->_ancestryLinks();
        if (empty($links)) return false;
        return $this->separator . join( $this->separator, $links );
    }

    function _HTML_output() {
        return  $this->_HTML_start() .
                $this->_HTML_ancestryLinks() .
                $this->_HTML_end();
    }

    function _HTML_end() {
        $final_link = "";
        if (isset($this->current_element) && $this->current_element) {
            $final_link =  $this->separator . $this->current_element . "\n" ;
        }

        return $final_link . "</span>\n";
    }

    function _HTML_outputTemplated() {
        $template_start = 
            '<table width="100%" border="0" cellspacing="0" cellpadding="3">'.
            "<tr><td>\n";
        $template_split = '</td><td>';
        $template_end = "</td></tr></table>\n";

        $html_end = $template_end;
        if ($this->hasActions()) $html_end = $template_split . $this->_HTML_outputActions() . $template_end;

        return  $template_start .
                $this->_HTML_output() .
                $html_end;
    }

    function _HTML_outputActions() {
        $actions_start = '<div align="right">' ."\n".
            '<table width="104" border="0" cellpadding="0" cellspacing="0">'. "\n";
        $actions_end = '</table></div>';
        $row_template = "<tr><td width='24' valign='middle' class='".$this->css_class."'>%1\$s</td>\n".
                        "<td valign='middle' width='88'>%2\$s</td></tr>\n";

        if (!($actions = $this->_buildActions())) return false;

        $output = $actions_start;
        foreach ($actions as $action => $action_def ) {
            $output .= vsprintf( $row_template, $action_def );
        }

        return $output . $actions_end;
    }


    ###################################
    ### private html helper methods ###
    ###################################

    function _buildActions() {
        $urlvars = AMP_URL_Values();
        $actions = array();
        $actions['email'] = array(
                'image' => '<img src="img/email.gif" align="top">',
                'link' => '<a href="javascript:openform(\'mailto.php\')"  class="'.$this->css_class.'">E-Mail Page</a>' );
        if (! ($this->content_type == AMP_CONTENT_PAGETYPE_ARTICLE || $this->content_type == AMP_CONTENT_PAGETYPE_LIST )) return $actions;

        $actions['print']  = array( 
                'image' => '<img src="/img/print.gif" align="top">',
                'link' => '<a href="print_article.php?'. join( "&", $urlvars) .'" class="'.$this->css_class."\">Printer Safe</a>" );

        return $actions;
    }

    function _ancestryLinks() {
        $links = array();
        if (!isset($this->current_section)) return $links;
        $ancestors = $this->map->getAncestors( $this->current_section );
        if (empty($ancestors)) return $links;

        foreach ( $ancestors as $id => $section_name ) {
            $section_name = $this->_trimText( $section_name );
			$new_item = "<a href=\"" . $this->baseURL . $this->itemHref . $id .
                        "\" class=\"". $this->css_class . "\">" . $section_name . "</a>";
            array_unshift( $links, $new_item );
		}

        return $links;
    }

    function _trimText( $text ) {
        $trimmed = strip_tags( $text );
        if (! (strlen( $trimmed ) > $this->max_text_length) ) return $trimmed; 

        $end_item = " ...";
        $trimmed = substr( trim($trimmed), 0, $this->max_text_length );
        if ( !($pos = strrpos( $trimmed, " " ))) return $trimmed . $end_item;

        return substr( $trimmed, 0, $pos ) . $end_item;
    }

}
?>
