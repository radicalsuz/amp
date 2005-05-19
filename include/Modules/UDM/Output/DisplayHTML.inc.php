<?php
require_once ('AMP/UserData/Plugin.inc.php');
require_once ('AMP/UserData/Input.inc.php');
require_once ('AMP/Region.inc.php');
if (file_exists_incpath('custom.layouts.inc.php')) {
    include_once ('custom.layouts.inc.php');
}

class UserDataPlugin_DisplayHTML_Output extends UserDataPlugin {
    
    var $options= array( 
        'subheader'=>array('label'=>'Subheading',
                            'available'=>true, 
                            'description'=>'Show subheadings for',
                            'default'=>null,
                            'type'=>'text'),
        'subheader2'=>array('label'=>'Subheading 2',
                            'available'=>true, 
                            'description'=>'Show subheadings for',
                            'default'=>null,
                            'type'=>'text'),
        'subheader3'=>array('label'=>'Subheading 3',
                            'available'=>true, 
                            'description'=>'Show subheadings for',
                            'default'=>null,
                            'type'=>'text'),
        'display_format'=>array('label'=>'List Display Function Name',
                                'default'=>'groups_layout_display',
                                'available'=>true,
                                'type'=>'text'),
        'detail_format'=>array('label'=>'Detail Display Function Name',
                               'default'=>'groups_detail_display',
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
        '_userid' => array ('default'=>null,
                            'available'=>false)
        );
    
    var $available=true;

    //multiple subheaders - yay!
    var $current_subheader;
    var $current_subheader2;
    var $current_subheader3;
    var $regionset;

    function UserDataPlugin_DisplayHTML_Output (&$udm, $instance=null) {   
        $this->init($udm, $instance);
        $this->regionset=new Region;
    }

    function _register_options_dynamic () {
        if ($this->udm->admin) {
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
            $this->options['header_text_list']['values']=$modules;
            $this->options['header_text_display']['values']=$modules;
        }
    }

    function execute ($options=null) {
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
        
        $inclass=method_exists($display_function, $this);

        //output display format
        foreach ($dataset as $dataitem) {
            if ($subheader_level) $output.=$this->subheader($dataitem, $options, $subheader_level);
            if($inclass) $output.=$this->$display_function($dataitem);
            else $output.=$display_function($dataitem, $this->options);
        }
    

		return $output;
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
            if (strpos($option_name, "subheader")===0) {
                if (strlen($option_name)==9) $level=1;
                else $level = intval(substr($option_name,9));
            }
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
        $dataitem["Location"] = $location;


        return $this->subheader_print($dataitem, $options, $level);
    }


    function subheader_print($dataitem, $options, $level) {
        if ($level<1) return false;
        if ($level==1) $textlevel='';
        else $textlevel=strval($level);
        $header_field = $options['subheader'.$textlevel];
        $output = "";

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
			$html .= "<span class=\"bodygrey\"><a href=\"mailto:$Email\">$Email</a></span><br>\n";
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

	function groups_detail_display( $data, $options=null) {
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
			$html .= "<span class=\"bodygrey\"><a href=\"mailto:$Email\">$Email</a></span><br>\n";
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

?>
