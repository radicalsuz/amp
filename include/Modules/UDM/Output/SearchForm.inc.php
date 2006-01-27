<?php
require_once('AMP/Region.inc.php');
require_once('HTML/QuickForm.php');
require_once('AMP/Geo/Geo.php');
require_once('AMP/UserData/Plugin.inc.php');
require_once( 'AMP/Form/SearchForm.inc.php' );

class UserDataPlugin_SearchForm_Output extends UserDataPlugin {
	var $regionset;
	var $lookups;
    var $available=true;
	var $form;
	var $fields_def;
	var $control_class;
    var $options = array (
        'show_distance'=>array(
            'description'=>'Allow Search by Zip/Distance',
            'type'=>'checkbox',
            'value'=>true,
            'default'=>true), 
        'form_name'=>array(
            'description'=>'Name of Search Form',
            'available'=>false,
            'default'=>'udm_search'),
        'field_order'=>array(
            'description'=>'Order of Fields, User Side',
            'available'=>true,
            'default'=>'newline,start_text,country,state,bydate,search,sortby,modin,endline'),
        'field_order_admin'=>array(
            'description'=>'Order of Fields, Admin View',
            'available'=>true,
            'default'=>'newline,start_text,country,state,city,endline,newline,bydate,publish,search,sortby,modin,endline'),
        'show_search_header'=>array(
            'description'=>'show description of current search',
            'type'=>'checkbox',
            'label'=>'Describe search on results page',
            'default'=>1),
        'search_form_display'=>array(
            'description'=>'show search form on result page',
            'type'=>'checkbox',
            'label'=>'show search form on result page',
            'default'=>1),
            );
        
                    

	function UserDataPlugin_SearchForm_Output (&$udm, $plugin_instance) {
        $this->init ($udm, $plugin_instance);
		
		//define lookup arrays (region, date, state, country, etc)
		$this->define_lookups();

        //define the search form
		$this->fields_def=$this->define_form();

        //check the REQUEST array
        if (method_exists($this->udm, 'setSQLCriteria')) {
            $this->udm->setSQLCriteria($this->read_request());
        }
	}	
	
	function read_request() {
		$this->setupRegion();


		
		// CHECK FOR SEARCH CRITERIA
		//ByDate
		//looks for records changed after the specified date
		if (isset($_REQUEST['bydate'])&&($_REQUEST['bydate'])) {
			$sql_criteria[]='`timestamp` >= '.$this->dbcon->qstr($_REQUEST['bydate']);
		}


		//Zip Code Search Request
		if (isset($_REQUEST['zip'])&&isset($_REQUEST['distance'])&&$_REQUEST['zip']&&$_REQUEST['distance']) {
			$srch_options['zip']=$_REQUEST['zip'];
			$srch_options['distance']=$_REQUEST['distance'];
            $srch_loc=&new Geo ($this->dbcon, NULL, NULL, NULL, $_REQUEST['zip']);
            if ($ziplist=$srch_loc->zip_radius($_REQUEST['distance'])) {
                $zipset = "(".$_REQUEST['zip'];
                foreach ($ziplist as $zindex=>$zinfo) {
                    if (strlen($zindex)==4) $zindex='0'.$zindex;
                    $zipset.=",".$this->dbcon->qstr($zindex);
                }
                $zipset.=")";
                $sql_criteria[]="zip IN $zipset";
			} else {
                $this->udm->errorMessage("Sorry, no match found for that zip code");
            }
		} 
		//State Request from index page
		if (isset($_REQUEST['state'])&&($_REQUEST['state'])) {
			$sql_criteria[]="State=".$this->dbcon->qstr($_REQUEST['state']);
			$this->lookups['city']['LookupWhere'] = " modin=".$this->udm->instance." AND State=".$this->dbcon->qstr($_REQUEST['state']);
			$this->setupLookup('city');
		    $this->fields_def['city']=array('type'=>'select', 'label'=>'Select City', 'values'=>$this->lookups['city']['Set'], 'value'=>$_REQUEST['city']);

		} 

		//city Request from index page
		if (isset($_REQUEST['city'])&&($_REQUEST['city'])) {
			$sql_criteria[]="city=".$this->dbcon->qstr($_REQUEST['city']);

		}

		//Area Request from pulldown
		if (isset($_REQUEST['area'])&&($_REQUEST['area'])) {
			$this->setupLookup('area');
			
			if($state_name=$this->lookups['area']['Set'][$_REQUEST['area']]) {
				$state_code=array_search($state_name, $this->lookups['state']['Set']);
				if ($state_code) {
					$sql_criteria[]="State=".$this->dbcon->qstr($state_code);
				}
			}
		}

		//Country
		if (isset($_REQUEST['country'])&&$_REQUEST['country']) {
			//check to see if the search is by code
			if (strlen($_REQUEST['country'])==3&&($country_name=$this->lookups['country']['Set'][ $_REQUEST['country']])) {
				$sql_criteria[]="Country=".$this->dbcon->qstr($_REQUEST['country']);
			} else {
				if ($country_code=array_search($_REQUEST['country'], $this->regionset->regions['WORLD'])) {
					$sql_criteria[]="Country=".$this->dbcon->qstr($country_code);
				}
			}
		}

		//Modin
		if (isset($_REQUEST['modin'])&&$_REQUEST['modin']) {
			$sql_criteria[]="modin=".$_REQUEST['modin'];
		}
		
		//Uid or Creator_id
		if ((isset($_REQUEST['uid'])&&$_REQUEST['uid'])) {
            if (is_array($_REQUEST['uid'])) {
                //allow for multiple ids
                $sql_criteria[] = "id in(" . join(",", $_REQUEST['uid']) . ")";
            } else {
                $sql_criteria[]="id=".$this->dbcon->qstr($_REQUEST['uid']);
            }
		}
        //Publish status
        if (is_numeric($_REQUEST['publish'])){
            if ($_REQUEST['publish']) $sql_criteria[]="publish=1";
            else $sql_criteria[]="(isnull(publish) OR publish!=1)";
        }

		//Vet valid URL data
        $vetted_set = array();
        $criteria_set = $this->udm->getURLCriteria();

		foreach ($this->fields_def as $field=>$fdef) {
			if (!isset($criteria_set[$field])) continue;
            if (!($criteria_set[$field]||($criteria_set[$field]==='0'))) continue;

            $vetted_set[ $field ] = $criteria_set[ $field ];
		}
        $this->udm->url_criteria = $vetted_set;

		return $sql_criteria;
	}

	//Create QuickForm definitions for search form items


	function define_form (){
		
		$this->setupRegion();
        $options=$this->getOptions();
		$def['field_order']=$options['field_order'];
		
		if ($this->udm->admin) {
			$this->control_class='list_controls'; 
			$def['field_order']=$options['field_order_admin'];
		} else {
			$this->control_class='go'; 
		}
			
		//country listing
		$def['country'] =array('type'=>'select', 'label'=>'By Country', 'required'=>false,  'values'=>$this->lookups['country']['Set'], 'size'=>null, 'value'=>$_REQUEST['country'], 'public'=>'1');

		//state listing
		//accepts area values
		if ($_REQUEST['area']) {
				//this is coming from the left nav pulldown, must convert the ID to a two digit code
				$state_code=$this->lookups['area']['Set'][$_REQUEST['area']];	
		}
		if ($_REQUEST['state']) $state_code = $_REQUEST['state'];
		
		$def['state']=array('type'=>'select', 'label'=>'By State/Province', 'required'=>false,  'values'=>$this->lookups['state']['Set'], 'size'=>null, 'value'=>$state_code, 'public'=>'1');

		$def['endline']=array('type'=>'static', 'label'=>'</td></tr>', 'public'=>'1');
		$def['newline']=array('type'=>'static', 'label'=>'<tr><td class="'.$this->control_class.'">', 'public'=>'1');
		$def['start_text']=array('type'=>'static', 'label'=>'Search '.$this->udm->name.'<BR>', 'public'=>'1');

		//date
		$mydate=($_REQUEST['bydate']&& isset($_REQUEST['bydate']))?
			$_REQUEST['bydate']:
		    "";	
			
		$def['bydate']=array('type'=>'select', 'label'=>'Entered Date', 'required'=>false,  'values'=>$this->lookups['bydate']['Set'], 'size'=>null, 'value'=>$mydate, 'public'=>'1');
	
		//distance by zip
		$distance_options=array('1'=>'1','5'=>'5', '10'=>'10', '25'=>'25', '100'=>'100', '250'=>'250');
		$def['distance']=array('type'=>'select', 'label'=>'Within:', 'required'=>false,  'values'=>$distance_options, 'size'=>null, 'value'=>(isset($_REQUEST['distance'])?$_REQUEST['distance']:'5'), 'public'=>'1');
		$def['zip']  =  array(
            'type'=>'text',     
            'label'=>'&nbsp;miles of US zipcode:&nbsp', 
            'value'=>$_REQUEST['zip'], 
            'size'=>'8', 
            'public'=>'1');
		
		$def['search']=array('type'=>'submit', 'label'=>'Search', 'public'=>'1');
		$def['modin']=array('type'=>'hidden', 'label'=>'', 'value'=>$this->udm->instance, 'size'=>'8', 'public'=>'1', 'enabled'=>'1');

        //Other values the list may want to preserve under some circumstances
        //Gdisplay is a display type for the groups page
		$def['gdisplay']=isset($_REQUEST['gdisplay'])?
            array('type'=>'hidden', 'label'=>'', 'value'=>$_REQUEST['gdisplay'], 'size'=>null, 'public'=>'1', 'enabled'=>'1'):
            null;
		

				
		$publish_options=array(''=>'Any', '0'=>'draft', '1'=>'live');
		$def['publish']=array('type'=>'select', 'label'=>'Status', 'value'=>$_REQUEST['publish'], 'values'=>$publish_options);
		#city is defined by state read_request routine
        #$def['city']=array('type'=>'select', 'label'=>'Select City', 'values'=>$this->lookups['city'], 'value'=>$_REQUEST['city']);
        $def['sortby']=array('type'=>($_REQUEST['sortby']?'select':'hidden'), 'label'=>($_REQUEST['sortby']?'Sort:':''), 'value'=>$_REQUEST['sortby'], 'public'=>1, 'enabled'=>1, 'values'=>array(''=>'Default',$_REQUEST['sortby']=>$_REQUEST['sortby']));

		return $def;

	}

	
	/**
	 * returns html for the search form
	 */
	function execute($options=null) {
        $options= array_merge($this->getOptions(), $options);
        if ( isset( $options['search_form_display']) && !$options['search_form_display']) return false;
		
		
		$frmName    = $options['form_name']; 
		$frmMethod  = 'GET';
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
                    $this->form_addElement( $form, $field, $this->fields_def[ $field ], $this->udm->admin );
                }
			}

		} else {
            foreach ($this->fields_def as $fname=>$fdef) {
                if (isset($options['show_'.$field])?$options['show_'.$field]:true)
                    $this->form_addElement( $form, $fname, $fdef, $this->udm->admin );
            }
        }
                
		$this->form = &$form;
		
        $output = $form->toHtml();
        if ($options['show_search_header']) $output = $this->search_text_header(). $output;

		return $output;
    
	}
		
		
		
		
	function form_addElement( &$form, $name, &$field_def, $admin = false ) {

        if (  !( isset( $field_def['public']) && $field_def[ 'public' ] ) && !$admin ) return false;

        $type     = isset( $field_def['type']) ? $field_def[ 'type'   ]:'';
        $label    = isset( $field_def['label']) ? $field_def[ 'label'  ] : '';
        $defaults = isset( $field_def['values']) ? $field_def[ 'values' ] : null; 
        $size     = isset( $field_def['size']) ? $field_def[ 'size' ]:null;
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
            if ( isset($field_def['value']) && $field_def['value'] ) $selected = $field_def['value'];
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
		$this->lookups['country']=array('name'=>'Country');
		$this->lookups['state']=array('LookupName'=>'State');
		$this->lookups['city']=array('name'=>'City', 'LookupTable' => 'userdata', 'LookupField' => 'city', 'LookupDistinctField' => 1, 'LookupSearchby' => 'city', 'LookupSortby' => 'city' );

		//Region is for backwards compatibility with older Region udms
		$this->lookups['area']=array('name'=>'Region', 'LookupField'=>'title', 'LookupTable'=>'region');
	
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
		$this->lookups['country']['Set']=&$this->regionset->regions['WORLD'];
		$this->lookups['state']['Set']=&$this->regionset->regions['US AND CANADA'];
	}


    //Generates a header based on the current search
    function search_text_header () {
		global $_REQUEST;
		$this->setupRegion();
		$search_type=$this->udm->name;
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
						$search_text[]="in ".$city_insert.$this->lookups['state']['Set'][$searchdata];
						break;
					case 'area':
                        break;
					case 'country':
						if (isset($this->lookups['country']['Set'][$searchdata])) {
							$search_text[]="in ".$this->lookups['country']['Set'][$searchdata];
						} elseif ($country_code=array_search($searchdata, $this->regionset->regions['WORLD']))	{ 
                            $search_text[]="in ".$searchdata;
                        }
						break;
					case 'uid':
						$search_text[]="by contact";
						break;
					case 'bydate':
						if (isset($_REQUEST['bydate'])&&$_REQUEST['bydate']&&strpos($search_type,"on or after")==0&&strpos($search_type,"upcoming")===FALSE) {
                            $search_type.=" changed on or after ".$this->lookups['bydate']['Set'][$_REQUEST['bydate']];
						}
						break;
						
                }
            }
        }
        if (isset($search_text)||$search_type!=$this->udm->name) { 
            if (substr($this->udm->name, strlen($this->udm->name)-1)!='s') 
                $search_type = str_replace( $this->udm->name, ($this->udm->name.'s'), $search_type);
            $header_text="Listing ".$search_type."<BR>".((is_array($search_text))?join(' ', $search_text):$search_text);
        } else {//default header - showing all
            $header_text="Listing All ".ucwords($this->udm->name);
            if (substr($this->udm->name, strlen($this->udm->name)-1)!='s') 
                $header_text.='s';
        }
        $header_class=($this->udm->admin?'header':'title');
        $header_text='<span class='.$header_class.'>'.$header_text.'</span><BR>';

        return $header_text;
    }

}
?>
