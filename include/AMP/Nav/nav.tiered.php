<?php
function createTieredNav($options){
    $mega_nav = new AMP_Nav_TieredNav( $options);
    return $mega_nav->execute( );
}

# CLASS:    AMP_Nav_TieredNav
#
# Summary:      This nav will display Sections, Articles, or both in a 2-Tiered style.
# Parameters:   display_sections: True to display subsections and sibling sections of current section
#               display_articles: True to display articles in current section as well
#               NOTE: setting false to both display_sections and display_articles
#                     will cause the nav to only display subsections of the current section,
#                     or sibling sections if there are no subsections.
# Author: Ted and Margot
#
require_once ( 'AMP/Content/Map.inc.php');
class AMP_Nav_TieredNav{

var $current_id;
var $current_article_id = false;
var $map;
var $display_sections;
var $display_articles;

function AMP_Nav_TieredNav( $options = array()){
    $this->__construct($options);
}

function __construct($options = array()){
    $this->display_sections = ( isset( $options['display_sections']) && $options['display_sections']) ?
                                $options['display_sections'] : false;
    $this->display_articles = ( isset( $options['display_articles']) && $options['display_articles']) ?
                                $options['display_articles'] : false;
    $this->current_id = AMP_current_section_id();
    $page = AMPContent_Page::instance( );
    if ( $current_article = $page->getArticle( )) {
        $this->current_article_id= $current_article->id;
    }
    $this->map = &AMPContent_Map::instance( );
}

function execute( ){
    $html = '';

#   Figure out what the top level section is for the current state
    $parent = $this->map->getParent( $this->current_id);
    $depth = count( $this->map->getAncestors( $this->current_id));
    if ( $parent && $parent == 1){
        $top =  $this->current_id;
    } elseif ( ( $depth > 2) && !($this->map->hasChildren( $this->current_id)) && ( $this->display_sections)){
        $top= $this->map->getParent( $parent);
    }else {
        $top = $parent;
    }

#   Build a Header for the Nav that links to the top level section.
	$html .='<div class="nav_header"><a href="section.php?id='.$top.'"/>'.
            $this->map->getName( $top).'</a></div>';

#   Build the list and return the html
    $html .= $this->build_list(  $top);
    return $html;
}

#   FUNCTION:  build_list
#   SUMMARY:   Builds the list of sections and/or articles,
#                       recursively builds a list of subsections if needed.
#

function build_list( $top, $class ='', $display_sections =true){
 	$html = "<ul class ='sidelist $class'>";
    $list_items = $this->map->getChildren( $top);

#       first build a list of sections
#       NOTE:   this always builds the list of top level sections
#               only builds a list of subsections if display_sections is set
    if ( $display_sections && $list_items){
        foreach ( $list_items as $id){
            $section = new Section(	AMP_Registry::getDbcon(), $id);
            if (!$section->isLive()) continue;
            if ( $id == $this->current_id) {
                $current = 'current_';
                $html .= $this->build_list_item( $id, $this->map->getName( $id), $class, $current);
                $html .= '<li class="sidelist '.$class.'">'.
                        $this->build_list( $id,'indent',$this->display_sections).
                        '</li>';
            } else {
                $html .= $this->build_list_item( $id, $this->map->getName( $id), $class);
                $children = $this->map->getChildren( $id);

#                   see if the current section is a child of one of the sections we are listing.
#                   if so, make a list.
                if ( $children){
                    foreach ( $children as $child_id){
                        if ( $child_id == $this->current_id) {
                            $html .= $this->build_list( $id,'indent',$this->display_sections);
                        }
                    }
                }
            }
        }
    }

#   build list of articles
    if ( $this->display_articles){
        $section = new Section(	AMP_Registry::getDbcon(), $top);
        $list_items =  AMP_lookup( 'articles_by_section_logic_live', $top );
        if( $list_items){
            foreach ( $list_items as $id=>$title){
                if ( $id == $this->current_article_id) {
                    $current = 'current_';
                    $html .= $this->build_list_item_article( $id, $title, $class, $current);
                } else {
                    $html .= $this->build_list_item_article( $id, $title, $class);
                }
            }
        }
    }
	$html .= '</ul>';
    
    return $html;
}

function build_list_item( $id, $title, $class= '', $current = ''){
	$section = new Section(	AMP_Registry::getDbcon(), $id);
	if (!$section->isLive()) return '';
    $html = '';
    $html .= '<li class="sidelist '.$class.'">
<a class="'.$current.'sidelist_link" href="article.php?list=type&type='.$id.'">'.$title.'</a>
</li>';
    return $html;
}

function build_list_item_article( $id, $title, $class= '', $current = ''){
    $html = '';
    $html .= '<li class="sidelist '.$class.'">
<a class="'.$current.'sidelist_link" href="article.php?id='.$id.'">'.$title.'</a>
</li>';
    return $html;
}
}
?>
