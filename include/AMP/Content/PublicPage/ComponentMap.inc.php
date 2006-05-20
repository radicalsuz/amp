<?php
require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_PublicPage extends AMPSystem_ComponentMap {

    var $heading = "Public Page";
    var $nav_name = "content";
    var $_path_controller = 'AMP/Content/PublicPage/Controller.php';
    var $_component_controller = 'PublicPage_Controller';
    var $_action_displays = array( 'add' => 'form');
    var $_action_default = 'add';

    var $paths = array( 
        'fields'    =>  'AMP/Content/PublicPage/Fields.xml',
        'form'      =>  'AMP/Content/PublicPage/Form.inc.php'
        );
    
    var $components = array( 
        'form'  => 'PublicPage_Form'
        );

    /**
     * onInitForm 
     * Populate the form with default values based on different AMP Modules that can publish to the Content System
     * Currently handles IntroTexts, Photo Galleries, Petitions, and Web Actions
     * 
     * @param AMP_System_Component_Controller &$controller 
     * @access public
     * @return void
     */
    function onInitForm( &$controller ){

        $form = &$controller->get_form( );

        if ( (  $introtext_id = $controller->assert_var( 'mod_id' ) ) 
             || $introtext_id = $controller->assert_var( 'introtext_id' ) ) 
            return $this->_initFormForIntrotext( $form, $introtext_id );
        if ( ( $petition_id = $controller->assert_var( 'pid' )) 
             || $petition_id = $controller->assert_var( 'petition_id' ) ) 
            return $this->_initFormForPetition( $form, $petition_id );
        if ( ( $action_id = $controller->assert_var( 'action' )) 
             || $action_id = $controller->assert_var( 'webaction_id' ) ) 
            return $this->_initFormForWebAction( $form, $action_id );
        if ( ( $gallery_id = $controller->assert_var( 'gallery' )) 
             || $gallery_id = $controller->assert_var( 'gallery_id' ) ) 
            return $this->_initFormForGallery( $form, $gallery_id );
        ampredirect( AMP_SYSTEM_URL_INDEX );
    }

    function _initFormForIntrotext( &$form , $introtext_id ){
        require_once( 'AMP/System/Introtext.inc.php');
        $dbcon = &AMP_Registry::getDbcon( );
        $publish_item = &new AMPSystem_Introtext( $dbcon, $introtext_id);
        $GLOBALS['modid'] = $publish_item->getToolId( );
        return $this->_initFormPublishItem( $form, $publish_item );
    }

    function _initFormForPetition( &$form, $petition_id ){
        require_once( 'Modules/Petition/Petition.php');
        $dbcon = &AMP_Registry::getDbcon( );
        $publish_item = &new Petition( $dbcon, $petition_id);
        return $this->_initFormPublishItem( $form, $publish_item, AMP_CONTENT_CLASS_ACTIONITEM );
    }

    function _initFormForGallery( &$form, $gallery_id ){
        require_once( 'Modules/Gallery/Gallery.php');
        $dbcon = &AMP_Registry::getDbcon( );
        $publish_item = &new Gallery( $dbcon, $gallery_id);
        $title = 'Photo Gallery: '.$publish_item->getName( );
        return $this->_initFormPublishItem( $form, $publish_item, AMP_CONTENT_CLASS_DEFAULT, $title );
    }

    function _initFormForWebAction( &$form, $action_id ){
        require_once( 'Modules/WebAction/WebAction.php');
        $dbcon = &AMP_Registry::getDbcon( );
        $publish_item = &new WebAction( $dbcon, $_REQUEST['action']);
        $class_id = AMP_CONTENT_CLASS_ACTIONITEM;
        return $this->_initFormPublishItem( $form, $publish_item, AMP_CONTENT_CLASS_ACTIONITEM );

    }

    function _initFormPublishItem( &$form, &$publish_item, $class_id = AMP_CONTENT_CLASS_DEFAULT, $title = null  ){
        if ( !isset( $title )) $title = $publish_item->getName( );
        $form_values = array( 
            'title' => $title,
            'blurb' =>  $publish_item->getBlurb( ),
            'class' => $class_id,
            'link'  =>  $publish_item->getURL( )
            );

        if ( !$form_values['link']){
            require_once( 'AMP/System/Flash.php');
            $flash = &AMP_System_Flash::instance( );
            $flash->add_message( AMP_TEXT_CONTENT_PUBLIC_NO_LINK );
        }

        $form->setValues( $form_values );

    }

}

?>
