<?php

class AMP_System_Data_Tree {

    var $_tree = array( );
    var $_children = array( );
    var $_roots = array( );

    var $_tree_source;
    var $_source_object;

    function AMP_System_Data_Tree( &$source_object, $criteria = null ){
        $this->__construct( $source_object, $criteria );
    }

    function __construct( &$source_object, $criteria = null ){
        $this->_source_object = &$source_object;
        $this->_tree_source = &$source_object->search( $criteria );
        //$source_object->sort( $this->_tree_source, 'listOrder' );
        $this->_init_children( );  
        $this->_init_map( );  

    }

    function _init_children( ){
        if ( !$this->_tree_source ) return;
        foreach( $this->_tree_source as $child ){
            if ( $parent_id = $child->getParent( )) {
                $this->_children[ $child->id ] = $parent_id;
            }
        }
    }

    function _init_map( ){
        foreach( $this->_tree_source as $id => $item ){
            if ( isset( $this->_children[ $id ])) continue;
            $this->_init_branch( $id );
            $this->_roots[] = $id;
        }
    }

    function _init_branch( $root_id ) {
        if ( !( $children = array_keys( $this->_children, $root_id ))) return false;
        $this->_tree[ $root_id ] = $children;
        foreach( $children as $child_id ) {
            $this->_init_branch( $child_id );
        }
    }

    function get_ancestors( $item_id ) {
        if ( !isset( $this->_tree_source[ $item_id ] )) return false;
        $source = &$this->_tree_source[ $item_id ];
        $self = array( $item_id => $source->getName( ));
        if ( !( $parent_id = $this->get_parent( $item_id ))) return $self;

        $ancestors = $this->get_ancestors( $parent_id );
        return $self + $ancestors;

    }

    function get_parent( $item_id ) {
        if ( !isset( $this->_children[$item_id])) return false;
        $parent_id = $this->_children[$item_id];
        return $parent_id;
    }

    function get_children( $item_id ) {
        if ( !isset( $this->_tree[ $item_id ])) return false;
        return $this->_tree[ $item_id ];
    }

    function get_descendents( $item_id ) {
        if ( !( $children = $this->get_children( $item_id ))) return false;
        foreach( $children as $child_id ) {
            $results[] = $child_id;
            if ( !( $descendents = $this->get_descendents( $child_id ))) continue;
            $results = $results + $descendents;
        }
        return $results;
    }

    function get_depth( $item_id ) {
        if ( !( $parent_id = $this->get_parent( $item_id ))) return 0;
        return $this->get_depth( $parent_id ) + 1;
    }

    function has_children( $item_id ){
        if ( !isset( $this->_tree[ $item_id ])) return false;
        return count( $this->_tree[ $item_id ]);
    }

    function select_options( $item_id = null) {
        $option_set = array( );

        $root_set = isset( $item_id ) ? $this->get_children( $item_id ) : $this->_roots;
        if ( !$root_set ) $root_set = array( );

        foreach( $root_set as $id ) {
            $option_set[$id] = $this->render_option( $id ) ;
            $child_options = $this->select_options( $id );
            if ( $child_options ) $option_set = $option_set + $child_options;
        }

        return $option_set;
    }

    function render_option( $id ) {
        if ( !isset( $this->_tree_source[ $id ])) return "$id not found";
        return str_repeat( '&nbsp;', $this->get_depth( $id ) * 4 ) 
                . $this->_tree_source[ $id ]->getName( );
    }

    function &get_item( $id ){
        if ( !isset( $this->_tree_source[ $id ])) return false;
        return $this->_tree_source[ $id ];
    }

}


?>
