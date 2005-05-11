<?php

/* AMP UTF-8 Upgrader. Requires MySQL 4.1+. That is all. */

/* Configuration for indexes that interfere with upgrades, and for tables that
** should be skipped. Modify as necessary. */

$indicies = array( 

    "articles" => array( 
        array( 'DROP INDEX article_ft_idx ON articles' ),
        array( 'CREATE FULLTEXT INDEX article_ft_idx ON ARTICLES( test, author, shortdesc, title )' )
    ),

    "categories_description" => array(
        array( 'DROP INDEX idx_categories_name ON categories_description' ),
        array( 'CREATE INDEX idx_categories_name ON categories_description ( categories_name )' )
    ),

    "countries" => array(
        array( 'DROP INDEX idx_countries_name ON countries' ),
        array( 'CREATE INDEX idx_countries_name ON countries ( countries_name )' )
    ),

    "email" => array(
        array( 'DROP INDEX email ON email' ),
        array( 'CREATE INDEX email ON email ( email )' )
    ),

    "languages" => array(
        array( 'DROP INDEX idx_languages_name ON languages' ),
        array( 'CREATE INDEX idx_languages_name ON languages ( name )' )
    ),

    "manufacturers" => array(
        array( 'DROP INDEX idx_manufacturers_name ON manufacturers' ),
        array( 'CREATE INDEX idx_manufacturers_name ON manufacturers ( manufacturers_name )' )
    ),

    "orders_status" => array(
        array( 'DROP INDEX idx_orders_status_name ON orders_status' ),
        array( 'CREATE INDEX idx_orders_status_name ON orders_status ( orders_status_name )' )
    ),

    "products_description" => array(
        array( 'DROP INDEX idx_products_name ON products_description' ),
        array( 'CREATE INDEX idx_products_name ON products_description ( products_name )' )
    ),

    "phplist_admin" => array(
        array( 'DROP INDEX loginname ON phplist_admin' ),
        array( 'CREATE INDEX loginname ON phplist_admin ( loginname )' )
    ),

);

$skip_tables = array( 'sessions', 'users_sessions', 'userdata_plugins_options',
                      'phplist_config', 'phplist_rssitem',
                      'phplist_rssitem_data', 'phplist_subscribepage_data',
                      'phplist_task', 'phplist_template', 'phplist_user_user' );

/* Don't modify beyond here, unless you intend to alter functionality. */

if (isset($_GET['table'])) {
	$tables = array( $_GET['table'] );
} else {
	$tables = $dbcon->MetaTables('TABLES');
}

foreach ( $tables as $table ) {

    print "Updating $table ... ";

    if (array_search( $table, $skip_tables )) {
        print "skipping.<br/>";
        continue;
    }

    $columns = $dbcon->MetaColumns( $table, false );
    $indexes = $dbcon->MetaIndexes( $table );

    $pre_sql = array();
    $post_sql = array();
    if ( isset($indicies[$table]) ) {
        $pre_sql = $indicies[$table][0];
        $post_sql = $indicies[$table][1];
    }

    foreach ( $pre_sql as $sql ) {
        $rs = $dbcon->Execute($sql);
        if (!$rs) print "Error dropping index: " . $dbcon->ErrorMsg();
    }

    foreach ( $columns as $column ) {

        if (!preg_match("/(text|char)/", $column->type)) continue;
        if (preg_match("/enum/", $column->type)) continue;

        $cName = $column->name;
        $cType = $column->type;
        $cLen  = $column->max_length;

        $cTypeLen = ($cLen && $cLen != '-1') ? "$cType($cLen)" : $cType;
        $bLen     = ($cLen && $cLen != '-1') ? "($cLen)" : '';

        $rs = $dbcon->Execute("ALTER TABLE $table CHANGE `$cName` `$cName` BLOB $bLen");
        $rs = $rs && $dbcon->Execute("ALTER TABLE $table CHANGE `$cName` `$cName` $cTypeLen CHARACTER SET utf8");

        if (!$rs) print "<strong>Error: " . $dbcon->ErrorMsg() . "</strong> ";
        
    }

    foreach ( $post_sql as $sql ) {
        $dbcon->Execute($sql);
    }

    $utf8_tbl_sql_1 = "ALTER TABLE $table CONVERT TO CHARACTER SET utf8 COLLATE utf8_general_ci";
    $utf8_tbl_sql_2 = "ALTER TABLE $table DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci";

    $rs = $dbcon->Execute($utf8_tbl_sql_1);
    $rs = $rs && $dbcon->Execute($utf8_tbl_sql_2);

    if (!$rs) print "<strong>Error: " . $dbcon->ErrorMsg() . "</strong> ";

    print "done.<br/>";
}

$rs = $dbcon->Execute( 'SELECT * FROM sysvar' );
$sysvars = $rs->FetchRow();

if (!isset($sysvars['encoding'])) {
    $encoding_db_sql = "ALTER TABLE sysvar ADD COLUMN encoding VARCHAR(12) DEFAULT 'utf-8'";
    $rs = $rs && $dbcon->Execute($encoding_db_sql);
}

$rs = $rs && $dbcon->Execute("UPDATE sysvar SET encoding='utf-8'");

$utf8_db_sql = "ALTER DATABASE " . AMP_DB_NAME . " DEFAULT CHARSET utf8";
$rs = $rs && $dbcon->Execute($utf8_db_sql);

if (!$rs) print "<strong>Error updating database defaults: " . $dbcon->ErrorMsg() . "</strong>";

?>
