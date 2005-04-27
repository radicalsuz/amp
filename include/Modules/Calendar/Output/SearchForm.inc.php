<?php
require_once('AMP/Region.inc.php');
require_once('HTML/QuickForm.php');
require_once('AMP/Geo/Geo.php');
require_once('Modules/Calendar/Plugin.inc.php');

class CalendarPlugin_SearchForm_Output extends CalendarPlugin {
	var $regionset;
	var $lookups;
	var $form;
	var $fields_def;
	var $control_class;
	#var $url_criteria;
    #var $sql_criteria;
    var $options = array (
        'show_caltype'=>array(
            'description'=>'Allow Search by Event Type',
            'type'=>'checkbox',
            'value'=>true,
            'default'=>true),
        'show_student'=>array(
            'description'=>'Allow Search For Student Events',
            'type'=>'checkbox',
            'value'=>true,
            'default'=>false),
        'show_recurring_options'=>array(
            'description'=>'Allow Search by Repeating Events',
            'type'=>'checkbox',
            'value'=>true,
            'default'=>false),
        'show_distance'=>array(
            'description'=>'Allow Search by Zip/Distance',
            'type'=>'checkbox',
            'value'=>true,
            'default'=>true), 
        'form_name'=>array(
            'description'=>'Name of Search Form',
            'available'=>false,
            'value'=>'calendar_search'),
        'field_order'=>array(
            'description'=>'Order of Fields, User Side',
            'available'=>true,
            'value'=>'newline,start_text,caltype,lcountry,state,endline,newline,bydate,distance,zip,student,search,sortby,endline'),
        'field_order_admin'=>array(
            'description'=>'Order of Fields, Admin View',
            'available'=>true,
            'value'=>'newline,start_text,caltype,lcountry,state, city, endline,newline,bydate,distance,zip,endline,newline,publish,recurring_options,student,old,search,sortby,endline')
            );
        
                    

	function CalendarPlugin_SearchForm_Output (&$calendar, $plugin_instance=null) {
        $this->init ($calendar, $plugin_instance);
		
		//define lookup arrays (region, date, state, country, etc)
		$this->define_lookups();

        //define the search form
		$this->fields_def=$this->define_form();

        //check the REQUEST array
        $this->calendar->sql_criteria=$this->read_request();
	}	
	
	function read_request() {
		global $_REQUEST;
		$this->setupRegion();


		
		// CHECK FOR SEARCH CRITERIA
		//ByDate
		//searches for future events only if no start date is specified.
		if (isset($_REQUEST['bydate'])&&($_REQUEST['bydate'])) {
			$sql_criteria[]='((`date` >= '.$this->dbcon->qstr($_REQUEST['bydate']).' AND `recurring_options`=0) OR (`enddate`>='.$this->dbcon->qstr($_REQUEST['bydate']).' AND `recurring_options`>0))';
		} else {
            if ($_REQUEST['old']!=1)  
                $sql_criteria[]='((`date` >= CURDATE() AND `recurring_options`=0) || (`recurring_options`>0 AND `enddate`>= CURDATE() ))';
		
		}


		//Zip Code Search Request
		if (isset($_REQUEST['zip'])&&isset($_REQUEST['distance'])&&$_REQUEST['zip']&&$_REQUEST['distance']) {
			$srch_options['zip']=$_REQUEST['zip'];
			$srch_options['distance']=$_REQUEST['distance'];
            $srch_loc=new Geo ($this->dbcon, NULL, NULL, NULL, $_REQUEST['zip']);
            if ($ziplist=$srch_loc->zip_radius($_REQUEST['distance'])) {
                $zipset = "(".$_REQUEST['zip'];
                foreach ($ziplist as $zindex=>$zinfo) {
                    if (strlen($zindex)==4) $zindex='0'.$zindex;
                    $zipset.=",".$this->dbcon->qstr($zindex);
                }
                $zipset.=")";
                $sql_criteria[]="lzip IN $zipset";
			} else {
                $this->calendar->error="Sorry, US zip codes only";
            }
		} 
		//State Request from event index page
		if (isset($_REQUEST['state'])&&($_REQUEST['state'])) {
			$sql_criteria[]="lstate=".$this->dbcon->qstr($_REQUEST['state']);
			$this->lookups['lcity']['LookupWhere'] = " lstate=".$this->dbcon->qstr($_REQUEST['state']);
			$this->setupLookup('lcity');
		    $this->fields_def['city']=array('type'=>'select', 'label'=>'Select City', 'values'=>$this->lookups['lcity']['Set'], 'value'=>$_REQUEST['city']);

		} 

		//city Request from event index page
		if (isset($_REQUEST['city'])&&($_REQUEST['city'])) {
			$sql_criteria[]="lcity=".$this->dbcon->qstr($_REQUEST['city']);

		}

		//Area Request from pulldown
		if (isset($_REQUEST['area'])&&($_REQUEST['area'])) {
			$this->setupLookup('area');
			
			if($state_name=$this->lookups['area']['Set'][$_REQUEST['area']]) {
				$state_code=array_search($state_name, $this->lookups['lstate']['Set']);
				if ($state_code) {
					$sql_criteria[]="lstate=".$this->dbcon->qstr($state_code);
				}
			}
		}

		//Event Type
		if (isset($_REQUEST['caltype'])&&$_REQUEST['caltype']) {
			$this->setupLookup('caltype');
			$sql_criteria[]="typeid=".$this->dbcon->qstr($_REQUEST['caltype']);
		}
		
		//Country
		if (isset($_REQUEST['lcountry'])&&$_REQUEST['lcountry']) {
			//check to see if the search is by code
			if (strlen($_REQUEST['lcountry'])==3&&($country_name=$this->lookups['lcountry']['Set'][ $_REQUEST['lcountry']])) {
				$sql_criteria[]="lcountry=".$this->dbcon->qstr($_REQUEST['lcountry']);
			} else {
				if ($country_code=array_search($_REQUEST['lcountry'], $this->regionset->region['WORLD'])) {
					$sql_criteria[]="lcountry=".$this->dbcon->qstr($country_code);
				}
			}
		}

		//Modin
		if (isset($_REQUEST['modin'])&&$_REQUEST['modin']) {
			$this->setupLookup('modin');
			$sql_criteria[]="modin=".$this->dbcon->qstr($_REQUEST['modin']);
		}
		
		//Student events
		if (isset($_REQUEST['student'])&&$_REQUEST['student']) {
			$sql_criteria[]="student=1";
		}
		//Old events (legacy compatibility)
		if (isset($_REQUEST['old'])&&$_REQUEST['old']) {
			$sql_criteria[]='((`date` < CURDATE() AND `recurring_options`=0) || (`recurring_options`>0 AND `enddate`< CURDATE() ))';
		}

		//Uid or Creator_id
		if ((isset($_REQUEST['uid'])&&$_REQUEST['uid'])) {
			$sql_criteria[]="uid=".$this->dbcon->qstr($_REQUEST['uid']);
		}
        //Publish status
        if (is_numeric($_REQUEST['publish'])){
            if ($_REQUEST['publish']) $sql_criteria[]="publish=1";
            else $sql_criteria[]="publish!=1";
        }
		//Repeating Event
		if (isset($_REQUEST['recurring_options'])&&$_REQUEST['recurring_options']==1) {
			$sql_criteria[]="recurring_options>0";
		} elseif (isset($_REQUEST['recurring_options'])&&$_REQUEST['recurring_options']==='0') {
			$sql_criteria[]="(recurring_options=0 or isnull(recurring_options))";
        }

		//Grab valid URL data
        $this->calendar->url_criteria=array();
		foreach ($this->fields_def as $field=>$fdef) {
			if (isset($_REQUEST[$field]) && ($_REQUEST[$field]||$_REQUEST[$field]==='0')) {
				$this->calendar->url_criteria[]=$field.'='.$_REQUEST[$field];
			}
		}

		return $sql_criteria;
	}

	//Create QuickForm definitions for search form items


	function define_form (){
		
		$this->setupLookup('caltype');
		$this->setupRegion();
		

		$def['field_order']=$this->options['field_order']['value'];
		
		
		if ($this->calendar->admin) {
			$this->control_class='list_controls'; 
			$def['field_order']=$this->options['field_order_admin']['value'];
		} else {
			$this->control_class='go'; 
		}
			


		//event types
		$def['caltype'] = array('type'=>'select', 'label'=>'By Event Type', 'required'=>false,  'values'=>$this->lookups['caltype']['Set'], 'size'=>null, 'value'=>$_REQUEST['caltype'], 'public'=>'1');


		//country listing
		$def['lcountry'] =array('type'=>'select', 'label'=>'By Country', 'required'=>false,  'values'=>$this->lookups['lcountry']['Set'], 'size'=>null, 'value'=>$_REQUEST['lcountry'], 'public'=>'1');

		//state listing
		//accepts area values
		if ($_REQUEST['area']) {
				//this is coming from the left nav pulldown, must convert the ID to a two digit code
				$state_code=$this->lookups['area']['Set'][$_REQUEST['area']];	
		}
		if ($_REQUEST['state']) $state_code = $_REQUEST['state'];
		
		$def['state']=array('type'=>'select', 'label'=>'By State/Province', 'required'=>false,  'values'=>$this->lookups['lstate']['Set'], 'size'=>null, 'value'=>$state_code, 'public'=>'1');

		$def['endline']=array('type'=>'static', 'label'=>'</td></tr>', 'public'=>'1');
		$def['newline']=array('type'=>'static', 'label'=>'<tr><td class="'.$this->control_class.'">', 'public'=>'1');
		$def['start_text']=array('type'=>'static', 'label'=>'Search the Calendar<BR>', 'public'=>'1');

		//date
		$mydate=($_REQUEST['bydate']&& isset($_REQUEST['bydate']))?
			$_REQUEST['bydate']:
			date("Y-m-d");
			
		$def['bydate']=array('type'=>'select', 'label'=>'On or After:', 'required'=>false,  'values'=>$this->lookups['bydate']['Set'], 'size'=>null, 'value'=>$mydate, 'public'=>'1');
	
		//distance by zip
		$distance_options=array('1'=>'1','5'=>'5', '10'=>'10', '25'=>'25', '100'=>'100', '250'=>'250');
		$def['distance']=array('type'=>'select', 'label'=>'Within:', 'required'=>false,  'values'=>$distance_options, 
                            'size'=>null, 'value'=>(is_numeric($_REQUEST['distance'])?$_REQUEST['distance']:'5'), 'public'=>'1');
		$def['zip']=array('type'=>'text', 'label'=>'&nbsp;miles of US zipcode:&nbsp', 'value'=>$_REQUEST['zip'], 'size'=>'8', 'public'=>'1');
		
		//student checkbox
		$def['student']=array('type'=>'checkbox', 'label'=>'Student Events Only', 'value'=>1, 'public'=>'1','enabled'=>'1');
		
		$def['search']=array('type'=>'submit', 'label'=>'Search', 'public'=>'1');

				
		$publish_options=array(''=>'Any', '0'=>'draft', '1'=>'live');
		$def['publish']=array('type'=>'select', 'label'=>'Status', 'value'=>$_REQUEST['publish'], 'values'=>$publish_options);
        $def['recurring_options']=array('type'=>'select', 'label'=>'Repeating', 'value'=>$_REQUEST['recurring_options'], 'values'=>array(''=>'Any','0'=>'No','1'=>'Yes'), 'public'=>'0', 'enabled'=>'1');
		#city is defined by state read_request routine
        #$def['city']=array('type'=>'select', 'label'=>'Select City', 'values'=>$this->lookups['lcity'], 'value'=>$_REQUEST['city']);
        $def['sortby']=array('type'=>($_REQUEST['sortby']?'select':'hidden'), 'label'=>($_REQUEST['sortby']?'Sort:':''), 'value'=>$_REQUEST['sortby'], 'public'=>1, 'enabled'=>1, 'values'=>array(''=>'Default',$_REQUEST['sortby']=>$_REQUEST['sortby']));
        $def['old']=array('type'=>($_REQUEST['old']?'checkbox':'hidden'), 'label'=>($_REQUEST['old']?'Past Events':''), 'value'=>'1', 'public'=>1, 'enabled'=>'1' );

		return $def;

	}

	
	/**
	 * returns html for the event search form
	 */
	function execute($options=null) {
        $options= array_merge($this->getOptions(), $options);
		
		
		$frmName    = $options['form_name']; 
		$frmMethod  = 'get';
		$frmAction  =   $_SERVER['PHP_SELF'] ;

	    $form = &new HTML_QuickForm( $frmName, $frmMethod, $frmAction );

        //remove the zip field if distance search is disabled
        if (!$options['show_distance']) unset ($this->fields_def['zip']);

		if ( isset( $this->fields_def[ 'field_order' ] ) ) {
		
			$fieldOrder = split( ',', $this->fields_def[ 'field_order']  );
			
			foreach ( $fieldOrder as $field ) {
				$field = trim( $field );
                if (isset($this->fields_def[$field])&&
                    (isset($options['show_'.$field])?$options['show_'.$field]:true)) {
                    $this->calendar_quickform_addElement( $form, $field, $this->fields_def[ $field ], $this->calendar->admin );
                }
			}

		} else {
            foreach ($this->fields_def as $fname=>$fdef) {
                if (isset($options['show_'.$field])?$options['show_'.$field]:true)
                    $this->calendar_quickform_addElement( $form, $fname, $fdef, $this->calendar->admin );
            }
        }
                
		$this->form = &$form;
		
		return  $form->toHtml();
    
	}
		
		
		
		
	function calendar_quickform_addElement( &$form, $name, &$field_def, $admin = false ) {

		if ( $field_def[ 'public' ] != 1 && !$admin ) return false;

		$type     = $field_def[ 'type'   ];
		$label    = $field_def[ 'label'  ];
		$defaults = $field_def[ 'values' ];
		$size     = $field_def[ 'size' ];
		$renderer =& $form->defaultRenderer();

		// Check to see if we have an array of values.
		if (!is_array($defaults)) {
			$defArray = explode( ",", $defaults );
			if (count( $defArray ) > 1) {
				$defaults = array();
				foreach ( $defArray as $option ) {
					$defaults[ $option ] = $option;
				}
			} else {
				$defaults = $defArray[0];
			}
		}			
	
	    
		// Add a default blank value to the select array.
		if ( $type  == 'select' && is_array( $defaults ) ) {
			//Move label into select box for non colonned entries.
			if (substr($label, strlen($label)-1)!=":") {
				$defaults = array('' => $label) + $defaults;
				$label="";
			} 
			if ($field_def['value']&&isset($field_def['value'])) $selected=$field_def['value'];
		}
		
		//add the element
		$form->addElement( $type, $name, $label, $defaults );

		//get the element reference
		$fRef =& $form->getElement( $name );

		$fRef->updateAttributes(array('class'=>$this->control_class, 'size'=>$size));
		if ( isset( $selected ) ) {
			$fRef->setSelected( $selected );
		}


		if ($type=='static') {
			  $renderer->setElementTemplate(" {label}", $name);
		} elseif ($type=='checkbox') {
			$renderer->setElementTemplate("{element}  {label} ", $name);
		} else {


			  $renderer->setElementTemplate("\n\t\t<span align=\"right\" valign=\"top\" class=\"".$this->control_class."\">{label} {element}\n\t", $name);
		}
		
		
		return 1;
	}
		
		





	//Lookups are used in figuring out what the criteria are
	//and what values to display to the user
	
	//first we define them
	//definitions can consist of a LookupTable and LookupField
	//which is later used by setupLookup to get the info
	//or the Set element can be created directly
	function define_lookups() {
		//Country and State are being set using setupRegion below
		//this pulls from the Region.inc.php code used by UDMs
		$this->lookups['lcountry']=array('name'=>'Country');
		$this->lookups['lstate']=array('LookupName'=>'State');
		$this->lookups['lcity']=array('name'=>'City', 'LookupTable' => 'calendar', 'LookupField' => 'lcity', 'LookupDistinctField' => 1, 'LookupSearchby' => 'lcity', 'LookupSortby' => 'lcity' );
		//Region is for backwards compatibility with older Region calendars
		$this->lookups['area']=array('name'=>'Region', 'LookupField'=>'title', 'LookupTable'=>'regions');
	
		//Modin is for searching events by campaign - not yet implemented but will be handy
		#$this->lookups['modin']=array('name'=>'Activity/Campaign', 'LookupField'=>'name', 'LookupTable'=>'userdata_fields');
		//Calendar Types lookup
		$this->lookups['caltype']=array('name'=>'Event Type', 'LookupField'=>'name', 'LookupTable'=>'eventtype');
		//Date lookup 
		$this->lookups['bydate']=array('name'=>'Date');
		//setup the date array : 
		//next five weeks, last 2 weeks
		for($n=-2; $n<=5; $n++) {
			$nextweek=mktime(0,0,0, date("m"), (date("d")+($n*7)), date("Y"));
			$this->lookups['bydate']['Set'][date('Y-m-d', $nextweek)]=date("M d, Y", $nextweek);
		}
			
		//1 year from the current date to 5 years ago
		for($n=date('Y')-5; $n<=date('Y')+1; $n++) {
			for ($m=1; $m<13; $m++) {
				$this->lookups['bydate']['Set'][($n."-".sprintf("%02d",$m)."-01")]=date("Y - F", mktime(0,0,0, $m, 1, $n));
			}
		}
	}



    //retrieves Lookup values from database tables and stores them in the
    //Lookups value 
	function setupLookup($which) {
		//if the Lookup is defined
		if (is_array($this->lookups[$which])) {
			$this_lookup=$this->lookups[$which];
			//if there is a LookupTable defined (assumes a LookupField is defined as well)
			if (isset($this_lookup['LookupTable'])&&!isset($this_lookup['Set'])) {
				if (isset($this_lookup['LookupSearchby'])) {
					$id_field = $this_lookup['LookupSearchby']. " AS id";
				} else {
					$id_field="id";
				}

				if ($this_lookup['LookupDistinctField']) {
					$lookup_sql = "SELECT DISTINCT " . $this_lookup['LookupField'] . ", $id_field";
				} else {
					$lookup_sql = "SELECT DISTINCT $id_field, " . $this_lookup['LookupField'];
				}

				$lookup_sql .= " FROM ".$this_lookup['LookupTable'];

				if (isset($this_lookup['LookupWhere'])) $lookup_sql.=" WHERE ".$this_lookup['LookupWhere'];
				if (isset($this_lookup['LookupSortby'])) { $lookup_sql.=" ORDER BY ".$this_lookup['LookupSortby']; }
				//get the set from the DB
                #print $which.": ".$l/okup_sql."<BR>";
				$this->lookups[$which]['Set']=$this->dbcon->CacheGetAssoc( $lookup_sql );
				
			}
		}
	}


	//get Region Values for state and country lookups
	function setupRegion () {
		if (!isset($this->regionset->regions['WORLD'])) {$this->regionset=new Region();}
		$this->lookups['lcountry']['Set']=&$this->regionset->regions['WORLD'];
		$this->lookups['lstate']['Set']=&$this->regionset->regions['US AND CANADA'];
	}


    //Generates a header based on the current search
    function search_text_header () {
		global $_REQUEST;
		$this->setupRegion();
		$search_type='events';
		foreach ($_REQUEST as $searchitem=>$searchdata) {
			//add criteria to the holding set
			if ($searchdata) {
				switch ($searchitem) {
					//adding text for various criteria
					case 'zip':
						$search_text[]="within ".$_REQUEST['distance']." miles of ".$searchdata;
						break;
					case 'state':
                        $city_insert=($_REQUEST['city']?$_REQUEST['city'].", ":"");
						$search_text[]="in ".$city_insert.$this->lookups['lstate']['Set'][$searchdata];
						break;
					case 'area':
					case 'modin':
						$search_text[]="in ".$this->lookups[$searchitem]['Set'][$searchdata];
						break;
					case 'lcountry':
						if (isset($this->lookups['lcountry']['Set'][$searchdata])) {
							$search_text[]="in ".$this->lookups['lcountry']['Set'][$searchdata];
						} elseif ($country_code=array_search($searchdata, $this->regionset->regions['WORLD'])) 		{ $search_text[]="in ".$searchdata;}
						break;
					case 'uid':
						$search_text[]="by contact";
						break;
					//Set the search type
					//options are 'events' - default, 'student events', 'upcoming events', 'events on or after #date#', '#Event Type#',  and the permutations of those;
					case 'student':
						if (strpos($search_type, "upcoming ")===FALSE) {
							$search_type='student '.$search_type;
						} else {
							$search_type=str_replace("upcoming ", "upcoming student ", $search_type);
						}
						break;
					case 'caltype':
						$search_type=$this->lookups[$searchitem]['Set'][$searchdata]."(s)";
					//bydate always runs after searchtype is set, so no break here
					//so date info is re-applied if the base text changes
					case 'bydate':
						if (isset($_REQUEST['bydate'])&&$_REQUEST['bydate']&&strpos($search_type,"on or after")==0&&strpos($search_type,"upcoming")===FALSE) {
							if ($_REQUEST['bydate']==strval(date('Y-m-d'))) {
								$search_type='upcoming '.$search_type;
							} else {
								$search_type.=" on or after ".$this->lookups['bydate']['Set'][$_REQUEST['bydate']];
							}
					
						}
						break;
						
                }
            }
        }
        if (isset($search_text)||$search_type!='events') { 
            $header_text="Listing ".$search_type."<BR>".((is_array($search_text))?join(' ', $search_text):$search_text);
        } else {//default header - showing all
            $header_text="Listing All Events";
        }
        $header_class=($this->calendar->admin?'header':'title');
        $header_text='<span class='.$header_class.'>'.$header_text.'</span><BR>';

        return $header_text;
    }

}
?>
