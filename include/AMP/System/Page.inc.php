<?php

/* * * * * * * *
 *
 * AMPSystem_Page
 *
 * A controller for AMP system-side pages
 * takes action based on form submits
 * shows lists or calls up form data as required
 *
 * AMP 3.5.0
 * 2005-07-03
 * Author: austin@radicaldesigns.org
 *
 * * * **/

require_once( 'AMP/System/Base.php' );
require_once( 'AMP/System/Page/Display.inc.php' );
require_once( 'AMP/System/Page/Urls.inc.php');

define( 'AMP_PAGE_ACTION_COMPONENT_INIT', 'init' );
define( 'AMP_PAGE_ACTION_COMPONENT_EXEC', 'execute' );

class AMPSystem_Page {

    var $form;
    var $list;
    var $copier;
    var $source;
    
    var $includes = array();

    var $action = 'View';

    var $dbcon;
    var $show = array();
    var $title;
    var $component_map;
    var $component_headers;

    var $results = array();
    var $errors = array();

    var $_performed_action;

    function AMPSystem_Page ( &$dbcon, $component_map=null ) {
        $this->init ($dbcon, $component_map);
    }

    function init( &$dbcon, $component_map=null ) {
        $this->dbcon = &$dbcon;
        if (!isset($component_map)) return;
        $this->component_map = &$component_map;
        $this->_setIncludeFileValues();
        $this->_setComponentNames();

    }

    function execute() {
        $this->_initSearch( );
        if ($this->showList()) return true;

        $this->_initForm();

        $action = $this->form->submitted();
        if ( !$action ) $action = "read"; 

        return $this->doAction ( $action );
    }

    function doAction( $action ) {

        $action_method = 'commit' . ucfirst( $action );

        if (method_exists( $this, $action_method )) {
            return $this->$action_method();
            $this->_performed_action = $action;
        }
    }

    function getAction( ){
        return $this->_performed_action;
    }

    function output ( ) {
        $display = & new AMPSystem_Page_Display( $this );
        $diplay_title = "Item";
        
        if (isset($this->component_map)) { 
            $display_title = $this->component_map->getHeading();
            $display->setNavName( $this->component_map->getNavName() );
        }

        $display->setItemType( $display_title );
        if ($this->showList()) $this->_initComponents('list');

        return $display->execute();
    }

    function hasSearch( ){
        if ( !isset( $this->component_map )) return false;

        $components = $this->component_map->getComponents( );
        return ( isset( $components['search']));
    }

    function _initSearch( ){
        if( !$this->hasSearch( )) return false;

        $this->_initComponents ( "search" );
        $this->search->Build( true );

        if ($action = $this->search->submitted() ) $this->doAction( $action );
        else $this->_setSearchFormDefaults();

    }
        

    function _setSearchFormDefaults() {
        $this->search->applyDefaults();
    }

    function commitSearch() {
        $this->_initComponents( 'list' );
        $this->list->applySearch($this->search->getSearchValues());
        $this->showList( true );
    }
    function _setIncludeFileValues( ) {
        $filepaths = $this->component_map->getFilePaths();
        foreach ($filepaths as $type => $filename ) {
            if (!file_exists_incpath ($filename) ) {
                trigger_error( 'System Page did not find component '.$type.' at: '.$filename );
                continue;
            }
            $this->includes[$type] = $filename;
        }
    }

    function setIncludeFile( $filename, $component ) {
        $this->includes[$component] = $filename;
    }

    function setComponentName( $classname, $component ) {
        $this->component_class[$component] = $classname;
    }

    function _setComponentNames( ) {
        $this->component_class = $this->component_map->getComponents();
    }

    function _requireComponents( $component_type=null ) {
        if (!is_array( $this->includes)) return false;

        $required_set = $this->includes;

        if (isset($component_type)) {
            $comp = is_array($component_type)? $component_type : array( $component_type );
            $required_set = array_combine_key ($comp, $this->includes );
        }

        foreach ($required_set as $type => $filename ) {
            require_once( $filename );
        }
    }

    function _initComponents ( $component_type = null, $reset=false ) {
        $init_classes = $this->component_class;
        $this->_requireComponents( $component_type );

        if (isset($component_type)) {
            $comp = is_array($component_type)? $component_type : array( $component_type );
            $init_classes = array_combine_key ($comp, $this->component_class );
        }

        foreach ($init_classes as $type => $component_def ) {
            if (isset($this->$type) && (!$reset)) continue;
            $classname = $component_def;
            if (!class_exists( $classname )) continue;
            $this->$type = & new $classname ( $this->dbcon );
            $this->doCallbacks( $type, AMP_PAGE_ACTION_COMPONENT_INIT ); 

        }
    }

    function _initForm() {
        $this->_initComponents( 'form' );
        if (!isset($this->includes['copier'])) $this->form->removeSubmit( 'copy' );
        if (!isset($_REQUEST['id'])) {
            $this->form->removeSubmit( 'copy' );
            $this->form->removeSubmit( 'delete' );
        }
        $this->form->Build();
    }


    function showList( $value=null ) {
        if (!isset($value)) return (isset($this->show['list'])?
                                        $this->show['list']
                                        : false);
        if (!$value) return $this->dropComponent('list');
        if ( $this->hasSearch( ))$this->addComponent('search', $value);
        $this->addComponent('list', $value);
        return $value;
    }

    function dropComponent( $comp_name ) {
        unset ($this->show[ $comp_name ]);
    }

    function addComponent( $comp_name, $vars=true ) {
        $this->show[$comp_name] = $vars;
    }

    function orderComponents( $order_array ) {
        if (!is_array( $order_array )) return false;
        $new_show = array();
        foreach ($order_array as $component) {
            if (!isset($this->show[ $component ])) continue;
            $new_show[ $component ] = $this->show[ $component ];
        }


        $this->show = $new_show;
    }

    function addComponentHeader( $comp_name, $header_value ) {
        $this->component_headers[ $comp_name ] = $header_value;
    }

    function getComponentHeader( $comp_name ) {
        if (!isset($this->component_headers[ $comp_name ])) {
            if (!method_exists($this->$comp_name, 'getComponentHeader')) return false;
            return $this->$comp_name->getComponentHeader();
        }

        return $this->component_headers[ $comp_name ];
    }

    function commitCancel() {
        return $this->showList( true );
    }

    function commitSave() {
        $this->addComponent('form');
        if (!$this->form->validate()) return false;
        $value_set = $this->form->getValues();

        $this->_initComponents( 'source' );
        $this->source->setData( $value_set );

        if ($this->source->save()) {
            if (!($itemname = $this->form->getItemName())) $itemname = "Item";
            $this->setMessage( $itemname . " has been saved." );
            if ( method_exists( $this->form, 'postSave' )) {
                $this->form->postSave( $this->source->getData() );
            }
            $this->dropComponent('form');
            return $this->showList( true );
        }

        $this->setMessage( $this->source->getErrors() , true);
        return false;
    }

    function commitRead() {
        $id = $this->form->getIdValue();
        if (!$id ) {
            $this->action = 'Add';
            $this->addComponent('form');
            $this->form->applyDefaults();
            return false;
        }

        $this->_initComponents( 'source' );
        if ( $this->source->readData( $id ) ) {
            $this->form->setValues( $this->source->getData() );
            $this->action = "Edit";
            $this->addComponent('form');
            return true;
        }

        return $this->showList( true );
    }

    function commitCopy() {
        $this->addComponent('form');
        if (!$this->form->validate()) return false;

        $id = $this->form->getIdValue();
        if (!$id) return $this->commitSave();

        $this->_initComponents( 'source' );
        
        $value_set = $this->form->getValues();
        unset($value_set[ $this->source->id_field ] );
        $this->source->setData( $value_set );

        /* This is a great DB item copier, but not a good Save As method -ap 2005-08-19

        $this->_initComponents( array( 'source','copier' ) );
        $this->source->readData( $id );
        $this->copier->setOriginal( "id=".$id );

        $namefield = $this->form->name_field;
        $this->copier->setOverride($namefield, $this->form->getItemName(), $this->source->getData($namefield));

        */
        if ($this->source->save() ) {
            $this->setMessage(  "Your working copy was saved as ". $this->form->getItemName() );
            $this->dropComponent('form');
            return $this->showList( true );
        }

        $this->setMessage("Save As ".$this->form->getItemName()." failed: ".$this->source->getErrors(), true );
        return false;
    }


    function commitDelete() {
        $id = $this->form->getIdValue();
        if (!$id) return false;
        
        $this->_initComponents( 'source' );
        $epitaph = "";
        if ($this->source->deleteData( $id )) {
            if ($name = $this->form->getItemName()) $epitaph = " for $name";
            $this->setMessage( "The record$epitaph was deleted" );
            return $this->showList( true );
        }

        $this->setMessage( $this->source->getErrors(), true );
        $this->addComponent('form');
        return false;
    }

    function &getComponents() {
        $this->_initComponents( array_keys($this->show) );
        return $this->show;
    }

    function setMessage( $message, $is_error = false ) {
        $text = $message;
        if (is_array( $message ) && isset($message['error']) && $message['error']) {
            $is_error = true;
            $text = $message['text'];
        }
        if ($is_error) return ($this->errors[] = $text);
        return ($this->results[] = $text);

    }

    function getResults() {
        return $this->results;
    }

    function getErrors() {
        return $this->errors;
    }

    function addCallback( $component_type, $method_def, $args=array(), $trigger = AMP_PAGE_ACTION_COMPONENT_INIT ) {
        if (!is_array($args)) $args = array( $args );
        $this->callbacks[ $trigger ][ $component_type ][] = array('method'=>$method_def, 'args'=>$args );
    }

    function getCallbacks( $component_type, $trigger = AMP_PAGE_ACTION_COMPONENT_INIT ) {
        if (!isset( $this->callbacks[ $trigger ] )) return false;
        if (!isset( $this->callbacks[ $trigger ][$component_type] )) return false;
        return $this->callbacks[$trigger][ $component_type ];
    }

    function doCallbacks( $component_type, $trigger = AMP_PAGE_ACTION_COMPONENT_INIT ) {
        if (! ($actions = $this->getCallbacks( $component_type, $trigger ))) return false;
        if (! isset($this->$component_type)) return false;
        $component = &$this->$component_type;

        foreach( $actions as $action_def ) {
            if (!method_exists( $component, $action_def['method'])) continue;
            call_user_func_array( array( &$component, $action_def['method'] ), $action_def['args'] );
        }
        return true;
    }


}
?>        
