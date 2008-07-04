<?php

$modid= AMP_MODULE_ID_LINKS;
$intro_id = AMP_CONTENT_PUBLICPAGE_ID_LINKS_DISPLAY;

if (isset($_GET["name"]) && $_GET['name']) {
	 $name_link = $_GET["name"];
  	ampredirect ("links.php#$name_link");  
}

require_once("AMP/BaseTemplate.php");
require_once("AMP/BaseModuleIntro.php");
require_once( 'AMP/Content/Link/Link.php');
require_once( 'AMP/Content/Link/Type/Type.php');
require_once( 'AMP/Display/Template.php');

$link_source = new AMP_Content_Link( AMP_Registry::getDbcon( ) );
$link_source->addCriteriaGlobal( $link_source->makeCriteria( array( 'live' => true )));

$link_type_source = &new Link_Type( $dbcon );
$requested_section =  (isset($_GET['linktype']) && $_GET['linktype']) ? $_GET['linktype'] : false; 
$content_map = AMPContent_Lookup::instance( 'sectionMap' );

$link_types = $link_type_source->search( $link_type_source->makeCriteria( array( 'live' => true )));
$display_types = false;

if ( $requested_section ) {

    if ( !isset( $content_map[ $requested_section ])) {
        AMP_make_404( );
    } else {
        $link_source->addCriteriaGlobal( $link_source->makeCriteria( array( 'section'  => $requested_section )));
        $renderer = &new AMP_Display_Template( 'AMP/Content/Display/heading.inc.thtml' );
        $section_names = &AMPContent_Lookup::instance( "sections" );
        $renderer->set_property( 'heading', $section_names[ $requested_section ]);
        print $renderer->execute( );
    }
    
} 

if ( $link_types ) {
    require_once( 'AMP/System/Data/Tree.php');
    $link_tree = &new AMP_System_Data_Tree( $link_type_source );
    $link_map = $link_tree->select_options( );
    AMP_display_linkset( $link_source, array( 'noLinkType' => true ));

    foreach( $link_map as $link_id => $link_name ) {
        AMP_display_linkset( $link_source, array( 'linkType' => $link_id ), $link_tree->get_depth( $link_id ));
    }
    
} else {
    AMP_display_linkset( $link_source );
}


function AMP_display_linkset( &$link_source, $criteria = array( ), $level=0  ){
    //static $link_source = false;
    $source_set = $link_source->search( $link_source->makeCriteria( $criteria ));
    if ( !$source_set ) return; 
    print "\n<div class='heading_".$level."' style='padding-left:".( $level*50 ). "px;'>\n";
    foreach( $source_set as $link ) {
        print AMP_display_link( $link );
    }
    print "\n\n</div>\n";
}

function AMP_display_link( &$source ){
    static $renderer = false;
    if ( !$renderer ) $renderer = new AMP_Display_Template( 'AMP/Content/Link/Display/link.inc.thtml' );
    $data = $source->getData( );
    $data['link_type_name'] = $source->getLinkTypeName( );
    //print $data['link_type_name'] . '<BR>';
    $renderer->set_properties( $data );
    return $renderer->execute( );

}

require_once("AMP/BaseFooter.php");

?>
