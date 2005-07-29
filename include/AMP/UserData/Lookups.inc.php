<?php

require_once ('AMP/System/Lookups.inc.php');

class FormLookup extends AMPSystem_Lookup {
    function FormLookup () {
        $this->init();
    }

    function &instance( $type, $lookup_baseclass="FormLookup" ) {
        return PARENT::instance( $type, $lookup_baseclass );
    }

}

class FormLookup_FormsbyPlugin extends FormLookup {
    var $datatable = "userdata_plugins";
    var $result_field = "instance_id";
    
    function FormLookup_FormsbyPlugin () {
        $this->init();
    }
}

class FormLookup_PluginsbyNamespace extends FormLookup {
    var $datatable = "userdata_plugins";
    var $result_field = "id";
    var $id_field = "namespace";
    var $sortby = "namespace";

    function FormLookup_PluginsbyNamespace( $namespace ) {
        $this->setNamespace( $namespace );
        $this->init();
    }

    function setNamespace ( $value ) {
       $dbcon = AMP_Registry::getDbcon();
       $this->criteria = "namespace=" . $dbcon->qstr($value); 
    }

    function &instance( $namespace ) {
        static $lookup  = false;
        if (!$lookup) {
            $lookup = new FormLookup_PluginsbyNamespace( $namespace ) ;
        } else {
            $lookup->setNamespace( $namespace );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class FormLookup_StartPluginsbyNamespace extends FormLookup {
    var $datatable = "userdata_plugins";
    var $result_field = "id";
    var $id_field = "id";
    var $sortby = "namespace";

    function FormLookup_StartPluginsbyNamespace( $namespace ) {
        $this->setNamespace( $namespace );
        $this->init();
    }

    function setNamespace ( $value ) {
       $dbcon = AMP_Registry::getDbcon();
       $this->criteria = "namespace=" . $dbcon->qstr($value) ." and action=" . $dbcon->qstr( 'Start' ); 
    }

    function &instance( $namespace ) {
        static $lookup  = false;
        if (!$lookup) {
            $lookup = new FormLookup_StartPluginsbyNamespace( $namespace ) ;
        } else {
            $lookup->setNamespace( $namespace );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class FormLookup_PluginsbyOptionDef extends FormLookup {
    var $datatable = "userdata_plugins_options";
    var $result_field = "plugin_id";
    var $id_field = "plugin_id";

    function FormLookup_PluginsbyOptionDef( $name, $value) {
        $this->setOptionDef( $name, $value );
        $this->init();
    }

    function setOptionDef ( $name, $value ) {
       $dbcon = &AMP_Registry::getDbcon();
       $this->criteria = "name=" . $dbcon->qstr( $name ) . ' and value=' . $dbcon->qstr( $value ); 
    }

    function &instance( $name, $value ) {
        static $lookup  = false;
        if (!$lookup) {
            $lookup = new FormLookup_PluginsbyOptionDef( $name, $value ) ;
        } else {
            $lookup->setOptionDef( $name, $value );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

class FormLookup_PluginNamespaces {
    var $datatable = "userdata_plugins";
    var $result_field = "namespace";

    function FormLookup_PluginNamespaces () {
        $this->init();
    }
}

class FormLookup_Names extends FormLookup {
    var $datatable = "userdata";
    var $result_field = "Concat( First_Name, ' ' , Last_Name ) as Name";
    var $sortby = "Last_Name, First_Name";

    function FormLookup_Names( $instance ) {
        $this->setInstance( $instance );
        $this->init();
    }

    function setInstance( $instance_id ) {
        $this->criteria = "modin=".$instance_id;
    }

    function &instance( $instance_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new FormLookup_Names ( $instance_id );
        } else {
            $lookup->setInstance( $instance_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
}

?>
