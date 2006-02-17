<?php
require_once ('Modules/Calendar/Plugin.inc.php');

class CalendarPlugin_Search_AMP extends CalendarPlugin {
	var $criteria;
	var $total_qty;
	var $sortby;
    var $options = array (
        'global_criteria'=> array (
            'available'=>true,
            'description'=>'Required criteria in all searches'
            ));
    var $alias = array(
            'Event'=>array(
                'f_alias'=>'Event',
                'f_orderby'=>'event',
                'f_type'=>'text',
                'f_sqlname'=>"event"
             ),
            'City'=>array(
                'f_alias'=>'City',
                'f_orderby'=>'lcity',
                'f_type'=>'text',
                'f_sqlname'=>"lcity"
             ),
            'State'=>array(
                'f_alias'=>'State',
                'f_orderby'=>'lstate',
                'f_type'=>'text',
                'f_sqlname'=>"lstate"
             ),
            'Scheduled'=>array(
                'f_alias'=>'Scheduled',
                'f_orderby'=>'recurring_options, date, lcity',
                'f_type'=>'text',
                'f_sqlname'=>"if(recurring_options>0, elt(recurring_options,'MultiDay','Weekly','Monthly','Annual'), DATE_FORMAT(date, '%c-%d-%Y'))"
             ),
             'Status'=>array(
                'f_alias'=>'Status',
                'f_orderby'=>'publish, recurring_options, date, lcity',
                'f_type'=>'text',
                'f_sqlname'=>'if(publish=1,"Live","Draft")'
              ));
    
    function CalendarPlugin_Search_AMP (&$calendar, $plugin_instance=null) {
        
        $this->init ($calendar, $plugin_instance);
		if (isset( $this->options['global_criteria'])
            && isset( $this->options['global_criteria']['value'])
            && ($this->options['global_criteria']['value'])) { 
                $this->criteria[]=$this->options['global_criteria']['value']; 
        }
		if (!$this->calendar->admin) { 
            $this->criteria[]="publish=1"; 
        }
        if (isset($this->calendar->sql_criteria)) { 
            $this->criteria=array_merge($this->criteria, $this->calendar->sql_criteria);
        }
	}

    function setSort() {

        $this->sortby=$this->calendar->doAction('Sort');
            
    }


	function execute ($options=null) {
		//combine init criteria with passed criteria 
        $options=array_merge($this->getOptions(), $options);
		if(isset( $options['criteria']) && is_array($options['criteria'])) {
			$this->criteria = array_merge($this->criteria, $options['criteria']);
		}
		
		//count total records in search AND get Jump Index
		$this->total_qty=$this->count_events();
        if (!isset($this->sortby)) $this->setSort();
		
		//get Event Records for current page
        $fieldset = "";
        foreach ($this->alias as $fname=>$fdef) {
            if (isset($fdef['f_sqlname'])) $fieldset.=$fdef['f_sqlname'].(isset($fdef['f_alias'])?" AS ".$fdef['f_alias']:"").", ";
        }
        $fieldset .= "calendar.*";

        if ($eventset=$this->return_events($fieldset, $this->criteria, $this->sortby['orderby'])) {
            $this->calendar->setEvents($eventset);
            return $this->calendar;
        } else {
            $this->calendar->error="No events found to match your search";
            return false;
        }
	}

    function count_events ($criteria=null) {		
		//combine init criteria with passed criteria 
		if(is_array($criteria)) {
			$criteria = array_merge($this->criteria, $criteria);
		} else {
            $criteria = $this->criteria;
        }

		$index_sql="SELECT count(id) as qty from calendar where ".join(" AND ", $criteria);
        if (AMP_DISPLAYMODE_DEBUG) AMP_DebugSQL( $index_sql, "calendarCount" );
        if ($indexset=$this->dbcon->Execute($index_sql)) {
		    $total_qty=$indexset->Fields("qty");
            return $total_qty;
        } else {
            return false;
        }
            
    }

    //Create an index of records based on the sort criteria
    //or supplied values
    function get_index ($qty, $criteria=null, $index_col=null, $orderby=null) {		
        if (!isset($criteria)) $criteria=$this->criteria; 
        if (!isset($this->sortby)) $this->setSort();
        if (!isset($index_col)) $index_col=$this->sortby['select'];
        if (!isset($orderby)) $orderby=$this->sortby['orderby'];
		$index_sql="SELECT id from calendar where ".join(" AND ", $criteria);
        if (isset($orderby)) $index_sql.= " ORDER BY ".$orderby;
        if (AMP_DISPLAYMODE_DEBUG)  AMP_DebugSQL( $index_sql,  "calendar_index1");
		if($indexset=&$this->dbcon->CacheGetAll($index_sql))  {
            for ($n=0;$n<=count($indexset);$n=$n+$qty) {
                $index_jumpset[]=$indexset[$n]['id'];
            }
            $index_ids=join(",", $index_jumpset);
            $index_sql="SELECT ".$index_col." FROM calendar where id in(".$index_ids.")";
            if (isset($orderby)) $index_sql.= " ORDER BY ".$orderby;
            if (AMP_DISPLAYMODE_DEBUG)  AMP_DebugSQL( $index_sql,  "calendar_index2");
            if($indexset=&$this->dbcon->CacheGetAll( $index_sql )) return $indexset;
            else return false;
        } else { 
            return false;
        }
    }

    function return_events($fieldset, $criteria, $orderby=null, $return_qty="*", $offset=0) {
		$sql="SELECT $fieldset from calendar where ".join(" AND ", $criteria);
		$sql.=(isset($orderby))?" ORDER BY ".$orderby:"";
        if ($pager=&$this->calendar->getPlugin('Output', 'Pager')) {
            $sql.=($pager->return_qty!="*")?" LIMIT ".strval($pager->offset). ", ".strval($pager->return_qty):"";
        } else {
            $sql.=($return_qty!="*")?" LIMIT ".strval($offset). ", ".strval($return_qty):"";
        } 
        if (AMP_DISPLAYMODE_DEBUG)  AMP_DebugSQL( $sql,  "calendar_main");
		if ($eventset=$this->dbcon->CacheGetAll($sql)) {
            return $eventset;
        } else {
            return false;
        }
        
    }
}


?>
