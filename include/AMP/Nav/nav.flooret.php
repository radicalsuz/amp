<?php

/*********************
03-16-2005  v3.01
Module:  Navigation
Description:  displays an expanding navigation menu that lists 
top level sections and one level of subsections
CSS: nav_sub_list, nav_list, nav_active, nav_sub_active
SYS VARS: $MM_type
To Do: 
Touch: Margot
*********************/ 



// function that makes a nav that shows sub sections if in that section

/*
function nav_sub_content($type) {
	global $dbcon;
	$html = '<ul class="nav_sub_list">';
	$sql = "select title,id, linktext from articles where type = $type and publish =1 and (class !=2 and class !=8 ) order by pageorder, id asc";
	$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
	while (!$R->EOF) {
		if ($R->Fields("linktext")) {
			$link = $R->Fields("linktext");
		}
		else {
			$link = $R->Fields("title");
		}
		$html .= '<li class="nav_sub_list"><a href="article.php?id='.$R->Fields("id").'" class="nav_sub_list">'.$link.'</a></li>';
		$R->MoveNext();
	}

	$html .= '</ul>';
	return $html;
}

function nav_sub_section($type) {
	global $dbcon;
	$html = '<ul class="nav_sub_list">';
		$sql = "select type, id from articletype where parent = $type and usenav =1 order by textorder, id asc";
		$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
		while (!$R->EOF) {
			$html .= '<li class="nav_sub_list"><a href="section.php?id='.$R->Fields("id").'" class="nav_sub_list" >'.$R->Fields("type").'</a></li>'; 
			$R->MoveNext();
	} 
	$html .= '</ul>';
	return $html;
}



function nav_sub_both($type) {
	global $dbcon;
	$html = '<ul class="nav_sub_list">';
		$sql = "select type, id from articletype where parent = $type and usenav =1 order by textorder, id asc";
		$R=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
		while (!$R->EOF) {
			$html .= '<li class="nav_sub_list"><a href="section.php?id='.$R->Fields("id").'" class="nav_sub_list" >'.$R->Fields("type").'</a></li>'; 
			$R->MoveNext();
	} 


	$sql = "select title,id, linktext from articles where type = $type and publish =1 and (class !=2 and class !=8 ) order by pageorder, id asc";
	$C=$dbcon->Execute($sql) or DIE('Could not load the sub navigation information'.$sql.$dbcon->ErrorMsg());	
	while (!$C->EOF) {
		if ($C->Fields("linktext")) {
			$link = $C->Fields("linktext");
		}
		else {
			$link = $C->Fields("title");
		}
		$html .= '<li class="nav_sub_list"><a href="article.php?id='.$C->Fields("id").'" class="nav_sub_list">'.$link.'</a></li>';
		$C->MoveNext();
	}

	$html .= '</ul>';
	return $html;
}


// function that builds Nav that shows top level sections
function nav_menu_dd($type){
	global $dbcon, $MM_type;
	$sql = "select type, id, listtype  from articletype where parent = 1 and usenav =1 order by textorder, id asc";
		$subsql = "select type, parent, id, listtype  from articletype where id = $type and usenav =1 order by textorder, id asc";
	
    $R=$dbcon->Execute($sql) or DIE('Could not load the navigation information'.$sql.$dbcon->ErrorMsg());	
	$subsections=$dbcon->Execute($subsql) or DIE('Could not load the navigation information'.$sql.$dbcon->ErrorMsg());	

    if (!isset($html)) $html = '';
	
    $html .= '<ul class="nav_list">' ;

	while (!$R->EOF) {
        if (($type == $R->Fields("id")) ||  ($R->Fields("id") == $subsections->Fields("parent")) ) {
            $html .= '<li ><a href="section.php?id='.$R->Fields("id").'" class="nav_active" >'.$R->Fields("type").'</a></li>';
        } else { 
		    $html .= '<li class="nav_list"><a href="section.php?id='.$R->Fields("id").'" class="nav_list">'.$R->Fields("type").'</a></li>';
        }
		if (($type == $R->Fields("id")) ||  ($R->Fields("id") == $subsections->Fields("parent")) ) {
			if ($R->Fields("listtype")  == 5) {
				$html .= nav_sub_section($R->Fields("id"));
			} else if ($R->Fields("listtype")  == 1)  {
				$html .= nav_sub_content($R->Fields("id"));
			} else {
				$html .= nav_sub_both($R->Fields("id"));
			}
		}
		$R->MoveNext();
	}
	$html .= '</ul>';

	return $html;
}
*/

function nav_menu_simple( $current_id ) {
    //grab a map of the content system
    $map = AMPContent_Map::instance( );
    
    //get the subsections, in order, of the root section
    $sections_order = $map->getChildren( AMP_CONTENT_MAP_ROOT_SECTION );
    if ( !$sections_order ) return false;

    //get a list of live sections
    $sections_live = AMP_lookup( 'sectionsLive');

    //filter the top-level sections to make sure they are live
    $sections = array_combine_key( $sections_order , $sections_live );

    $links = array( );
    $renderer = AMP_get_renderer( );

    foreach( $sections as $id => $name ) {
        $section_is_active = ( ( $id == $current_id ) || ( $id  == $map->getParent($current_id) ) );
        $link_css_class = $section_is_active ? 'nav_active' : 'nav_list';

        $links[ $id ] = $renderer->link( AMP_url_add_vars( AMP_CONTENT_URL_LIST_SECTION, array( "type=" . $id )),
                            $name, array( 'class' => $link_css_class ));
        $list_items[ $id ] = $renderer->simple_li( $links[$id], array( 'class' => 'nav_list'));
        if ( $section_is_active ) {
            $list_items[ $id ] .= nav_sub_listing( $id );
        }
        
    }

    return $renderer->simple_ul( join( "\n", $list_items), array( 'class' => 'nav_list'));

}

function nav_sub_listing( $section_id ) {
    $map = AMPContent_Map::instance( );
    $live_sections = AMP_lookup( 'sectionsLive' );
    $listtype = $map->readSection( $section_id, 'listtype');

    $include_articles =     !( $listtype == AMP_SECTIONLIST_SUBSECTIONS );
    $include_subsections =  !( $listtype == AMP_SECTIONLIST_ARTICLES );

    $links = array( );
    $children = $map->getChildren( $section_id );
    $renderer = AMP_get_renderer( );

    if ( $include_subsections && $children ) {
        $child_sections = array_combine_key( $children, $live_sections );
        foreach( $child_sections as $child_id => $child_name ) {
            $links[] = $renderer->link( AMP_url_add_vars( AMP_CONTENT_URL_LIST_SECTION, array( 'type='. $child_id )),
                                    $child_name, array( 'class' => 'nav_sub_list'));

        }
    }

    $articles = AMP_lookup( 'articleLinksBySectionLive', $section_id );
    if ( $include_articles && $articles ) {
		$page = AMPContent_Page::instance();
		$article_id = $currentPage->getArticleId();
        foreach( $articles as $id => $name ) {
			$link_class = 'nav_sub_list';
			if ($article_id && ($id == $article_id)) {
				$link_class= 'nav_sub_active';
			}

            $links[] = $renderer->link( AMP_url_add_vars( AMP_CONTENT_URL_ARTICLE, array( 'type='. $id )), 
                                $name, array( 'class' => $link_class ));
        }

    }

    return $renderer->UL( $links, array( 'class' => 'nav_sub_list'), array( 'class' => 'nav_sub_list'));
}


function nav_flooret_output( ) {
    $page = AMPContent_Page::instance( );
    $section_id = $page->getSectionId( );
    if ( !$section_id ) return false;
    return nav_menu_simple( $section_id );
}

print nav_flooret_output( );

//echo nav_menu_dd($MM_type);

?>
