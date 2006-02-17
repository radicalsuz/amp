<?php
require_once ('Modules/Calendar/Plugin.inc.php');

class CalendarPlugin_Index_Output extends CalendarPlugin {

    function CalendarPlugin_Index_Output (&$calendar, $options=null, $instance=null) {   
        $this->init($calendar, $options, $instance);
    }

    function init (&$calendar, $options=null, $instance=null) {
        $this->dbcon=&$calendar->dbcon;
        $this->calendar= &$calendar;
    }

    function execute ($options=null) {

		$index['state']['name']="Upcoming Events By State";
		$index['state']['sql'] ="SELECT count(calendar.id) as qty, calendar.lstate as item_key, states.statename as item_name from calendar, states WHERE calendar.lstate=states.state and calendar.publish=1 and ((recurring_options>0 and enddate>=CURDATE()) OR (recurring_options=0 and date>=CURDATE())) GROUP BY calendar.lstate ";
		$index['caltype']['sql']="SELECT count(calendar.id) as qty, calendar.typeid as item_key, eventtype.name as item_name from calendar, eventtype WHERE calendar.typeid=eventtype.id and calendar.publish=1 and ((recurring_options>0 and enddate>=CURDATE()) OR (recurring_options=0 and date>=CURDATE())) GROUP BY calendar.typeid ORDER BY eventtype.name ";
		$index['caltype']['name']="Upcoming Events By Type";
        $output = "";
		foreach ($index as $index_key=>$this_index) {
			$index_set=$this->dbcon->CacheGetAll($this_index['sql']);
			$output.='<P><B>'.$this_index['name'].'</B><BR>';
			foreach ($index_set as $index_item) {
				$output.='<a href="'.$_SERVER['PHP_SELF'].'?'.$index_key.'='.$index_item['item_key'].'&bydate='.date("Y-m-d").'">'.$index_item['item_name'].'</a> ('.$index_item['qty'].')<BR>';
			}
		}
		return $output;
    }
        
    
    
/*	

	function a_slow_bad_object_based_approach_to_Index() {
		$index['state']['name']="Events By State";
        $index['state']['source_sql']="Select state as id_val, statename as description_val from states";
        $index['state']['cal_field']="lstate";
		$index['caltype']['name']="Events By Type";
        $index['caltype']['source_sql']="Select id as id_val, name as description_val from eventtype";
        $index['caltype']['cal_field']="typeid";
        
        $base_criteria="((recurring_options>0 and enddate>=CURDATE()) OR (recurring_options=0 and date>=CURDATE()))"; 
        $result=new CalendarSearch ($this->dbcon, $base_criteria); 

		foreach ($index as $index_key=>$this_index) {
			$source_set=$this->dbcon->GetArray($this_index['source_sql']);
			$output.='<P><B>'.$this_index['name'].'</B><BR>';
            foreach ($source_set as $index_id=>$index_val) {
                $criteria = $this_index['cal_field']."=".$this->dbcon->qstr($index_val['id_val']);
                $result_count=$result->count_events($criteria);
                if ($result_count>0) {
                    $output.='<a href="'.$_SERVER['PHP_SELF'].'?'.$index_key.'='.$index_val['id_val'].'&bydate='.date("Y-m-d").'">'.$index_val['description_val'].'</a> ('.$result_count.')<BR>';
                }
			}
		}

		return $output;
	}
    
 */   
}    
    
?>
