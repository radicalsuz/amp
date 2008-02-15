<?php
require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Read_Comments extends UserDataPlugin {
    var $options = array( 
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
    );
    var $available = true;
    var $_field_prefix = "plugin_Comments";

    function UserDataPlugin_Read_Comments( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ){
        $this->fields = array(
		    'comments_header' => array(
                'type'=>'header', 
                'label'=>'User Comments', 
                'public'=>false,  
                'enabled'=>true
                ),
            'comments_list' => array(
                'type'=>'html',
                'public'=>false,
                'enabled'=>true
                )
            );
    }

    function execute( $options = array( )){
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];

        require_once( 'AMP/Content/Article/Comment/List_Basic.inc.php');
        $comment_list = & new AMP_Content_Article_Comment_List_Basic( $this->dbcon, array( 'userdata_id' => $uid ));
        $comment_list->setEditLinkTarget( 'blank' );
        $comment_list->appendAddLinkVar( 'userdata_id='.$uid );

        $comment_list_output = $comment_list->execute( ) ;
        $this->udm->fields[ $this->addPrefix('comments_list') ]['values'] = $this->inForm( $comment_list_output );
    }

}

?>
