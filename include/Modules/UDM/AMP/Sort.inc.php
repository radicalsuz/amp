<?php

class UserDataPlugin_Sort_AMP extends UserDataPlugin {
    var $sortby;
    var $select;
    var $order;
    var $sortname;
    var $options = array (
        'default_sortname'=>array(
            'value'=>"State/Org",
            'available'=>true,
            'description'=>'Text name of default sort'),
        'default_select'=>array(
            'value'=>"Concat( State, '-', Company) as `State/Org`"),
        'default_orderby'=>array(
            'value'=>"State,Company,Last_Name,First_Name"),
        'default_sortname_admin'=>array(
            'value'=>'Name'),
            
        'default_select_admin'=>array(
            'value'=>'Concat( First_Name, " ", Last_Name ) as `Name`'),
            
        'default_orderby_admin'=>array(
            'value'=>'Last_Name,First_Name'));

    function UserDataPlugin_Sort_AMP(&$udm, $plugin_instance) {
        $this->init ($udm, $plugin_instance);
    }


    function execute ($options=null) {
        if (!isset($options)) {
            if ($this->executed) return $this->sortby;
            $options=$this->getOptions();
        }
        

		//Check sort
        if (isset($_REQUEST['sortby'])&&$_REQUEST['sortby']) {

            //If the request is set, see if the userdata fields are defined
            if (isset($this->udm->fields[$_REQUEST['sortby']])) {
                $sort_defs=&$this->udm->fields[$_REQUEST['sortby']];
            }
            if (is_array($sort_defs)) {
                /*
                //Set the select value to the sqlname from the db
                $this->select=$sort_defs['f_sqlname'];
                //If the field is aliased, set the column name to the alias
                if (isset($sort_defs['f_alias'])) {
                    $this->sortname=$sort_defs['f_alias'];
                    $this->select.=' AS `'.$sort_defs['f_alias'].'`';
                } else {
                    $this->sortname=$sort_defs['f_label'];
                }
                //If the field has a custom ordering value, set this as well
                if (isset($sort_defs['f_orderby'])) {
                    $this->orderby=$sort_defs['f_orderby'];
                } else {
                    $this->orderby=$sort_defs['f_sqlname'];
                }
            } else {
                //If no userdata field is defined, just set all the sort values
                //to the REQUEST value and hope that works :)
                //fixme
                */
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
        return $this->sortby;

    }

    function makelink ($sortname) {
        if ($searchform=&$this->udm->getPlugin('Output', 'SearchForm')) {
            $link=sprintf("<a href=\"javascript: document.forms['%1\$s'].elements['sortby'].value = '%2\$s'; 
                document.forms['%1\$s'].submit();\">%3\$s</a>", $searchform->options['form_name']['value'], $sortname, $sortname);
        } else {
            $link='<a href="'.$_SERVER['PHP_SELF'].'?'.$_SERVER['QUERY_STRING'].'&sortby='.$sortname.'">'.$sortname.'</a>';
        }
        return $link;
    }
}

?>
