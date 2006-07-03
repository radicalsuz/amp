<?php

require_once( 'AMP/System/Data/Item.inc.php');

class AMP_System_Setup extends AMPSystem_Data_Item {

    var $datatable = "sysvar";
    var $name_field = "websitename";

    var $_keys_template_setup = array( 
        'indextemplate'
    );

    var $_keys_phplist_setup = array( 
        'phplist_website',
        'phplist_domain',
        'phplist_admin_address',
        'phplist_report_address',
        'phplist_message_from_address',
        'phplist_message_from_name',
        'phplist_message_replyto_address',
    );

    var $_keys_phplist_admin_setup = array( 
        'phplist_admin_password' ,
        'phplist_admin_email' 
    );

    var $_keys_translation_punbb = array( 
        'o_board_title' => 'websitename',
        'o_admin_email' => 'emfaq',
        'o_webmaster_email' => 'emfaq',
        'o_mailing_list' => 'emfaq',
        'o_base_url' => 'basepath'
    );

    var $_value_suffix_punbb = array( 
        'o_board_title' => ' Forum',
        'o_base_url'    => '/punbb'
    );

    function AMP_System_Setup ( &$dbcon, $id = AMP_SYSTEM_SETTING_DB_ID ) {
        $this->init( $dbcon, $id );
        $this->_initExtraSetup( );
    }

    function _initExtraSetup( ){
        $extra_keys = array_merge( 
                        $this->_keys_phplist_setup, 
                        $this->_keys_phplist_admin_setup, 
                        $this->_keys_template_setup 
                    );
        foreach( $extra_keys as $extra_key ){
            $this->_addAllowedKey( $extra_key );
        }

    }

    function _afterRead( ){
        $this->_readTemplates( );
        $this->_readPHPlistConfig( );
        $this->_readPunbbConfig( );
    }

    function _afterSave( ){
        $this->_updateTemplates( );
        $this->_updatePHPlistConfig( );
        $this->_updatePunbbConfig( );
    }

    function _updateTemplates( ){
       require_once( 'AMP/System/IntroText.inc.php') ;
       $frontpage = &new AMPSystem_IntroText( $this->dbcon, AMP_CONTENT_INTRO_ID_FRONTPAGE );
       $template_id_frontpage = $this->getTemplateIdFrontpage( );
       if ( !$template_id_frontpage ) return false;
       if ( $template_id_frontpage == $frontpage->getTemplate( )) return true;
       $frontpage->setTemplate( $template_id_frontpage );
       return $frontpage->save( );
    }

    function _readTemplates( ){
       require_once( 'AMP/System/IntroText.inc.php') ;
       $frontpage = &new AMPSystem_IntroText( $this->dbcon, AMP_CONTENT_INTRO_ID_FRONTPAGE );
       $this->setTemplateIdFrontpage( $frontpage->getTemplate( ));

    }

    function _updatePHPlistConfig( ){
        require_once( 'Modules/Blast/Config/Config.php');
        $phplist_config_data = array_combine_key( $this->_keys_phplist_setup, $this->getData( ));
        if ( empty( $phplist_config_data )) return false;
        foreach( $phplist_config_data as $local_key  => $value ){
            $phplist_key = str_replace( 'phplist_', '', $local_key );
            $config_setting = &new Blast_Config( $this->dbcon, $phplist_key );
            $current_value = $config_setting->getValue();
            if ( $current_value == $value ) continue;

            $config_setting->setValue( $value );
            $config_setting->save( );
        }

        require_once( 'Modules/Blast/Config/Admin.php');
        $phplist_admin_data = array_combine_key( $this->_keys_phplist_admin_setup, $this->getData( ));
        $admin_data = array( );
        foreach( $phplist_admin_data as $local_key => $value ){
            $phplist_key = str_replace( 'phplist_admin_', '', $local_key );
            $admin_data[ $phplist_key ] = $value;
        }
        $admin_settings = &new Blast_Config_Admin( $this->dbcon, PHPLIST_CONFIG_ADMIN_ID );
        $admin_settings->mergeData( $admin_data );
        return $admin_settings->save( );
    }

    function _readPHPlistConfig( ){
        require_once( 'Modules/Blast/Config/Config.php');
        $phplist_config_data = array_combine_key( $this->_keys_phplist_setup, $this->getData( ));
        foreach( $this->_keys_phplist_setup as $local_key ){
            $phplist_key = str_replace( 'phplist_', '', $local_key );
            $config_setting = &new Blast_Config( $this->dbcon, $phplist_key );
            $phplist_config_data[ $local_key ] = $config_setting->getValue( );
        }
        $this->mergeData( $phplist_config_data );

        require_once( 'Modules/Blast/Config/Admin.php');
        $admin_settings = &new Blast_Config_Admin( $this->dbcon, PHPLIST_CONFIG_ADMIN_ID );
        $phplist_admin_data = array( );
        foreach( $admin_settings->getData( ) as $phplist_key => $value ){
            $local_key = 'phplist_admin_' . $phplist_key;
            if ( array_search( $local_key, $this->_keys_phplist_admin_setup ) === FALSE ) continue;
            $phplist_admin_data[$local_key] = $value;
        }
        $this->mergeData( $phplist_admin_data );

    }

    function _updatePunbbConfig( ){
       require_once( 'Modules/Forum/Config.php');
       $punbb_config_data = array_combine_key( $this->_keys_translation_punbb, $this->getData( ));
        if ( empty( $punbb_config_data )) return false;
       foreach( $punbb_config_data as $local_key  => $value ){
           $punbb_key = array_search( $local_key, $this->_keys_translation_punbb );
           $punbb_value = 
               isset( $this->_value_suffix_punbb[ $punbb_key ] ) ? 
                   $value . $this->_value_suffix_punbb[ $punbb_key ] : 
                   $value ;

           $config_setting = &new Forum_Config( $this->dbcon, $punbb_key );
           $current_value = $config_setting->getValue();
           if ( $current_value == $punbb_value ) continue;

           $config_setting->setValue( $punbb_value );
           $config_setting->save( );
        }

    }

    function _readPunbbConfig( ){
        //do nothing
    }

    function getTemplateId( ){
        return $this->getData( 'template');
    }

    function getTemplateIdFrontpage( ){
        return $this->getData( 'indextemplate');
    }

    function setTemplateIdFrontpage( $template_id ){
        return $this->mergeData( array( 'indextemplate' => $template_id ));
    }

    function setImageWidths( $sizes ){
        $legacy_keys = array( 'thumb' => 'thumb', 'tall' => 'optl', 'wide' => 'optw' );
        $legacy_size_values = array( );
        foreach( $sizes as $size_key => $value ){
            if ( !isset( $legacy_keys[$size_key])) continue ;
            $legacy_size_values[ $legacy_keys[ $size_key ]]  = $value ;
        }
        if ( empty( $legacy_size_values )) return false;
        return $this->mergeData( $legacy_size_values );
    }
}

?>
