<?php

/*
** DGS Search
** db.php written by James Sella and Max Spaulding
** Copyright (c) 2000-2001 Digital Genesis Software, LLC. All Rights Reserved.
** Released under the GPL Version 2 License.
** http://www.digitalgenesis.com
*/

function db($retVal, $q, $r, $o, $s, $timer) {
	global $config, $database;

	$matchCount = count($retVal);
	$maxSearchTime = $config['maxSearchTime'];
	$debug = $config['debug'];

	if ($debug)
		printf("->Debug: db() [Module]<BR>\n");

	if ($s > 0 && $matchCount >= $r + $o) //If we know the size already and have enough matches, return.
		return $retVal;

	$tm = getTime();

	reset($database);
	while (list(, $db) = each($database)) {
		if (getTime() - $timer > $maxSearchTime) {
			if ($debug)
				printf("->Debug: db() - <B>Exceeded Max Search Time of %.1f secs. Currently %.1f secs. - Exiting db module early.</B><BR>\n", $maxSearchTime, getTime() - $timer);
			break;
		}

		switch ($db['type']) {
			case 'mysql':
				$query = searchMySQL($db, $q, $r, $o, $s, $matchCount); 
				break;
			case 'pgsql':
				$query = searchPgSQL($db, $q, $r, $o, $s, $matchCount); 
				break;
			case 'mssql':
				$query = searchMSSQL($db, $q, $r, $o, $s, $matchCount);
				break; 
			case 'odbc':
				$query = searchODBC($db, $q, $r, $o, $s, $matchCount);
				break;
			case 'ibase':
				$query = searchIBase($db, $q, $r, $o, $s, $matchCount);
				break;
			default:
				printf("Error: %s is not a supported database type.<BR>\n", $db['type']);
				return $retVal;
		}

		reset($query);
		while (list(, $match) = each($query)) {
			/* Do URL substitutions where needed. */

			$matchCount++;
			if ($matchCount < $o || $matchCount > $r + $o) { //Only build the desc if we need them.
				$retVal[] = array(0); //Place holder, since this is outside of the displayed results.
			} else {
				$link = $db['link'];
				$url = $db['url'];
				$descArray = $db['desc'];
				$descWidth = $db['descWidth'];
				$rfields = $db['returnField'];
				$i = 0;
				if (!is_array($descArray))
					$descArray = array();
				reset($rfields);
				while (list(, $field) = each($rfields)) {
					$matchField = trim($match[$field]);
					$var = sprintf('@%d@', $i);
					$link = str_replace($var, $matchField, $link);
					$url = str_replace($var, $matchField, $url);
					reset($descArray);
					while (list($idx, $desc) = each($descArray)) {
						$descArray[$idx] = str_replace($var, $matchField, $desc);
					}
					$i++;
				}
				if ($descWidth > 0) {
					reset($descArray);
					while (list($idx, $desc) = each($descArray)) {
						$desc = processWindow($desc, $descWidth, 1, $q, $matchCount);
						$descArray[$idx] = $desc[0]; /* processWindow() sets the index to 0 for the first item. */
					}
				}

				$retVal[] = array('link' => $link, 'url' => $url, 'description' => $descArray, 'source' => 'db');
			}

			if ($s > 0 && $matchCount > $r + $o) //If we know the size already and have enough matches, break.
				break;
		}
	}  

	if ($debug) { //Time taken to process db
		printf("->Debug: db() - Module Search Time: %.1f sec<BR>\n", getTime() - $tm);
		flush();
	}

	return $retVal;
}

function searchMySQL($db, $q, $r, $o, $s, $c) {
	global $config;

	$server = $db['server'];
	$port = $db['port'];
	$username = $db['username'];
	$password = $db['password'];
	$database = $db['database'];
	$tables = $db['table'];
	$tableAssoc = $db['tableAssoc'];
	$rfields = $db['returnField'];
	$sfields = $db['searchField'];
	$wildcard = strtolower($db['wildcard']);
	$persistent = $db['persistent'];
	$orderByDepth = $db['orderByDepth'];
	$forceLower = $db['forceLower'];
	$debug = $config['debug'];
	$retVal = array();

	if ($debug)
		printf("-->Debug: searchMySQL()<BR>\n");

	if (!function_exists('mysql_connect')) {
		printf("MySQL support is not compiled into PHP.<BR>\n"); 
		return $retVal;
	}

	if (strlen($server) < 1)
		$server = 'localhost';

	if ($port > 0)
		$server = sprintf("%s:%d", trim($server), $port);

	if ($debug)
		printf("-->Debug: searchMySQL() - server: '%s', username: '%s', password: '%s'<BR>\n", $server, $username, $password);

	if ($persistent)
		$con = @mysql_pconnect($server, $username, $password);
	else
		$con = @mysql_connect($server, $username, $password);

	if (!$con) {
		printf("Error: Connection to MySQL server '%s' failed.<BR>\n", $server);
		return $retVal;
	}

	if (!@mysql_select_db($database, $con)) {
		printf("Error: Connection to MySQL database '%s' failed.<BR>\n>%s: %s<BR>\n", $database, @mysql_errno($con), @mysql_error($con));
		return $retVal;
	}

	$statement = 'SELECT ';
	$orderBy = ' ORDER BY ';

	if ($s > 0 && $r > 0) {
		$limit = ' LIMIT ';
		if ($o > 0)
			$limit .= sprintf('%d,%d', $o, $r);
		else
			$limit .= $r - ($c % $r);
	}

	$i = 0;
	reset($rfields);
	while (list(, $entry) = each($rfields)) {
		$statement .= $entry;
		if ($i < $orderByDepth || $orderByDepth < 0)
			$orderBy .= $entry;
		$i++;
		if ($i < count($rfields)) {
			$statement .= ', ';
			if ($i < $orderByDepth || $orderByDepth < 0)
				$orderBy .= ', ';
		}
	}

	$statement .= ' FROM ';

	$i = 0;
	reset($tables);
	while (list(, $entry) = each($tables)) {
		$i++;
		$statement .= $entry;
		if ($i < count($tables))
			$statement .= ', ';
	}

	$statement .= ' WHERE ';
	
	if (strlen($tableAssoc) > 0)
		$statement .= $tableAssoc . ' AND (';

	$i = 0;
	reset($sfields);
	while (list(, $entry) = each($sfields)) {
		$i++;
		if ($forceLower) {
			$statement .= 'LOWER(' . $entry . ')';
			$q = strtolower($q);
		} else {
			$statement .= $entry;
		}
		if (!strcmp($wildcard, 'none')) {
			$statement .= ' = \'';
		} else {
			$statement .= ' LIKE \'';
		}
		if (!strcmp($wildcard, 'left') || (strcmp($wildcard, 'right') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= $q;
		if (!strcmp($wildcard, 'right') || (strcmp($wildcard, 'left') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= '\'';
		if ($i < count($sfields))
			$statement .= ' OR ';
	}

	if (strlen($tableAssoc) > 0)
		$statement .= ') ';

	if ($orderByDepth != 0)
		$statement .= $orderBy;

	if ($limit)
		$statement .= $limit;

	if ($debug) {
		printf("-->Debug: searchMySQL() - MySQL statement: %s<BR>\n", $statement);
		flush();
	}

	$query = @mysql_query($statement, $con);
	if (!$query && $forceLower) {
		if ($debug) {
			printf("-->Debug: searchMySQL() - Statement failed using LOWER(), now attempting with LCASE().<BR>\n-->Debug: searchMySQL() - %s: %s<BR>\n", mysql_errno($con), @mysql_error($con)); 
		}
		str_replace('LOWER', 'LCASE', $statement);
		$query = @mysql_query($statement, $con);
	}
	if (!$query) {
		printf("Error: MySQL query '%s' failed.<BR>\n>%s: %s<BR>\n", $statement, @mysql_errno($con), @mysql_error($con));
		return $retVal;
	}

	$width = count($rfields);
	for ($row = 0; $row < $o - ($c % $r); $row++) /* Pad out results. We need a real fix. */
		$retVal[$row][0] = 0;
	while ($line = mysql_fetch_row($query)) {
		for($i = 0; $i < $width; $i++) {
			$retVal[$row][$rfields[$i]] = $line[$i];
		}
		$row++;
	}

	if (!$db['persistent'])
		mysql_close($con);

	return $retVal;
}

function searchPgSQL($db, $q, $r, $o, $s, $c) {
	/* NOTE: This function is untested as of 01-31-01. */
	global $config;

	$server = $db['server'];
	$port = $db['port'];
	$username = $db['username'];
	$password = $db['password'];
	$database = $db['database'];
	$tables = $db['table'];
	$tableAssoc = $db['tableAssoc'];
	$rfields = $db['returnField'];
	$sfields = $db['searchField'];
	$wildcard = strtolower($db['wildcard']);
	$orderByDepth = $db['orderByDepth'];
	$forceLower = $db['forceLower'];
	$debug = $config['debug'];

	settype($retVal, 'array');

	if (!function_exists('pg_connect')) {
		printf('PostgeSQL support is not compiled into PHP.<BR>\n'); 
		return $retVal;
	}

	if (strlen($server) < 1)
		$sqlUrl = 'host=localhost';
	else
		$sqlUrl = sprintf("host='%s'", trim($server));

	if ($port > 0)
		$sqlUrl = sprintf("%s port='%d'", $sqlUrl, $port);

	if (strlen($database) > 0)
		$sqlUrl = sprintf("%s dbname='%s'", $sqlUrl, trim($database));

	if (strlen($username) > 0)
		$sqlUrl = sprintf("%s user='%s'", $sqlUrl, trim($username));

	if (strlen($password) > 0)
		$sqlUrl = sprintf("%s password='%s'", $sqlUrl, trim($password));

	if ($debug)
		printf("-->Debug: searchPgSQL() - sqlUrl: '%s'<BR>\n", $sqlUrl);

	if ($db['persistent'])
		$con = @pg_pconnect($sqlUrl);
	else
		$con = @pg_connect($sqlUrl);

	if (!$con) {
		printf("Error: Connection to PostgreSQL server '%s' failed.<BR>\n", $server);
		return $retVal;
	}

	$statement = 'SELECT ';
	$orderBy = ' ORDER BY ';

	$i = 0;
	reset($rfields);
	while (list(, $entry) = each($rfields)) {
		$statement .= $entry;
		if ($i < $orderByDepth || $orderByDepth < 0)
			$orderBy .= $entry;
		$i++;
		if ($i < count($rfields)) {
			$statement .= ', ';
			if ($i < $orderByDepth || $orderByDepth < 0)
				$orderBy .= ', ';
		}
	}

	$statement .= ' FROM ';

	$i = 0;
	reset($tables);
	while (list(, $entry) = each($tables)) {
		$i++;
		$statement .= $entry;
		if ($i < count($tables))
			$statement .= ', ';
	}

	$statement .= ' WHERE ';
	
	if (strlen($tableAssoc) > 0)
		$statement .= $tableAssoc . ' AND ';

	$i = 0;
	reset($sfields);
	while (list(, $entry) = each($sfields)) {
		$i++;
		if ($forceLower) {
			$statement .= 'LOWER(' . $entry . ')';
		} else {
			$statement .= $entry;
		}
		if (!strcmp($wildcard, 'none')) {
			$statement .= ' = \'';
		} else {
			$statement .= ' LIKE \'';
		}
		if (!strcmp($wildcard, 'left') || (strcmp($wildcard, 'right') && strcmp($wildcard, 'none')))
			 $statement .= '%';
		$statement .= $q;
		if (!strcmp($wildcard, 'right') || (strcmp($wildcard, 'left') && strcmp($wildcard, 'none')))
			 $statement .= '%';
		$statement .= '\'';
		if ($i < count($sfields))
			$statement .= ' OR ';
	}

	if ($orderByDepth != 0)
		$statement .= $orderBy;

	if ($debug) {
		printf("PostgreSQL statement: %s<BR>\n", $statement);
		flush();
	}

	$query = @pg_exec($con, $statement);
	if (!$query && $forceLower) { /* Does PgSQL use LOWER() or LCASE()? Which should we use here? */
		if ($debug)
			 printf("Statement failed using LOWER(), now attempting with LCASE().<BR>\n"); 
		str_replace('LOWER', 'LCASE', $statement);
		$query = @pg_exec($con, $statement);
	}
	if (!$query) {
		printf("Error: PostgreSQL query '%s' failed.<BR>\n", $statement);
		return $retVal;
	}

	$width = count($rfields);
	$row = 0;
	$numrows = pg_numrows($query);
	while ($numrows) {
		$line = pg_fetch_row($query, $row);
		for($i = 0; $i < $width; $i++) {
			$retVal[$row][$rfields[$i]] = $line[$i];
		}
		$row++;
		$numrows--;
	}

	if (!$db['persistent'])
		pg_close($con);

	return $retVal;
}

function searchIBase($db, $q, $r, $o, $s, $c) {
	/* NOTE: This function is untested as of 01-31-01. */
	global $config;

	$server = $db['server'];
	$port = $db['port'];
	$username = $db['username'];
	$password = $db['password'];
	$database = $db['database'];
	$tables = $db['table'];
	$tableAssoc = $db['tableAssoc'];
	$rfields = $db['returnField'];
	$sfields = $db['searchField'];
	$wildcard = strtolower($db['wildcard']);
	$persistent = $db['persistent'];
	$orderByDepth = $db['orderByDepth'];
	$forceLower = $db['forceLower'];
	$debug = $config['debug'];
	$retVal = array();

	if ($debug)
		printf("-->Debug: searchIBase()<BR>\n");

	if (!function_exists('ibase_connect')) {
		printf("InterBase support is not compiled into PHP.<BR>\n"); 
		return $retVal;
	}

	if ($port > 0) { //Someone that has InterBase could add this.
		if ($debug)
			printf("-->Debug: searchIBase() - Port setting was ignored. Not implemented yet..<BR>\n");
	}

	$server = (isset($server) && ereg('[^:@/]$', $server)) ? $server . ':' : $server;
	$hostDatabase = $server . $database;

	if ($debug)
		printf("-->Debug: searchIBase() - hostDatabase: %s, username: %s, password: %s<BR>\n", $hostDatabase, $username, $password);

	if ($persistent)
		$con = @ibase_pconnect($hostDatabase, $username, $password);
	else
		$con = @ibase_connect($hostDatabase, $username, $password);

	if (!$con) {
		printf("Error: Connection to InterBase database '%s' failed.<BR>\n", $hostDatabase);
		return $retVal;
	}

	$statement = 'SELECT ';
	$orderBy = ' ORDER BY ';

	$i = 0;
	reset($rfields);
	while (list(, $entry) = each($rfields)) {
		$statement .= $entry;
		if ($i < $orderByDepth || $orderByDepth < 0)
			$orderBy .= $entry;
		$i++;
		if ($i < count($rfields)) {
			$statement .= ', ';
			if ($i < $orderByDepth || $orderByDepth < 0)
				$orderBy .= ', ';
		}
	}

	$statement .= ' FROM ';

	$i = 0;
	reset($tables);
	while (list(, $entry) = each($tables)) {
		$i++;
		$statement .= $entry;
		if ($i < count($tables))
			$statement .= ', ';
	}

	$statement .= ' WHERE ';
	
	if (strlen($tableAssoc) > 0)
		$statement .= $tableAssoc . ' AND (';

	$i = 0;
	reset($sfields);
	while (list(, $entry) = each($sfields)) {
		$i++;
		if ($forceLower) {
			$statement .= 'LOWER(' . $entry . ')';
			$q = strtolower($q);
		} else {
			$statement .= $entry;
		}
		if (!strcmp($wildcard, 'none')) {
			$statement .= ' = \'';
		} else {
			$statement .= ' LIKE \'';
		}
		if (!strcmp($wildcard, 'left') || (strcmp($wildcard, 'right') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= $q;
		if (!strcmp($wildcard, 'right') || (strcmp($wildcard, 'left') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= '\'';
		if ($i < count($sfields))
			$statement .= ' OR ';
	}

	if (strlen($tableAssoc) > 0)
		$statement .= ') ';

	if ($orderByDepth != 0)
		$statement .= $orderBy;

	if ($debug) {
		printf("-->Debug: searchIBase() - InterBase statement: %s<BR>\n", $statement);
		flush();
	}

	$query = @ibase_query($con, $statement);
	if (!$query && $forceLower) {
		if ($debug) {
			printf("-->Debug: searchIBase() - Statement failed using LOWER(), now attempting with LCASE().<BR>\n-->Debug: searchIBase() - %s<BR>\n", ibase_errmsg()); 
		}
		str_replace('LOWER', 'LCASE', $statement);
		$query = @ibase_query($con, $statement);
	}
	if (!$query) {
		printf("Error: InterBase query '%s' failed.<BR>\n>%s<BR>\n", $statement, ibase_errmsg());
		return $retVal;
	}

	$width = count($rfields);
	$row = 0;
	while ($line = ibase_fetch_row($query)) {
		for($i = 0; $i < $width; $i++) {
			$retVal[$row][$rfields[$i]] = $line[$i];
		}
		$row++;
	}

	if (!$persistent)
		ibase_close($con);

	return $retVal;
}

function searchMSSQL($db, $q, $r, $o, $s, $c) {
	global $config;

	$server = $db['server'];
	$port = $db['port'];
	$username = $db['username'];
	$password = $db['password'];
	$database = $db['database'];
	$tables = $db['table'];
	$tableAssoc = $db['tableAssoc'];
	$rfields = $db['returnField'];
	$sfields = $db['searchField'];
	$wildcard = strtolower($db['wildcard']);
	$persistent = $db['persistent'];
	$orderByDepth = $db['orderByDepth'];
	$forceLower = $db['forceLower'];
	$debug = $config['debug'];
	$retVal = array();

	if ($debug)
		printf("-->Debug: searchMSSQL()<BR>\n");

	if (!function_exists('mssql_connect')) {
		printf("MSSQL support is not compiled into PHP.<BR>\n"); 
		return $retVal;
	}

	if (strlen($server) < 1)
		$server = 'localhost';

	if ($port > 0)
		$server = sprintf("%s:%d", trim($server), $port);

	if ($debug)
		printf("-->Debug: searchMSSQL() - server: '%s', username: '%s', password: '%s'<BR>\n", $server, $username, $password);

	if ($persistent)
		$con = @mssql_pconnect($server, $username, $password);
	else
		$con = @mssql_connect($server, $username, $password);

	if (!$con) {
		printf("Error: Connection to MSSQL server '%s' failed.<BR>\n", $server);
		return $retVal;
	}

	if (!@mssql_select_db($database, $con)) {
		printf("Error: Connection to MSSQL database '%s' failed.<BR>\n>%s<BR>\n", $database, mssql_get_last_message());
		return $retVal;
	}

	$statement = 'SELECT ';
	$orderBy = ' ORDER BY ';

	if ($s > 0 && $r > 0)
		$statement .= sprintf('TOP %d ', ($o + $r) - $c);

	$i = 0;
	reset($rfields);
	while (list(, $entry) = each($rfields)) {
		$statement .= $entry;
		if ($i < $orderByDepth || $orderByDepth < 0)
			$orderBy .= $entry;
		$i++;
		if ($i < count($rfields)) {
			$statement .= ', ';
			if ($i < $orderByDepth || $orderByDepth < 0)
				$orderBy .= ', ';
		}
	}

	$statement .= ' FROM ';

	$i = 0;
	reset($tables);
	while (list(, $entry) = each($tables)) {
		$i++;
		$statement .= $entry;
		if ($i < count($tables))
			$statement .= ', ';
	}

	$statement .= ' WHERE ';
	
	if (strlen($tableAssoc) > 0)
		$statement .= $tableAssoc . ' AND (';

	$i = 0;
	reset($sfields);
	while (list(, $entry) = each($sfields)) {
		$i++;
		if ($forceLower) {
			$statement .= 'LOWER(' . $entry . ')';
			$q = strtolower($q);
		} else {
			$statement .= $entry;
		}
		if (!strcmp($wildcard, 'none')) {
			$statement .= ' = \'';
		} else {
			$statement .= ' LIKE \'';
		}
		if (!strcmp($wildcard, 'left') || (strcmp($wildcard, 'right') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= $q;
		if (!strcmp($wildcard, 'right') || (strcmp($wildcard, 'left') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= '\'';
		if ($i < count($sfields))
			$statement .= ' OR ';
	}

	if (strlen($tableAssoc) > 0)
		$statement .= ') ';

	if ($orderByDepth != 0)
		$statement .= $orderBy;

	if ($debug) {
		printf("-->Debug: searchMSSQL() - MSSQL statement: %s<BR>\n", $statement);
		flush();
	}

	$query = @mssql_query($statement, $con);
	if (!$query && $forceLower) {
		if ($debug) {
			printf("-->Debug: searchMSSQL() - Statement failed using LOWER(), now attempting with LCASE().<BR>\n-->Debug: searchMSSQL() - %s<BR>\n", mssql_get_last_message()); 
		}
		str_replace('LOWER', 'LCASE', $statement);
		$query = @mssql_query($statement, $con);
	}
	if (!$query) {
		printf("Error: MSSQL query '%s' failed.<BR>\n>%s<BR>\n", $statement, mssql_get_last_message());
		return $retVal;
	}

	$width = count($rfields);
	$row = 0;
	while ($line = mssql_fetch_row($query)) {
		for($i = 0; $i < $width; $i++) {
			$retVal[$row][$rfields[$i]] = $line[$i];
		}
		$row++;
	}

	if (!$persistent)
		mssql_close($con);

	return $retVal;
}

function searchODBC($db, $q, $r, $o, $s, $c) {
	/* NOTE: searchODBC is missing LIMIT/TOP support since we need to keep */
	/*		 this generic. As a result it will be slower than the others. */
	global $config;

	$username = $db['username'];
	$password = $db['password'];
	$database = $db['database'];
	$tables = $db['table'];
	$tableAssoc = $db['tableAssoc'];
	$rfields = $db['returnField'];
	$sfields = $db['searchField'];
	$wildcard = strtolower($db['wildcard']);
	$persistent = $db['persistent'];
	$orderByDepth = $db['orderByDepth'];
	$forceLower = $db['forceLower'];
	$debug = $config['debug'];
	$retVal = array();

	if ($debug)
		printf("-->Debug: searchODBC()<BR>\n");

	if (!function_exists('odbc_connect')) {
		printf("ODBC support is not compiled into PHP.<BR>\n"); 
		return $retVal;
	}

	if ($debug)
		printf("-->Debug: searchODBC() - database: '%s', username: '%s', password: '%s'<BR>\n", $database, $username, $password);

	if ($persistent)
		$con = @odbc_pconnect($database, $username, $password);
	else
		$con = @odbc_connect($database, $username, $password);

	if (!$con) {
		printf("Error: Connection to ODBC database '%s' failed.<BR>\n", $database);
		return $retVal;
	}

	$statement = 'SELECT ';
	$orderBy = ' ORDER BY ';

	$i = 0;
	reset($rfields);
	while (list(, $entry) = each($rfields)) {
		$statement .= $entry;
		if ($i < $orderByDepth || $orderByDepth < 0)
			$orderBy .= $entry;
		$i++;
		if ($i < count($rfields)) {
			$statement .= ', ';
			if ($i < $orderByDepth || $orderByDepth < 0)
				$orderBy .= ', ';
		}
	}

	$statement .= ' FROM ';

	$i = 0;
	reset($tables);
	while (list(, $entry) = each($tables)) {
		$i++;
		$statement .= $entry;
		if ($i < count($tables))
			$statement .= ', ';
	}

	$statement .= ' WHERE ';
	
	if (strlen($tableAssoc) > 0)
		$statement .= $tableAssoc . ' AND (';

	$i = 0;
	reset($sfields);
	while (list(, $entry) = each($sfields)) {
		$i++;
		if ($forceLower) {
			$statement .= 'LOWER(' . $entry . ')';
			$q = strtolower($q);
		} else {
			$statement .= $entry;
		}
		if (!strcmp($wildcard, 'none')) {
			$statement .= ' = \'';
		} else {
			$statement .= ' LIKE \'';
		}
		if (!strcmp($wildcard, 'left') || (strcmp($wildcard, 'right') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= $q;
		if (!strcmp($wildcard, 'right') || (strcmp($wildcard, 'left') && strcmp($wildcard, 'none')))
			$statement .= '%';
		$statement .= '\'';
		if ($i < count($sfields))
			$statement .= ' OR ';
	}

	if (strlen($tableAssoc) > 0)
		$statement .= ') ';

	if ($orderByDepth != 0)
		$statement .= $orderBy;

	if ($debug) {
		printf("-->Debug: searchODBC() - ODBC statement: %s<BR>\n", $statement);
		flush();
	}

	$query = @odbc_exec($con, $statement);
	if (!$query && $forceLower) {
		if ($debug) {
			printf("-->Debug: searchODBC() - Statement failed using LOWER(), now attempting with LCASE().<BR>\n"); 
		}
		str_replace('LOWER', 'LCASE', $statement);
		$query = @odbc_exec($con, $statement);
	}
	if (!$query) {
		printf("Error: ODBC query '%s' failed.<BR>\n", $statement);
		return $retVal;
	}

	$width = count($rfields);
	$row = 0;
	while (odbc_fetch_row($query)) {
		for($i = 0; $i < $width; $i++) {
			$retVal[$row][$rfields[$i]] = odbc_result($query, $rfields[$i]);
		}
		$row++;
	}

	if (!$persistent)
		odbc_close($con);

	return $retVal;
}

?>
