<?php

/*
** DGS Search
** config.php written by James M. Sella
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

/* Change options below as needed. */

   /* General Options */
$subdir =1 ;
// require("../adodb/adodb.inc.php");
 // require("../Connections/freedomrising.php");
$config['hideCredits'] =1;
   $config['installBase']       = $base_path."search/" ; /* Where the package was installed. */
   $config['searchModules']     = array("db");                          /* Modules to search for results. Available: fs and db. */
   $config['displayModules']    = array('title', 'query', 'stats', 'hr', 'nav', 'results', 'nav'); /* Modules to display results. */
   $config['language']          = 'english';                            /* Language pack to use. Set english, german, or spanish. */
   $config['fileSeparator']     = '/';                                  /* Would be '\\' for Win9x/NT/2000. */
   
  // $config['header']            = '';                          /* The page header. */
 //  $config['footer']            = '../footer.php';                         /* The page footer. */
   $config['target']            = '_self';                              /* The TARGET used for links on Result page. */
   $config['fonts']             = '';       /* Fonts to be used on the Display Results page. */
   $config['headerColor']       = '';                             /* The color of the header on the Display Results page. */
   $config['navColor']          = '';                             /* The color of the Navigation sections on the Display Results page. */
   $config['infoBarColor']      = '';                             /* The color of the Info Bar sections on the Display Results page. */
   $config['infoBar']           = true;                                 /* Display Info Bar for search results on Display Results page. */
   $config['infoBarFormat']     = '@URL@ @DASH-SIZE@ @DASH-LASTMOD@ @LASTMOD@'; /* Format of Info Bar on Display Results page. */
   $config['dateFormat']        = 'M j, Y H:i:s';                       /* Date format for Last Modified on Display Results. */
   $config['results']           = 100;                                   /* Default results per page. 0 is unlimited. */
   $config['boldQuery']         = true;                                 /* Bold the query string in description. */
   $config['timed']             = false;                                 /* Displays search time to user. */
   $config['maxSearchTime']     = 25;                                   /* The max amount of time to spend within the fs module. Set 0 to disable. */
   $config['translate']         = false;                                 /* Displays a 'Translate' link for each result. */
   $config['translateFrom']     = 'en_es';                              /* Sets the default translation to be preformed. See INSTALL. */
   $config['verifyConfig']      = false;                                 /* Verifies config file. Set false for a small speed increase. */
   $config['warn']              = true;                                 /* Displays warnings (ie: SAFE MODE warnings). */
   $config['debug']             = false;                                /* Displays a lot of slightly useful or annoying information. */
   $config['remoteDebug']       = false;                                /* Allows debugging to be enabled via a browser. See INSTALL. */

   /* Filesystem Options -- Search module 'fs' */

   $config['urlBase']           = $Web_url;                 /* The base URL for your site. */
   $config['siteBase']          = $base_path;           /* The directory that directly coresponds to 'urlBase'. */
   $config['fsBase']            = $base_path;           /* Where we should begin searching the filesystem. */
   $config['fsExclude']         = array('^\.ht', '^dgssearch$', 'adodb', 'old site', 'system', 'petition', 'email', 'poll', 'search', 'search', 'Connections', 'header.php', 'footer.php');        /* Files or directories in fsbase to exclude. Regex supported. */
   $config['cacheFile']         = '/tmp/dgssearch.cache';               /* Cache file for files to search. Speeds up searches. */
   $config['cacheTTL']          = 3600;                                 /* Cache file time to live (TTL) in second. Set 0 to disable. */
   $config['maxFileSize']       = 51200;                                /* The max file size to search in bytes. */
   $config['metaDesc']          = false;                                /* If true, prefer the META description over content descriptions. */
   $config['stripTags']         = true;                                 /* If HTML and PHP tags should be striped from fs searches. */
   $config['followLinks']       = true;                                 /* If SymLinks should be followed. Ignored on WinNT. */
   $config['frameSet']          = false;                                /* Handle auto-generated frameset layout schemes. See INSTALL. */
   $config['exts']              = array('s?html?', 'php3?', 'php?', 'txt');     /* The extentions to inlcude in search. Set '' for all. Regex supported. */
   $config['docExts']           = array('pdf', 'doc', 'ps');            /* Doc extensions to link to instead of HTML/txt files, if avail. See INSTALL.  */
   $config['descWidth']         = 80;                                   /* The width of the desc on the results page. */
   $config['descHeight']        = 2;                                    /* The number of desc lines on the results page. */
   $config['descEnd']           = '...';                                /* Added to beginning and end of descriptions. */

   /* Database Options -- Search module 'db' */

   /* NOTE: Module 'db' must be specified in $config['searchModules'] to enable database searches. See INSTALL for documentation. */

    $database[1]['type']         = 'mysql';                              /* Supports mysql, pgsql, mssql, ibase and odbc. See INSTALL for special instructions. */
   $database[1]['server']       = $MM_HOSTNAME;//'localhost';                          /* The SQL Server. (Ignored by ODBC). */
   $database[1]['port']         = 0;                                    /* Database port. Set 0 for default port. */
   $database[1]['username']     = $MM_USERNAME;//'';                           /* Username to connect to database. */
   $database[1]['password']     = $MM_PASSWORD;//';                           /* Password to connect to database. */
   $database[1]['database']     = $MM_DATABASE;//'shiftpower';                           /* The database or DSN you will be accessing. */
   $database[1]['persistent']   = true;                                 /* Use persistent database connections. */
   $database[1]['table']        = array('articles');                         /* The table in database to search. */
   $database[1]['tableAssoc']   = '';                                   /* If multiple tables listed, is used to join the tables. See INSTALL. */
   $database[1]['searchField']  = array('title', 'test', 'author', 'shortdesc', 'subtitile');             /* The fields to search. */
   $database[1]['returnField']  = array('id', 'title', 'test');       /* Fields returned from db. Can be used to sub into link, url and desc. */
   $database[1]['link']         = '@1@';                       /* The link used for results. */
   $database[1]['url']          = $Web_url.'article.php?id=@0@';/* The URL used for display the data from your database. */
   $database[1]['desc']         = array('@2@.');    /* The description to display. */
   $database[1]['descWidth']    = 80;                                   /* The width of the desc for this entry. Set to 0 to disable. */
   $database[1]['wildcard']     = 'both';                               /* Wildcard support: none, left, right or both */
   $database[1]['orderByDepth'] = 1;                                   /* OrderBy Depth. Default of -1 is all. See INSTALL. */
   $database[1]['forceLower']   = false;                                /* Forces a case-insensitive search by lowercasing everything. */

/* 
   $database[2]['type']         = 'mysql';                              /* Supports mysql, pgsql, mssql, ibase and odbc. See INSTALL for special instructions. 
    $database[2]['server']       = $MM_HOSTNAME;//'localhost';                     
   $database[2]['port']         = 0;                                  
   $database[2]['username']     = $MM_USERNAME;
   $database[2]['password']     = $MM_PASSWORD;
   $database[2]['database']     = $MM_DATABASE;//'shiftpower';                   
   $database[2]['persistent']   = true;                              
   $database[2]['table']        = array('faq');                       
   $database[2]['tableAssoc']   = '';                                 
   $database[2]['searchField']  = array('question', 'longanswer', 'shortanswer', 'firstname', 'lastname');           
   $database[2]['returnField']  = array('id', 'question', 'shortanswer');      
   $database[2]['link']         = '@1@';                    
   $database[2]['url']          = $Web_url.'faq.php?id=@0@';
   $database[2]['desc']         = array('@2@.');   
   $database[2]['descWidth']    = 80;                                 
   $database[2]['wildcard']     = 'both';                           
   $database[2]['orderByDepth'] = 1;                                 
   $database[2]['forceLower']   = false;                              
   */

   
/* Empty Database Template (For searching another table or database. You can add as many as you want.) */

/*
   $database[1]['type']         = '';
   $database[1]['server']       = '';
   $database[1]['port']         = 0;
   $database[1]['username']     = '';
   $database[1]['password']     = '';
   $database[1]['database']     = '';
   $database[1]['persistent']   = true;
   $database[1]['table']        = array('');
   $database[1]['tableAssoc']   = '';
   $database[1]['searchField']  = array('');
   $database[1]['returnField']  = array('');
   $database[1]['link']         = '';
   $database[1]['url']          = '';
   $database[1]['desc']         = array('');
   $database[1]['descWidth']    = 80;
   $database[1]['wildcard']     = 'both';
   $database[1]['orderByDepth'] = -1;
   $database[1]['forceLower']   = false;
*/

/* Constants - Usually no need to change options below.  */

   /* Generic */
   $config['program']           = 'Search the'.$SiteName ;
   $config['version']           = '';
   $config['maxResults']        = 65535;

   /* FindExt() - utils.php */
   $config['extSeparator']      = '.';
   $config['thisDir']           = '.';
   $config['parentDir']         = '..';

?>
