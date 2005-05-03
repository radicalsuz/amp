<?php
require_once ('AMP/UserData/Plugin.inc.php');
require_once ('AMP/UserData/Input.inc.php');
require_once ('AMP/Region.inc.php');
if (file_exists_incpath('custom.layouts.inc.php')) {
    include_once ('custom.layouts.inc.php');
}

class UserDataPlugin_DisplayHTML_Output extends UserDataPlugin {
    
    var $options= array( 
        'subheader'=>array('available'=>true, 
                            'description'=>'Show subheadings for',
                            'default'=>null,
                            'type'=>'text'),
        'display_format'=>array('name'=>'List Display Function Name',
                                'default'=>'groups_layout_display',
                                'available'=>true,
                                'type'=>'text'),
        'detail_format'=>array('name'=>'Detail Display Function Name',
                               'default'=>'groups_detail_display',
                               'available'=>true,
                               'type'=>'text'),
        'header_text'=>array('name'=>'Intro Text',
                               'default'=>'1',
                               'available'=>true,
                               'type'=>'select'),
        '_userid' => array ('default'=>null,
                            'available'=>false)
        );
    
    var $current_subheader;
    var $regionset;

    function UserDataPlugin_DisplayHTML_Output (&$udm, $options=null, $instance=null) {   
        $this->init($udm, $options, $instance);
    }

    function init (&$udm, $options=null, $instance=null) {
        $this->dbcon=&$udm->dbcon;
        $this->udm= &$udm;
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
            $this->options['header_text']['values']=$modules;
        }
    }

    function execute ($options=null) {
        $options=array_merge($this->getOptions(), $options);
        //Check to see if a single record was specified
        //if so, return detail information for that record 
		if (isset($options['_userid'])) {

            $detail_function=isset($options['detail_format'])?($options['detail_format']):"display_detail";
            $inclass=method_exists($detail_function, $this);

            $single_udm=&new UserDataInput ($this->dbcon, $this->udm->instance, $this->udm->admin);
            $single_udm->getUser($options['_userid']);

            $dataset=$single_udm->getData();

            if ($inclass){ $output=$this->$detail_function($dataset, $options);
            } else {
                $output=$detail_function($dataset, $options);
            }
        } else {
        //Print the current results list
            $dataset=$this->udm->getData();
            
            $display_function=isset($options['display_format'])?($options['display_format']):"display_item";
            $inclass=method_exists($display_function, $this);

            //output display format
            foreach ($dataset as $dataitem) {
                if (isset($options['subheader'])) $output.=$this->subheader($dataitem, $options['subheader']);
                if($inclass) $output.=$this->$display_function($dataitem);
                else $output.=$display_function($dataitem, $this->options);
            }
        }

		return $output;
    }
        
    function subheader($dataitem, $header_field) {
        if ($this->current_subheader != trim($dataitem[$header_field])) {
            $this->current_subheader = trim($dataitem[$header_field]);
            $output .= '<h1 style="font-size: small; background: #ccc; padding: 3px 3px;">' . $this->current_subheader;
            if ($header_field=='City') {
                if ($dataitem['State']) $output.= ($dataitem['City']?', ':'') . $this->regionset->regions['US AND CANADA'][$dataitem['State']];
                if ($dataitem['Country']!="USA") $output.= '&nbsp;' . $this->regionset->regions['WORLD'][$dataitem['Country']];
            }
            $output.= '</h1>';
        }
        return $output;
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

?>
