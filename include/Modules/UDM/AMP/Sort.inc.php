<?php
require_once ('AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Sort_AMP extends UserDataPlugin {
    var $sortby;
    var $select;
    var $available=true;
    var $order;
    var $sortname;
    var $options = array (
        /*'default_sortname'=>array(
            'value'=>"State/Org",
            'available'=>true,
            'description'=>'Text name of default sort'),
        'default_select'=>array(
            'value'=>"Concat( State, '-', Company) as `State/Org`"),
        'default_orderby'=>array(
            'value'=>"State,Company,Last_Name,First_Name"),*/
        'default_sortname'=>array(
            'value'=>"Name",
            'available'=>true,
            'description'=>'Text name of default sort'),
        'default_select'=>array(
            'value'=>"Concat(First_Name,' ',Last_Name) as Name"),
        'default_orderby'=>array(
            'value'=>"Last_Name,First_Name"),
        'default_sortname_admin'=>array(
            'value'=>'Name'),
            
        'default_select_admin'=>array(
            'value'=>'Concat( First_Name, " ", Last_Name ) as `Name`'),
            
        'default_orderby_admin'=>array(
            'value'=>'Last_Name,First_Name'));

    function UserDataPlugin_Sort_AMP(&$udm, $plugin_instance = null) {
        $this->init ($udm, $plugin_instance);
    }


    function execute ($options=null) {
        if (!isset($options)) {
            if ($this->executed) return $this->sortby;
        }
        $options = array_merge($this->getOptions(), $options);
        

		//Check sort
        if (isset($_REQUEST['sortby'])&&$_REQUEST['sortby']) {

            //If the request is set, see if the userdata fields are defined
            if ($searches = &$this->udm->getPlugins('Search')){
                $search_obj = $searches[key($searches)];
            }
            
            if ($search_obj && isset($search_obj->alias[$_REQUEST['sortby']])) {
                $sortalias=$search_obj->alias[$_REQUEST['sortby']];
                $this->sortname=ucwords($_REQUEST['sortby']);
                $this->select=$sortalias['f_sqlname'].' AS `'.$sortalias['f_alias'].'`';
                $this->orderby=$sortalias['f_orderby'];
                #print $this->select;
            } elseif (isset($this->udm->fields[$_REQUEST['sortby']])) {
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
            if ($this->udm->admin) {
                $this->sortname=$options['default_sortname_admin'];
                $this->select=$options['default_select_admin'];
                $this->orderby=$options['default_orderby_admin'];
            }
        }
        $this->sortby=array('name'=>$this->sortname, 'select'=>$this->select, 'orderby'=>$this->orderby);
        $this->executed=true;
        $this->udm->sortby = $this->sortby;
        return $this->sortby;

    }

    function makelink ($sortname) {
        #if ($searchform=&$this->udm->getPlugin('Output', 'SearchForm')) {
        #    $link=sprintf("<a href=\"javascript: document.forms['%1\$s'].elements['sortby'].value = '%2\$s'; 
        #        document.forms['%1\$s'].submit();\">%3\$s</a>", $searchform->options['form_name']['value'], $sortname, $sortname);

        if (!isset($this->udm->url_criteria)) $this->udm->parse_URL();
        $link='<a href="'.$_SERVER['PHP_SELF'].'?'.join('&', $this->udm->url_criteria).'&sortby='.$sortname.'">'.$sortname.'</a>';

        return $link;
    }
}

?>
