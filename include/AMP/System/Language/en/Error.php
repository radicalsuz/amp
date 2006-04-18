<?php

define( 'AMP_TEXT_ERROR_IMAGE_NOT_ALLOWED', "Could not determine the type of image. JPG, GIF, and PNG format only"); 
define( 'AMP_TEXT_ERROR_FILE_EXISTS', "File already exists: %s" );
define( 'AMP_TEXT_ERROR_FILE_WRITE_FAILED', "Failed to write: %s" );
define( 'AMP_TEXT_ERROR_IMAGE_LIBRARY_NOT_FOUND', '%s %s failed: Your installation of PHP may not support this for images of type %s');

define( 'AMP_TEXT_ERROR_DATA_COPY_FAILURE_MULTIPLE_IDS', 'Multiple ID fields, cannot copy.');

define ('AMP_TEXT_ERROR_LOOKUP_SQL_FAILED', 'Failed to retrieve %s: %s' );
define( 'AMP_TEXT_ERROR_LOOKUP_NOT_FOUND', 'Lookup %s : not found');
define( 'AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED', '%s does not support method %s attempted by %s');

define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED_MULTIPLE_INSTANCES', 
            'Cannot update multiple instances of plugin %s : %s using interface');
define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED_NOT_REGISTERED',
            'Cannot update plugin %s : %s settings. Plugin is not registered');
define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED',
            'Cannot update plugin %s : %s settings.' );
define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_REGISTRATION_FAILED',
            'Cannot register plugin %s : %s' );


if ( !defined( 'AMP_TEXT_ERROR_TOOL_NOT_CONFIGURED'))
        define( 'AMP_TEXT_ERROR_TOOL_NOT_CONFIGURED', 'This page has not been configured for use');
if ( !defined( 'AMP_TEXT_ERROR_STATUS_FIELD_MISSING'))
        define( 'AMP_TEXT_ERROR_STATUS_FIELD_MISSING', "Status field for %s, defined as '%s', does not exist");
?>
