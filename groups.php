<?php

/*****
 *
 * UserData Listing Page
 * 
 * Allows search for records 
 *
 * 
 *
 *****/
$modid=5;
require_once( 'AMP/BaseDB.php' );
require_once('AMP/UserData/Set.inc.php');

$intro_id = 58;

//Default list behavior
$list_options['display_format']='groups_layout_display';

$sort_options['default_sortname'] = "Location";
$sort_options['default_orderby'] = '(if(Country="USA",1,if(Country="CAN",2,if((isnull(Country) or Country=""),3,Country)))),State,City,Company';
$sort_options['default_select'] = "Concat( if(!isnull(Country), Concat(Country, ' - '),''), if(!isnull(State), Concat(State, ' - '),''), if(!isnull(City), City,''))";


//Display sensitivity for legacy compatibility
$gdisplay = isset($_REQUEST['gdisplay'])?$_REQUEST['gdisplay']:false;
switch ($gdisplay) {
    case 2:
        //international
        $srch_options['criteria']['value'] = array("Country != 'USA'");
        break;

    //Alphabetical listings
    case 3:
        //alphabetical subheaders
        $sort_options['default_sortname'] = "Group";
        $sort_options['default_select'] = "Company as `Group`";
        $sort_options['default_orderby'] = "Company";
        $list_options['subheader'] = 'alpha';
        break;
    case 4:
        //alphabetical w/o subheaders
        $list_options['subheader']='';
        $sort_options['default_sortname'] = "Group";
        $sort_options['default_select'] = "Company as `Group`";
        $sort_options['default_orderby'] = "Company";
        break;
    case 5:
        $list_options['subheader'] = "Country";
        $list_options['subheader2'] = "State";
        $list_options['subheader3'] = "City";
        break;
    case 6:
        if (isset($_REQUEST['field'])) {
            $_REQUEST['sortby'] = $_REQUEST['field'];
        }
        break;
    case 7:
        $list_options['subheader'] = "Country";
        $list_options['subheader2'] = "State";
        //Sort choices
        break;
    default:
}



if (!isset($_REQUEST['modin']))$_REQUEST['modin']=2;
$modin=$_REQUEST['modin'];

$admin=false;
$userlist=&new UserDataSet($dbcon, $modin, $admin);

$sub = isset($_REQUEST['btnUDMSubmit']);
$uid= isset($_REQUEST['uid'])?$_REQUEST['uid']:false;
$uid= isset($_REQUEST['gid'])?$_REQUEST['gid']:$uid;

if ($uid && $modin) {

    $userlist->uid = $uid;
    $list_options['detail_format'] = 'groups_detail_display';
    $output= $userlist->output('DisplayHTML', $list_options); 

} else { 
    #$userlist->registerPlugin("Output", "Index");
    if (is_array($sort_options)) {
        $sort = $userlist->getPlugins("Sort");
        $sort_plugin = $sort[key($sort)];
        $sort_plugin->setOptions($sort_options);
    }

    //restrict to a modin, even without a searchform specified
    $searchform = $userlist->getPlugins('SearchForm');
    if (!isset($searchform)||$searchform==false) {
        if (is_array($srch_options['criteria']['value'])) {
            $srch_options['criteria']['value'][] = "modin=".$modin;
        } else {
            $srch_options['criteria']['value'] = array("modin=".$modin);
        }
    }

    //display result list
    $userlist->doAction('Search', $srch_options); 
    $output=$userlist->output_list('DisplayHTML', $list_options); 
}

require_once( 'AMP/BaseTemplate.php' );

print $output;

        
// Append the footer and clean up.
require_once( 'AMP/BaseFooter.php' );

?>
