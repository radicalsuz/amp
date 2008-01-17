<?php
require_once ('AMP/UserData/Plugin.inc.php');
require_once ('AMP/Region.inc.php');
if (file_exists_incpath('custom.layouts.inc.php')) {
    include_once ('custom.layouts.inc.php');
}

class UserDataPlugin_DisplayHTML_Output extends UserDataPlugin {
    
    var $options= array( 
        'subheader'=>array(
                            'available'=>true, 
                            'label'=>'Show subheadings for',
                            'default'=>'',
                            'type'=>'text'),
        'subheader2'=>array(
                            'available'=>true, 
                            'label'=>'Show second-level subheadings for',
                            'default'=>'',
                            'type'=>'text'),
        'subheader3'=>array(
                            'available'=>true, 
                            'label'=>'Show third-level subheadings for',
                            'default'=>'',
                            'type'=>'text'),
        'display_format'=>array('label'=>'List Display Function Name',
                                'default'=>'list_display_default',
                                'available'=>true,
                                'type'=>'text'),
        'detail_format'=>array('label'=>'Detail Display Function Name',
                               'default'=>'detail_display_default',
                               'available'=>true,
                               'type'=>'text'),
        'header_text_list'=>array('label'=>'Intro Text For List Page',
                               'default'=>'1',
                               'available'=>true,
                               'type'=>'select'),
        'header_text_detail'=>array('label'=>'Intro Text For Detail Page',
                               'default'=>'1',
                               'available'=>true,
                               'type'=>'select'),
        'column_count'=>array('label'=>'Columns for display',
                               'default'=>'1',
                               'available'=>true,
                               'type'=>'text',
                               'size'=>'3'
                               ),
        'column_renderer'=>array('label'=>'Column Wrapper Function Name',
                               'default'=>'',
                               'available'=>true,
                               'type'=>'text'),
        '_userid' => array ('default'=>null,
                            'available'=>false)
        );
    
    var $available=true;

    //multiple subheaders - yay!
    var $current_subheader;
    var $current_subheader2;
    var $current_subheader3;
    var $regionset;

    var $alias = array(
            'Name'=>array(
                'f_alias'=>'Name',
                'f_orderby'=>'Last_Name,First_Name',
                'f_type'=>'text',
                'f_sqlname'=>"Concat(if(!isnull(First_Name), First_Name, ''), ' ', if(!isnull(Last_Name), Last_Name, '') )"
             ),
             'Location'=>array(
                'f_alias'=>'Location',
                'f_sqlname'=>"Concat( if(!isnull(Country), Concat(Country, ' - '),''), if(!isnull(State), Concat(State, ' - '),''), if(!isnull(City), City,''))",
                'f_orderby'=>'(if(Country="USA",1,if(Country="CAN",2,if(isnull(Country),3,Country)))),State,City,Company',
                'f_type'=>'text'),
             'Status'=>array(
                'f_alias'=>'Status',
                'f_orderby'=>'publish',
                'f_type'=>'text',
                'f_sqlname'=>'if(publish=1,"Live","Draft")'
              ));

    var $_css_class_container_list_column= 'list_column';
    var $_css_class_container_list = 'list_form';
    var $_css_class_container_list_item = 'list_item';

    var $is_last_column = false;

    function UserDataPlugin_DisplayHTML_Output (&$udm, $instance=null) {   
        $this->init($udm, $instance);
        $this->regionset=new Region;
    }

    function _register_options_dynamic () {
        if ($this->udm->admin) {
            /*
            $udm_mod_id  = $this->dbcon->qstr( $this->udm->instance );
            $modlist_sql = "SELECT   moduletext.id, moduletext.name FROM moduletext, modules
                            WHERE    modules.id = moduletext.modid
                                AND modules.userdatamodid = $udm_mod_id
                            ORDER BY name ASC";
            $modlist_rs  = $this->dbcon->CacheExecute( $modlist_sql )
                or die( "Error fetching module information: " . $this->dbcon->ErrorMsg() );

            $modules[ '' ] = '--';
            while ( $row = $modlist_rs->FetchRow() ) {
                $modules[ $row['id'] ] = $row['name'];
            }
            */
            require_once( 'AMP/UserData/Lookups.inc.php');
            $introtexts = &FormLookup_IntroTexts::instance( $this->udm->instance );
            $this->options['header_text_list']['values']    = array( '' => 'None selected') + $introtexts;
            $this->options['header_text_detail']['values']  = array( '' => 'None selected') + $introtexts;
        }
    }

    function execute ($options=array( )) {
        $options=array_merge($this->getOptions(), $options);
        //Check to see if a single record was specified
        //if so, return detail information for that record 
        $this->udm->modTemplateID = $this->header_text_id();

        // if the UID is set, show only one record with the detail format
        if (isset($this->udm->uid)) {
            $display_function=isset($options['detail_format'])?($options['detail_format']):"display_detail";
            $dataset = $this->udm->getUser( $this->udm->uid );
            $data_item = current($dataset);
            if (!($data_item['publish']||$this->udm->admin)) $dataset = false;
            $subheader_level=0;

        } else { 
            //by default the list display function is used
            $subheader_level = $this->subheader_depth($options);
            $display_function=isset($options['display_format'])?($options['display_format']):"display_item";
            //Retrieve the full results list
            $dataset=$this->udm->getData();

        }
        
        $inclass = method_exists($this, $display_function);
        $comments_plugin = &$this->udm->getPlugin( 'Output', 'Comments' );
        
        $output = '';
        $column_output='';
        $column_length = round( count( $dataset ) / $options['column_count'] );

        $items_output = '';
        $items_count=0;
        $items_size_tracker = array( );
        $items_header_size_tracker = array( );
        $renderer = AMP_get_renderer( );

        //output display format
        foreach ($dataset as $dataitem) {

            //check if a new subheader is needed
            if ($subheader_level){
                $subheader_output = $this->subheader($dataitem, $options, $subheader_level);
                //store the size of the subheader for each entry
                if ( $subheader_output ) $items_header_size_tracker[ strlen( $items_output ) ] = $subheader_output;
                $items_output .= $subheader_output; 

            }

            //run the output function
            if($inclass) {
                //$items_output.=$this->$display_function($dataitem);
                $items_output.=$renderer->div( $this->$display_function($dataitem ), array( 'class' => $this->_css_class_container_list_item ));

            } else {
                $items_output.=$renderer->div( $display_function($dataitem, $options, $this ), array( 'class' => $this->_css_class_container_list_item ));
            }

            //add comments
            if ( $comments_plugin ) {
                $items_output .= $this->addComments( $dataitem, $comments_plugin );
            }

            //column formatting

            $items_count++;
            $size = strlen( $items_output );
            //store the size of each entry in the column
            $items_size_tracker[ $size ] = $items_count;
        }

        if ( !isset( $options['column_count']) || !$options['column_count'] || ( $options['column_count'] ==1)) {
            //return output for single-column lists
            return $items_output;
        }

        //put output into columns by data length
        //this length # includes HTML tags, so further refinement is possible using strip_tags

        $column_size_perfect = strlen( $items_output ) / $options['column_count'];
        $column_end = $column_size_perfect;
        $column_pointer = 0;
        $next_column_header = '';

        foreach( $items_size_tracker as $bytes => $item_count ) {
            //if the current total_bytes is less than the next column break point, keep going 
            if ( $bytes < $column_end ) continue;

            $this->prepare_new_column( );
            $column_size = $bytes - $column_pointer;
            //pulls the column contents from the big $items_output string
            $column_html = $next_column_header . substr( $items_output, $column_pointer, $column_size );
            $column_output .= $this->renderColumn( $column_html, $options );

            $next_column_header = $this->subheader( $dataset[$item_count], $options, $subheader_level ) ;
            $column_end += $column_size;
            $column_pointer += $column_size;

            if ( isset( $items_header_size_tracker[ $bytes ]) && $next_column_header ) {
                $column_pointer = $column_pointer + strlen( $items_header_size_tracker[$bytes]);
            }

            if ( $column_end >= strlen( $items_output )) {
                $this->prepare_new_column( );
                $column_html = $next_column_header . substr( $items_output, $column_pointer );
                $column_output .= $this->renderColumnLast( $column_html, $options );
                break; 
            }
        }
        $output = $this->renderBlock( $column_output );
    

		return $output;
    }

    function prepare_new_column( ) {
        $this->current_subheader3 = '';
        $this->current_subheader2 = '';


    }


    function renderBlock( $html ) {
        $renderer = AMP_get_renderer( );
        return $renderer->div( $html, array( 'class' => $this->_css_class_container_list ));
    }

    function renderColumnLast( $html, $options ) {
        $this->is_last_column = true;
        $stored_class = $this->_css_class_container_list_column;
        $this->_css_class_container_list_column .= " list_column_last";

        $result = $this->renderColumn( $html, $options );
        $this->is_last_column = false;
        $this->_css_class_container_list_column = $stored_class;
        return $result;
    }

    function renderColumn( $html, $options = array( ) ) {
        if ( isset( $options['column_renderer']) && $options['column_renderer']) {
            $column_renderer = $options['column_renderer'];
            return $column_renderer( $html, $this );
        }

        $renderer = AMP_get_renderer( );
        return $renderer->inDiv( 
                    $html,
                    array( 'class' => $this->_css_class_container_list_column )
                    );
    }

    function addComments( $dataitem, &$display  ) {
        if( !isset( $dataitem['id'] )) return false; 
        return $display->execute( array( '_linked_uid' => $dataitem['id']) );
    }

    function setAliases() {
        $this->udm->alias = $this->alias;
    }


    function header_text_id() {
        $options = $this->getOptions();
        if ($this->udm->uid) {
            return $options['header_text_detail'];
        } else {
            return $options['header_text_list'];
        }
    }

    function subheader_depth($options) {
        $level = 0;
        foreach ($options as $option_name=>$option_value) {
            if (strpos($option_name, "subheader")===FALSE ) {
                continue;
            }
            if ( !$option_value ) continue;
            if (strlen($option_name)==9) $level=1;
            else $level = intval(substr($option_name,9));
        }
        return $level;
    }

        
    function subheader($dataitem, $options, $level='') {
        
        $output = "";
        
        // Show alphabetical headers
        $dataitem["alpha"]="&#8212; ".strtoupper(substr($dataitem['Company'],0,1))." &#8212;";
        
        // Create Readable Locations 
        $location = $dataitem['City'];
        if ($dataitem['State']) {
            $state_name =  isset($this->regionset->regions['US AND CANADA'][$dataitem['State']])?
                    $this->regionset->regions['US AND CANADA'][$dataitem['State']]:
                    $dataitem['State'];
            $location.= ($dataitem['City']?', ':'') .$state_name;
            $dataitem['State']=$state_name;
        }
        if ($dataitem['Country']!="USA") {
            $country_name = isset ($this->regionset->regions['WORLD'][$dataitem['Country']])?
                    $this->regionset->regions['WORLD'][$dataitem['Country']]:
                    $dataitem['Country'];
            $location .= '&nbsp;' . $country_name;
            $dataitem['Country'] = $country_name;
        }
        if ( isset( $dataitem['Intl'] ) && $dataitem['Intl']) $dataitem['Intl'] = $dataitem['Country'];
        $dataitem["Location"] = $location;


        return $this->subheader_print($dataitem, $options, $level);
    }


    function subheader_print($dataitem, $options, $level) {
        if ($level<1) return false;
        if ($level==1) $textlevel='';
        else $textlevel=strval($level);
        $header_option = 'subheader' . ( $level>1 ? strval( $level ) : '' );
        $header_field = $options[ $header_option ];
        //$header_field = $options[( 'subheader'.$textlevel)];
        $output = "";
        if (!isset( $dataitem[$header_field])) {
            return false;
        }

        //set which header we are currently checking
        $current_sub = "current_subheader" . $textlevel;

        //show normal headers
        if ($this->$current_sub != trim($dataitem[$header_field])) {
            $this->$current_sub = trim($dataitem[$header_field]);
            $output = '<span class="list_subheader'.$textlevel.'">' . $this->$current_sub.'</span><BR>';
        }
        # return $output;
        $level = $level - 1;
        return $this->subheader_print($dataitem, $options, $level).$output;
    }

    function display_detail($dbcon, $calid) {
        print 'Warning: this detail page has not been specified.  Please contact your site administrator!';
    }

    function display_item($dataitem, $options) {
        print 'Warning: this display type has not been specified.  Please contact your site administrator!';
    }
        
}    


//end of UserData list class
//utility output functions follow
if (!function_exists('groups_layout_display')) {
	function groups_layout_display($data, $options) {
        
        $id=$data['id'];
        $Organization=$data['Company'];
        $City=$data['City'];
        $State=$data['State'];
        $Country=$data['Country'];
        $First_Name=$data['First_Name'];
        $Last_Name=$data['Last_Name'];
        $Email=$data['Email'];
        $Phone=$data['Phone'];
        $Web_Page=$data['Web_Page'];
        $About=$data['custom1'];
        $Details=$data['custom18'];
        $image=$data['custom19'];
        $start="";
        $end="";
        $html="";
		
		if ($image) {
			$start = "<table width= \"100%\"><tr><td width = 100><img src =\"img/thumb/$image\"></td><td valign=\"top\">";
			$end = "</td></tr></table>\n";
		}
		$html .= $start;

		$html .= "<span class =\"eventtitle\"> \n";
        $endlink='';
		if ($Web_Page && ($Web_Page != 'http://')) {
			 $html .= '<a href="'.$Web_Page.'" target="_blank" class ="eventtitle" >';
			 $endlink = "</a>";
		}
		//else if ($Details) {
			 #$html .= '<a href="groups.php?gid='.$id.'" class ="eventtitle" >';
			 #$endlink = "</a>";
		//}
		$html .= $Organization.$endlink."</span><br>";
		
		if ($City && $State) {
			$html .= "<span class=\"eventsubtitle\">$City, ";
			if ( strtoupper( $State ) =='INTL' ) { 
				$html .= $Country;
			} else {
				$html .= list_state_convert($State);
			}		
			$html .="</span><br>\n";
		}
	
		if ( ($First_Name) & ($Last_Name) ) {
			$html .= "<span class=\"bodygrey\">". $First_Name . "&nbsp;" . $Last_Name. "</span><br>\n";
		}
		if ($Email) {
			$html .= "<span class=\"bodygrey\">". AMP_protect_email( $Email ) ."</span><br />\n";
		}
		if ($Phone) {
			$html .= "<span class=\"bodygrey\">". $Phone . "</span><br>\n";
		}
		if ($About) {
			$html .= "<span class=\"bodygrey\">". converttext($About) . "</span><br>\n";
		}
        $html.="<p>\n";
		# $html .= events_groups($id);
		$html .= $end;

		return $html;
	}
}

if (!function_exists('list_state_convert')) {

function list_state_convert($in) {
	global $dbcon;
	if ( is_numeric($in) ) {

		$S=$dbcon->CacheExecute("SELECT state from states where id = $in ") or DIE($dbcon->ErrorMsg());
		$out = $S->Fields("state");
		return $out;
		
	} else {
		return $in;
	}
}

}
if (!function_exists('groups_detail_display')) {

	function groups_detail_display( $data, $options=array( )) {
        $id=$data['id'];
        $Organization=$data['Company'];
        $City=$data['City'];
        $State=$data['State'];
        $Country=$data['Country'];
        $First_Name=$data['First_Name'];
        $Last_Name=$data['Last_Name'];
        $Email=$data['Email'];
        $Phone=$data['Phone'];
        $Web_Page=$data['Web_Page'];
        $About=$data['custom1'];
        $Details=$data['custom18'];
        $image=$data['custom19'];
        $html="";
	
		$html .= '<p class ="title">'.$Organization.'</p>';
		if ($Web_Page && ($Web_Page != 'http://')) {
			 $html .= '<a href="'.$Web_Page.'" target="_blank" class ="bodygrey" >'.$Web_Page.'</a><br>';
		}
		
		if ($City && $State) {
			$html .= "<span class=\"eventsubtitle\">$City, ";
			if ($State =='Intl') { 
				$html .= $Country;
			} else {
				$html .= list_state_convert($State);
			}		
			$html .="</span><br>\n";
		}
	
		if ( ($First_Name) & ($Last_Name) ) {
			$html .= "<span class=\"bodygrey\">". $First_Name . "&nbsp;" . $Last_Name. "</span><br>\n";
		}
		if ($Email) {
			$html .= "<span class=\"bodygrey\">".AMP_protect_email( $Email ) ." </span><br />\n";
		}
		if ($Phone) {
			$html .= "<span class=\"bodygrey\">". $Phone . "</span><br><br><br>\n";
		}
		if ($image) {
			$html .= "<img src =\"img/pic/$image\" align = left>\n";
		}
		if ($About) {
			$html .= "<span class=\"bodygrey\">". converttext($About) . "</span><br><br>\n";
		}
		if ($Details) {
			$html .= "<span class=\"text\">". converttext($Details) . "</span><br>\n";
		}
		$html .= "<br>\n";

		return $html;
	}
}

function list_display_default( $data, &$udm ) {
    return $data['City'] . ',' . $data['State'] . '<br />';
}

function detail_display_default( $data, &$udm ) {
    return $data['City'] . ',' . $data['State'] . '<br />';
}

function housing_display_list( $data, &$udm ) {
    if( $data['custom1'] == 'Have Housing') {
        return housing_display_offer_list( $data, $udm );
    } else {
        return housing_display_request_list( $data, $udm );
    }
}

function housing_display_offer_list( $data, &$udm ) {
    $renderer = AMP_get_renderer( );
    $output = '';
    $output .= $renderer->span( 
                'Contact:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['First_Name']. ' '. $data['Last_Name'], array( 'class' => 'board_data')) 
                . $renderer->newline( );
    if( isset( $data['Company']) && $data['Company']) {
        $output .= $renderer->span( 
                'Org:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['Company'], array( 'class' => 'board_data')) 
                . $renderer->newline( );
    }
    if( isset( $data['Phone']) && $data['Phone']) {
        $output .= $renderer->span( 
                'Phone:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['Phone'], array( 'class' => 'board_data')) 
                . $renderer->newline( );
    }
    if( isset( $data['Email']) && $data['Email']) {
        $output .= $renderer->span( 
                'Email:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( AMP_protect_email( $data['Email'] ), array( 'class' => 'board_data')) 
                . $renderer->newline( );
    }
    if( isset( $data['custom3']) && $data['custom3']) {
        $output .= $renderer->span( 
                'Available:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom3'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom8'])&& $data['custom8']) {
        $output .= $renderer->span( 
                'Location:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom8'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }

    $summary_output = $renderer->div( $output, array( 'class' => 'board_summary'));
    $output = '';
    if( isset( $data['custom9']) && $data['custom9']) {
        $output .= $renderer->span( 
                'Bus/Metro:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom9'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom10']) && $data['custom10']) {
        $output .= $renderer->span( 
                'Parking:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom10'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom11']) && $data['custom11']) {
        $output .= $renderer->span( 
                'Meals:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom11'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom7']) && $data['custom7']) {
        $output .= $renderer->span( 
                'Accessibility:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom7'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom4']) && $data['custom4']) {
        $output .= $renderer->span( 
                'Beds:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom4'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom5']) && $data['custom5']) {
        $output .= $renderer->span( 
                'Floor:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom5'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom6']) && $data['custom6']) {
        $output .= $renderer->span( 
                'Tent:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom6'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom14']) && $data['custom14']) {
        $output .= $renderer->span( 
                'Smoking:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom14'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom13']) && $data['custom13']) {
        $output .= $renderer->span( 
                'Children:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom13'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom18']) && $data['custom18']) {
        $output .= $renderer->span( 
                'Other Comments:', array( 'class' => 'board_label')) . $renderer->newline( ) 
                . $renderer->span( $data['custom18'], array( 'class' => 'board_data')) 
                . $renderer->newline( 2 );

    }
    $detail_output = $renderer->div( $output, array( 'class' => 'board_details'));
    return $summary_output . $detail_output;

}

function housing_display_request_list( $data, &$udm ) {
    $renderer = AMP_get_renderer( );
    $output = '';
    $output .= $renderer->span( 
                'Contact:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['First_Name']. ' '. $data['Last_Name'], array( 'class' => 'board_data')) 
                . $renderer->newline( );
    if( isset( $data['Company']) && $data['Company']) {
        $output .= $renderer->span( 
                'Org:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['Company'], array( 'class' => 'board_data')) 
                . $renderer->newline( );
    }
    if( isset( $data['Phone']) && $data['Phone']) {
        $output .= $renderer->span( 
                'Phone:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['Phone'], array( 'class' => 'board_data')) 
                . $renderer->newline( );
    }
    if( isset( $data['Email']) && $data['Email']) {
        $output .= $renderer->span( 
                'Email:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( AMP_protect_email( $data['Email'] ), array( 'class' => 'board_data')) 
                . $renderer->newline( );
    }
    if( isset( $data['custom16']) && $data['custom16']) {
        $output .= $renderer->span( 
                'Dates Needed:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom16'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }
    if( isset( $data['custom17']) && $data['custom17']) {
        $output .= $renderer->span( 
                'Number of People:', array( 'class' => 'board_label')) . $renderer->space( ) 
                . $renderer->span( $data['custom17'], array( 'class' => 'board_data')) 
                . $renderer->newline( );

    }

    $summary_output = $renderer->div( $output, array( 'class' => 'board_summary'));
    $output = '';
    if( isset( $data['custom18']) && $data['custom18']) {
        $output .= $renderer->span( 
                'Other Comments:', array( 'class' => 'board_label')) . $renderer->newline( ) 
                . $renderer->span( $data['custom18'], array( 'class' => 'board_data')) 
                . $renderer->newline( 2 );

    }
    $detail_output = $renderer->div( $output, array( 'class' => 'board_details'));
    return $summary_output . $detail_output;

}
?>
