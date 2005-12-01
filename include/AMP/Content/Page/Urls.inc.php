<?php

define ('AMP_CONTENT_URL_SEARCH',       'search.php');
define ('AMP_CONTENT_URL_ARTICLE',      'article.php');
define ('AMP_CONTENT_URL_GALLERY',      'gallery.php');
define ('AMP_CONTENT_URL_RSSFEED',      'rssfeed.php');
define ('AMP_CONTENT_URL_LIST_SECTION', 'article.php?list=type');
define ('AMP_CONTENT_URL_LIST_CLASS',   'article.php?list=class');
if (!defined( 'AMP_CONTENT_URL_FRONTPAGE' ))define ('AMP_CONTENT_URL_FRONTPAGE',    'index.php');
if (!defined( 'AMP_CONTENT_URL_INDEX' ))    define ('AMP_CONTENT_URL_INDEX',        'index.php');
define ('AMP_CONTENT_URL_FORM',         'modinput4.php');
define ('AMP_CONTENT_URL_FORM_DISPLAY', 'userdata_display.php');
define ('AMP_CONTENT_URL_GROUPS',       'groups.php');
define ('AMP_CONTENT_URL_TRACKBACKS',       AMP_SITE_URL . 'article_trackback.php');
define ('AMP_CONTENT_URL_DOCUMENTS', '/downloads/' );
if (!defined( 'AMP_CONTENT_URL_IMAGES' )) define ('AMP_CONTENT_URL_IMAGES', 'img/' );

?>
