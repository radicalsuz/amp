<?php

define( 'AMP_TEXT_ERROR_IMAGE_NOT_ALLOWED', "Could not determine the type of image. JPG, GIF, and PNG format only"); 
define( 'AMP_TEXT_ERROR_FILE_EXISTS', "File already exists: %s" );
define( 'AMP_TEXT_ERROR_FILE_EXISTS_NOT', "File does not exist: %s" );
define( 'AMP_TEXT_ERROR_FILE_WRITE_FAILED', "Failed to write: %s" );
define( 'AMP_TEXT_ERROR_IMAGE_LIBRARY_NOT_FOUND', '%s %s failed: Your installation of PHP may not support this for images of type %s');

define( 'AMP_TEXT_ERROR_DATA_COPY_FAILURE_MULTIPLE_IDS', 'Multiple ID fields, cannot copy.');

define ('AMP_TEXT_ERROR_FAILED', '%s Failed' );
define ('AMP_TEXT_ERROR_NO_SELECTION', '%s not selected' );
define ('AMP_TEXT_ERROR_LOOKUP_SQL_FAILED', 'Failed to retrieve %s: %s' );
define( 'AMP_TEXT_ERROR_LOOKUP_NOT_FOUND', 'Lookup %s : not found');
define( 'AMP_TEXT_ERROR_METHOD_NOT_SUPPORTED', '%s does not support method %s attempted by %s');
define( 'AMP_TEXT_ERROR_NOT_DEFINED', '%s does not define %s');
define( 'AMP_TEXT_ERROR_CREATE_FAILED', '%s failed to create %s');
define( 'AMP_TEXT_ERROR_ACTION_NOT_ALLOWED', "You do not have permission to %s"); 
define( 'AMP_TEXT_ERROR_LOGIN_REQUIRED', "User should log in before trying to %s. URL: %s"); 
if ( !defined( 'AMP_TEXT_ERROR_FORM_REQUIRED_FIELD'))
        define( 'AMP_TEXT_ERROR_FORM_REQUIRED_FIELD', "This field is required." );

define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED_MULTIPLE_INSTANCES', 
            'Cannot update multiple instances of plugin %s : %s using interface');
define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED_NOT_REGISTERED',
            'Cannot update plugin %s : %s settings. Plugin is not registered');
define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_UPDATE_FAILED',
            'Cannot update plugin %s : %s settings.' );
define( 'AMP_TEXT_ERROR_USERDATA_PLUGIN_REGISTRATION_FAILED',
            'Cannot register plugin %s : %s' );

define( 'AMP_TEXT_ERROR_OPEN_FAILED', "Cannot open %s");
define( 'AMP_TEXT_ERROR_XML_READ_FAILED', 'XML Read failed for %s');

define( 'AMP_TEXT_ERROR_OPTION_FORMAT_INCORRECT', 'Value badly formatted, "%s" is missing "%s"' );


if ( !defined( 'AMP_TEXT_ERROR_TOOL_NOT_CONFIGURED'))
        define( 'AMP_TEXT_ERROR_TOOL_NOT_CONFIGURED', 'This page has not been configured for use');
if ( !defined( 'AMP_TEXT_ERROR_REQUIRED_FIELD_MISSING'))
        define( 'AMP_TEXT_ERROR_REQUIRED_FIELD_MISSING', "Field for %s, defined as '%s', does not exist");

if ( !defined( 'AMP_TEXT_ERROR_EMAIL_SENDER_NOT_SET'))
        define( 'AMP_TEXT_ERROR_EMAIL_SENDER_NOT_SET', 'No sender defined.  Email not sent.');
if ( !defined( 'AMP_TEXT_ERROR_EMAIL_MESSAGE_NOT_SET'))
        define( 'AMP_TEXT_ERROR_EMAIL_MESSAGE_NOT_SET', 'No message defined.  Email not sent.');
if ( !defined( 'AMP_TEXT_ERROR_EMAIL_TARGET_NOT_SET'))
        define( 'AMP_TEXT_ERROR_EMAIL_TARGET_NOT_SET', 'No recipient defined.  Email not sent.');

/**
 * UDM Form Errors 
 */
if ( !defined( 'AMP_TEXT_ERROR_FORM_DATA_INVALID'))
        define( 'AMP_TEXT_ERROR_FORM_DATA_INVALID', 'There was a problem with one or more fields. Please scroll down for more info.');
if ( !defined( 'AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED'))
        define( 'AMP_TEXT_ERROR_FORM_CAPTCHA_FAILED', 'The data you entered did not match the image or your time expired. Please try again');

/**
 * DIA errors 
 */
if ( !defined( 'AMP_TEXT_ERROR_DIA_SAVE_FAILURE'))
        define( 'AMP_TEXT_ERROR_DIA_SAVE_FAILURE', 'Save to DIA failed');
if ( !defined( 'AMP_TEXT_ERROR_DIA_READ_FAILURE'))
        define( 'AMP_TEXT_ERROR_DIA_READ_FAILURE', 'DIA read failed for table %s key %s');

/**
 * Data Item Errors
 */
if ( !defined( 'AMP_TEXT_ERROR_SORT_PROPERTY_FAILED' ))
        define( 'AMP_TEXT_ERROR_SORT_PROPERTY_FAILED', "sort by %s failed in %s: no access method found" ); 
if ( !defined( 'AMP_TEXT_ERROR_DATABASE_READ_FAILED' ))
        define( 'AMP_TEXT_ERROR_DATABASE_READ_FAILED', "%s failed to read the database : %s");
if ( !defined( 'AMP_TEXT_ERROR_DATABASE_SAVE_FAILED' ))
        define( 'AMP_TEXT_ERROR_DATABASE_SAVE_FAILED', "%s save failed : %s");
if ( !defined( 'AMP_TEXT_ERROR_DATABASE_CONNECTION_BAD' ))
        define( 'AMP_TEXT_ERROR_DATABASE_CONNECTION_BAD', '%s was initialized with an invalid dbcon');
if ( !defined( 'AMP_TEXT_ERROR_DATABASE_SQL_FAILED' ))
        define( 'AMP_TEXT_ERROR_DATABASE_SQL_FAILED', "%s failed to %s data : %s \n statement: %s");
if ( !defined( 'AMP_TEXT_ERROR_DATABASE_PROBLEM' ))
        define( 'AMP_TEXT_ERROR_DATABASE_PROBLEM', "Database Error");
if ( !defined( 'AMP_TEXT_ERROR_NO_CLASS_NAME_DEFINED' ))
        define( 'AMP_TEXT_ERROR_NO_CLASS_NAME_DEFINED', '_class_name var not defined for %s: search failed' );
if ( !defined( 'AMP_TEXT_ERROR_LOG_FORMAT' ))
        define( 'AMP_TEXT_ERROR_LOG_FORMAT', '%s in %s on line %s'."\n" );

/**
 * Cache Errors
 */
if ( !defined( 'AMP_TEXT_ERROR_CACHE_CONNECTION_FAILED'))
        define( 'AMP_TEXT_ERROR_CACHE_CONNECTION_FAILED', '%s cache method failed.' );
if ( !defined( 'AMP_TEXT_ERROR_CACHE_REQUEST_FAILED'))
        define( 'AMP_TEXT_ERROR_CACHE_REQUEST_FAILED', '%s cache method %s failed for item %s.' );
if ( !defined( 'AMP_TEXT_ERROR_CACHE_PATH_NOT_FOUND'))
        define( 'AMP_TEXT_ERROR_CACHE_PATH_NOT_FOUND', "Folder '%s' does not exist and could not be created for caching");
if ( !defined( 'AMP_TEXT_ERROR_CACHE_INVALID_KEY'))
        define( 'AMP_TEXT_ERROR_CACHE_INVALID_KEY', 'Invalid key type %s sent to cache');
?>
