<?php

require_once ('AMP/System/Lookups.inc.php');

class FormLookup extends AMPSystem_Lookup {
    function FormLookup () {
        $this->init();
    }

    function &instance( $type, $instance_var = null, $lookup_baseclass="FormLookup" ) {
        return parent::instance( $type, $instance_var, $lookup_baseclass );
    }

    function available( ){
        return false;
    }

}

class FormLookup_FormsbyPlugin extends FormLookup {
    var $datatable = "userdata_plugins";
    var $result_field = "instance_id";
    
    function FormLookup_FormsbyPlugin () {
        $this->init();
    }
}

class FormLookup_PublishedForms extends FormLookup {
    var $datatable = "userdata_fields";
    var $result_field = "publish";
    var $criteria = "publish=1";

    function FormLookup_PublishedForms( ){
        $this->init( );
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
    function available( ){
        return false;
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

    function available( ){
        return false;
    }
}

class FormLookup_PluginNamespaces extends FormLookup {
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

    function FormLookup_Names( $instance = false ) {
        $this->__construct( $instance );
    }

    function __construct( $instance_id = false ) {
        if ( $instance_id ) {
            $this->setInstance( $instance_id );
            $this->setPublic( $instance_id );
        }

        $this->init();

    }

    function setInstance( $instance_id ) {
        $this->criteria = "modin=".$instance_id;
    }

    function setPublic( $instance_id ) {
        $published_forms = FormLookup::instance( 'PublishedForms' );
        if ( !isset( $published_forms[ $instance_id ])) return;

        if ( !defined('AMP_USERMODE_ADMIN' )) {
            $this->criteria .= ' AND publish=' . AMP_CONTENT_STATUS_LIVE;
        }
    }

    function &instance( $instance_id ) {
        static $lookup = array( );
        if (!isset( $lookup[$instance_id])) {
            $lookup[$instance_id] = new FormLookup_Names ( $instance_id );
        } 

        return $lookup[$instance_id]->dataset;
    }
    function available( ){
        return false;
    }
}

class AMPSystemLookup_FormNames extends FormLookup_Names {
    function AMPSystemLookup_FormNames( $instance = false ) {
        $this->__construct( $instance );
    }
}


class FormLookup_Companies extends FormLookup {
    var $datatable = "userdata";
    var $result_field = "Company";
    var $sortby = "Company";

    function FormLookup_Companies( $instance ) {
        $this->setInstance( $instance );
        $this->init();
    }

    function setInstance( $instance_id ) {
        $this->criteria = "modin=".$instance_id;
    }

    function &instance( $instance_id ) {
        static $lookup = false;
        if (!$lookup) {
            $lookup = new FormLookup_Companies ( $instance_id );
        } else {
            $lookup->setInstance( $instance_id );
            $lookup->init();
        }
        return $lookup->dataset;
    }
    function available( ){
        return false;
    }
}


class FormLookup_FindScheduleForm  {

    var $namespace = 'AMPSchedule';
    var $id;
    var $result_form;

    function FormLookup_FindScheduleForm( $schedule_id ) {
        return $this->init( $schedule_id );
    }

    function init( $schedule_id ) {
        $this->id = $schedule_id;
        if( $result = $this->getPluginsByOption( $this->namespace ) ) {
            $formlist = &FormLookup::instance('FormsbyPlugin');
            $this->result_form =  $formlist[current( $result )];
        }
    }

    function getResult() {
        return $this->result_form;
    }
    
    function getPluginsByOption( $namespace ) {
        if (!isset($this->id)) return false;
        $option_plugins = FormLookup_PluginsbyOptionDef::instance( 'schedule_id', $this->id );
        if (empty( $option_plugins )) return false;

        $namespace_plugins = FormLookup_StartPluginsbyNamespace::instance( $namespace );
        if (empty( $namespace_plugins )) return false;

        $result = array_intersect( $option_plugins, $namespace_plugins ) ;
        return $result;
    }

    function available( ){
        return false;
    }
}

class FormLookup_FindAppointmentForm extends FormLookup_FindScheduleForm {
    var $namespace = 'AMPAppointment';

    function FormLookup_FindAppointmentForm ( $schedule_id ) {
        $this->init( $schedule_id );
    }
}

class FormLookup_IntroTexts {
    var $datatable = "moduletext";
    var $result_field = "name";

    function FormLookup_IntroTexts( ) {
        $this->init( );
    }

    function &instance( $form_id ) {
        static $results = array( );
        if ( isset( $results[ $form_id] )) return $results[ $form_id ];

        $results[ $form_id ] = array( );
        $tools = &AMPSystem_Lookup::instance( 'ToolsByForm' );
        if ( isset( $tools[ $form_id ])) {
            $modidsByIntrotext = &AMPSystem_Lookup::instance( 'toolsByIntrotext' );
            $result_ids = array_keys( $modidsByIntrotext, $tools[ $form_id ]);
            if ( !empty( $result_ids )) {
                $introtext = &AMPSystem_Lookup::instance( 'introtexts');
                $results[ $form_id ] = array_combine_key ( $result_ids, $introtext );
            }
        }
        return $results[ $form_id ];
    }
    function available( ){
        return false;
    }
}

class FormLookup_PluginOptions extends FormLookup {
    var $datatable = "userdata_plugins_options";
    var $result_field = "value";
    var $id_field = 'name';

    function FormLookup_PluginOptions( $plugin_id = null) {
        if ( isset( $plugin_id )) $this->addCriteriaPlugin( $plugin_id );
        $this->init( );
    }

    function addCriteriaPlugin( $plugin_id ){
        $this->criteria = 'plugin_id='.$plugin_id;
    }

    function &instance( $plugin_id ) {
        static $results = array( );
        if ( isset( $results[ $plugin_id] )) return $results[ $plugin_id ]->dataset;

        $results[ $plugin_id ] = &new FormLookup_PluginOptions( $plugin_id );
        return $results[ $plugin_id ]->dataset;
    }
    function available( ){
        return false;
    }
}

class FormLookup_PluginPriorities extends FormLookup {
    var $datatable = 'userdata_plugins';
    var $result_field = 'priority';

    function FormLookup_PluginPriorities( ){
        $this->init( );
    }
}

class FormLookup_Variant extends FormLookup {
    var $datatable = 'userdata';
    var $id_field = 'State';
    var $result_field = 'count( id ) as qty';
    var $criteria = 'modin = 1 GROUP BY State';
    var $_criteria_suffix = ' GROUP BY ';

    function FormLookup_Variant( $result_field=null, $modin=null ){
        $this->setCriteria( $result_field, $modin );
        $this->init( );
    }

    function makeCriteriaModin( $modin ){
        return 'modin=' . $modin ;
    }

    function setCriteria( $result_field, $modin ){
        if ( !( isset( $result_field) && isset( $modin ))) return false;
        $this->id_field = $result_field;
        $this->criteria = $this->makeCriteriaModin( $modin ). $this->_criteria_suffix . $result_field;
        return true;

    }

    function &instance( $result_field, $modin ){
        static $variant_lookups = array( );
        if ( isset( $variant_lookups[$modin]) && isset( $variant_lookups[$modin][$result_field])){
            return $variant_lookups[$modin][$result_field]->dataset;
        }
        $variant_lookups[$modin][$result_field] = new FormLookup_Variant( $result_field, $modin );
        return $variant_lookups[$modin][$result_field]->dataset;
    }
}

class FormLookup_Modin extends FormLookup {
    var $datatable = 'userdata';
    var $id_field = 'id';
    var $result_field = 'modin';

    function FormLookup_Modin( ){
        $this->init( );
    }
}

class FormLookup_CommentCounts extends FormLookup {
    var $datatable = 'comments';
    var $id_field = 'userdata_id';
    var $result_field = 'count( id ) as qty';
    var $criteria = '( !isnull( userdata_id ) AND userdata_id != "" ) GROUP BY userdata_id ';

    function FormLookup_CommentCounts ( ){
        $this->init( );
    }
}

class AMPSystemLookup_LiveForms extends AMPSystem_Lookup {
    var $datatable = 'userdata_fields';
    var $result_field = 'name';
    var $criteria = 'publish=1';

    function AMPSystemLookup_LiveForms( ) {
        $this->init( );
    }
}


class AMPSystemLookup_FormFields extends AMPSystem_Lookup {
    var $datatable = 'userdata_fields';
   
    function AMPSystemLookup_FormFields( $form_id ) {

        if ( !$form_id ) return false;
        require_once( 'AMP/System/UserData.php');
        $form_def = &new AMPSystem_UserData( AMP_Registry::getDbcon( ), $form_id );
        $data = $form_def->getData( );

        foreach( $data as $key => $value ) {
            if ( substr( $key, 0, 8 ) == 'enabled_' && $value ) {
                $short_key = substr( $key, 8 );
                $this->dataset[ $short_key ] = $data[ 'label_' . $short_key ];
            }
        }
    }
}
?>
