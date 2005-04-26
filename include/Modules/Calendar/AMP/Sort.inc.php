<?php
require_once ('Modules/Calendar/Plugin.inc.php');

class CalendarPlugin_Sort_AMP extends CalendarPlugin {
    var $sortby;
    var $select;
    var $order;
    var $sortname;
    var $options = array (
        'default_sortname'=>array(
            'value'=>"Location",
            'available'=>true,
            'description'=>'Text name of default sort'),
        'default_select'=>array(
            'value'=>"Concat( if(!isnull(lcountry), Concat(lcountry, ' - '),''), if(!isnull(lstate), Concat(lstate, ' - '),''), if(!isnull(lcity), lcity,'')) as Location"),
        'default_orderby'=>array(
            'value'=>"lcountry, lstate, lcity, recurring_options"),
        'default_sortname_admin'=>array(
            'value'=>'Status/Date'),
            
        'default_select_admin'=>array(
            'value'=>'Concat( if(publish=1,"Live","Draft"), " / ", DATE_FORMAT(date, "%Y - %M")) as `Status/Date`'),
            
        'default_orderby_admin'=>array(
            'value'=>'publish, recurring_options, date, lcity') );

    function CalendarPlugin_Sort_AMP(&$calendar, $plugin_instance) {
        $this->init ($calendar, $plugin_instance);
    }


    function execute ($options=null) {
        if (!isset($options)) {
            if ($this->executed) return $this->sortby;
            $options=$this->getOptions();
        }
        

		//Check sort
        if (isset($_REQUEST['sortby'])&&$_REQUEST['sortby']) {

            //If the request is set, see if the calendar fields are defined
            if ($search_obj=&$this->calendar->getPlugin('AMP', 'Search') 
                    && isset($search_obj->alias[$_REQUEST['sortby']])) {
                $sortalias=$search_obj->alias[$_REQUEST['sortby']];
                $this->sortname=ucwords($_REQUEST['sortby']);
                $this->select=$sortalias['f_sqlname'].' AS `'.$sortalias['f_alias'].'`';
                $this->orderby=$sortalias['f_orderby'];
                #print $this->select;
            } elseif (isset($this->calendar->fields[$_REQUEST['sortby']])) {
                $this->sortname=ucwords($_REQUEST['sortby']);
                $this->select=$_REQUEST['sortby'];
                $this->orderby=$_REQUEST['sortby'];
            }
    
        } else {

            //Setup the default sort
            $this->sortname=$options['default_sortname'];
            $this->select=$options['default_select'];
            $this->orderby=$options['default_orderby'];

            //Setup the default sort for Admin view
            if ($this->calendar->admin) {
                $this->sortname=$options['default_sortname_admin'];
                $this->select=$options['default_select_admin'];
                $this->orderby=$options['default_orderby_admin'];
            }
        }
        $this->sortby=array('name'=>$this->sortname, 'select'=>$this->select, 'orderby'=>$this->orderby);
        $this->executed=true;
        return $this->sortby;

    }

    function makelink ($sortname) {
        if (!isset($this->calendar->url_criteria)) $this->calendar->parse_URL();
        $link='<a href="'.$_SERVER['PHP_SELF'].'?'.join('&', $this->calendar->url_criteria).'&sortby='.$sortname.'">'.$sortname.'</a>';

        return $link;
    }
}

?>
