<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php'    );
require_once( 'AMP/Content/Article.inc.php'         );
require_once( 'AMP/Content/Article/Public/Form.inc.php' );
require_once( 'AMP/Content/Article/Public/ComponentMap.inc.php' );

class UserDataPlugin_Save_Article extends UserDataPlugin_Save {

    var $name = 'Save Articles';
    var $available = true;

    var $options = array( 
        'allowed_sections' => array( 
            'type' => 'multiselect',
            'label' => 'Section',
            'size'  => 5,
            'available' => true,
            'default' => AMP_CONTENT_MAP_ROOT_SECTION,
            ),
        'allowed_classes' => array( 
            'type' => 'multiselect',
            'label' => 'Class',
            'size'  => 5,
            'available' => true,
            'default' => AMP_CONTENT_CLASS_USERSUBMITTED,
            ),
        'auto_publish' => array( 
            'type' => 'checkbox',
            'default' => '',
            'available' => true,
            'label' => 'Publish Articles Automatically',
            ),
        'result_mapping' => array( 
            'type' => 'text',
            'size' => '15',
            'default' => '',
            'label' => 'Save returned Article id to:',
            'available' => true
            ),
        );

    var $_field_prefix = 'plugin_Article_Save';

    function UserDataPlugin_Save_Article(  &$udm, $plugin_instance=null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        $article_form = &new Article_Public_Form( );
        $this->fields = $article_form->getFields( ) ;
        $this->insertBeforeFieldOrder( array_keys( $this->fields ));
    }

    function _register_options_dynamic( ) {
        $this->options['allowed_classes']['values'] = AMP_lookup( 'classes');
        $this->options['allowed_sections']['values'] = AMP_lookup( 'sectionMap');
        if ( is_array( $this->options['allowed_sections']['values'])) {
            $this->options['allowed_sections']['values'] = array( AMP_CONTENT_MAP_ROOT_SECTION => '-- ' . AMP_SITE_NAME . ' --' )+ $this->options['allowed_sections']['values'];
        }
    }

    function getSaveFields( ) {
        return $this->getAllDataFields( );
    }

    function save( $data, $options = array( ) ) {
        $options = array_merge( $this->getOptions( ), $options );
        $data = $this->validate( $data, $options );
        if ( !$data ) return false;

        $article = &new Article( AMP_Registry::getDbcon( ));
        $article->setDefaults( );
        $article->mergeData( $data );
        $result = $article->save( );
        if ( !$result ) return false;

        if ( isset( $options['result_mapping']) && $options['result_mapping']) {
            $update_array[ $options['result_mapping']] = $article->id;
            $save_plugin = &$this->udm->registerPlugin( 'AMP', 'Save');
            $save_plugin->save( $update_array );
        }

        return $result;

    }

    function validate( $data, $options  ) {
        $flash = AMP_System_Flash::instance( );

        if ( $options['allowed_sections']) {
            $allowed_sections = explode( ',', $options['allowed_sections']);
            if ( isset( $data['section'])) {
                if ( array_search( $data['section'], $allowed_sections ) === FALSE ) {
                    $flash->add_error( 'Publishing to requested section not allowed');
                    return false;
                }

            } else {
                $data['section'] = $allowed_sections[0];
            }
        } else {
            $data['section'] = AMP_CONTENT_MAP_ROOT_SECTION;
        }
        if ( $options['allowed_classes']) {
            $allowed_classes = explode( ',', $options['allowed_classes']);
            if ( isset( $data['class'])) {
                if ( array_search( $data['class'], $allowed_classes ) === FALSE ) {
                    $flash->add_error( 'Publishing to requested class not allowed');
                    return false;
                }

            } else {
                $data['class'] = $allowed_classes[0];
            }
        }
        if ( isset( $options['auto_publish'])) {
            $data['publish'] = $options['auto_publish'];
        }
        return $data;

    }

}

?>
