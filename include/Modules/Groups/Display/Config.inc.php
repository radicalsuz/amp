<?php

if ( !defined('AMP_CONTENT_INTROTEXT_ID_GROUPS' ) ) define( 'AMP_CONTENT_INTROTEXT_ID_GROUPS', 58 );

//Default list behavior
if ( !( defined( 'AMP_MODULE_GROUPS_LIST_DISPLAY_STANDARD') && AMP_MODULE_GROUPS_LIST_DISPLAY_STANDARD )) {
    $list_options['display_format']='groups_layout_display';

    $sort_options['default_sortname'] = "Location";
    $sort_options['default_orderby']  = '(if(Country="USA",1,if(Country="CAN",2,if((isnull(Country) or Country=""),3,Country)))),State,City,Company';
    $sort_options['default_select']   = "Concat( if(!isnull(Country), Concat(Country, ' - '),''), if(!isnull(State), Concat(State, ' - '),''), if(!isnull(City), City,''))";

}

//Display sensitivity for legacy compatibility
function AMP_legacy_groups_get_display( $gdisplay ) {
    global $srch_options;
    global $sort_options;
    global $list_options;

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
            $list_options['subheader3'] = "subregion";
            //Sort choices
            $sort_options['default_orderby']  = '(if(Country="USA",1,if(Country="CAN",2,if((isnull(Country) or Country=""),3,Country)))),State,custom4,City,Company';
            $sort_options['default_select']   = "if( ( !isnull( custom4) and custom4 !='0' and custom4 != '' and state='CA'), custom4, '') as subregion, Concat( if(!isnull(Country), Concat(Country, ' - '),''), if(!isnull(State), Concat(State, ' - '),''), if(!isnull(City), City,'')), if ( UPPER( State ) != 'INTL', State, '' ) as State";
            #$sort_options['default_select']   = "custom4 as subregion, Concat( if(!isnull(Country), Concat(Country, ' - '),''), if(!isnull(State), Concat(State, ' - '),''), if(!isnull(City), City,''))";
            break;
        default:
    }
}
?>
