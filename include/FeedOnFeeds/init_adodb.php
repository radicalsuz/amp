<?php

if (!defined('AMP_CONTENT_RSS_CUSTOMFORMAT')) define ('AMP_CONTENT_RSS_CUSTOMFORMAT', false);

if (!defined('AMP_CONTENT_RSS_FULLTEXT')) define ('AMP_CONTENT_RSS_FULLTEXT', false);

require_once("FeedOnFeeds/config.php");

define('MAGPIE_CACHE_AGE', 60*15);
define('MAGPIE_USER_AGENT', 'FeedOnFeeds/0.1.7 (+http://minutillo.com/steve/feedonfeeds/)');
define('MAGPIE_CACHE_DIR', FOF_CACHE_DIR);

// suppress magpie's warnings, we'll handle those ourselves
#error_reporting(E_NOTICE);

require_once('FeedOnFeeds/magpierss/rss_fetch.inc');
require_once('FeedOnFeeds/magpierss/rss_utils.inc');

$fof_connection = &AMP_Registry::getDbcon( );

if(!isset( $installing ) || !$installing) is_writable( FOF_CACHE_DIR ) or die("Cache directory is not writable or does not exist.  Have you run <a href=\"install.php\"><code>install.php</code></a>?");

$FOF_FEED_TABLE = FOF_FEED_TABLE;
$FOF_ITEM_TABLE = FOF_ITEM_TABLE;

$fof_rss_cache = new RSSCache( MAGPIE_CACHE_DIR );

function fof_get_feeds()
{

	$result = &fof_do_query("select id, url, title, link, description from ". FOF_FEED_TABLE." order by title");

	$i = 0;

	while($row = $result->FetchRow( ))
	{
		$id = $row['id'];
		$age = fof_rss_age($row['url']);

		$feeds[$i]['id'] = $id;
		$feeds[$i]['url'] = $row['url'];
		$feeds[$i]['title'] = $row['title'];
		$feeds[$i]['link'] = $row['link'];
		$feeds[$i]['description'] = $row['description'];
		$feeds[$i]['age'] = $age;


		if($age == -1)
		{
			$agestr = "never";
			$agestrabbr = "&infin;";
		}
		else
		{
			$seconds = $age % 60;
			$minutes = $age / 60 % 60;
			$hours = $age / 60 / 60 % 24;
			$days = floor($age / 60 / 60 / 24);

			if($seconds)
			{
				$agestr = "$seconds second";
				if($seconds != 1) $agestr .= "s";
				$agestr .= " ago";

				$agestrabbr = $seconds . "s";
			}

			if($minutes)
			{
				$agestr = "$minutes minute";
				if($minutes != 1) $agestr .= "s";
				$agestr .= " ago";

				$agestrabbr = $minutes . "m";
			}

			if($hours)
			{
				$agestr = "$hours hour";
				if($hours != 1) $agestr .= "s";
				$agestr .= " ago";

				$agestrabbr = $hours . "h";
			}

			if($days)
			{
				$agestr = "$days day";
				if($days != 1) $agestr .= "s";
				$agestr .= " ago";

				$agestrabbr = $days . "d";
			}
		}

		$feeds[$i]['agestr'] = $agestr;
		$feeds[$i]['agestrabbr'] = $agestrabbr;

		$i++;

	}

	$result = fof_do_query("select count( feed_id ) as count, feed_id as id from ".FOF_FEED_TABLE.", ".FOF_ITEM_TABLE." where ".FOF_FEED_TABLE.".id = ".FOF_ITEM_TABLE.".feed_id and `read` is null group by feed_id order by ".FOF_FEED_TABLE.".title");

	while($row = $result->FetchRow( ))
	{
	  for($i=0; $i<count($feeds); $i++)
	  {
	  	if($feeds[$i]['id'] == $row['id'])
	  	{
	  		$feeds[$i]['unread'] = $row['count'];
	  	}
	  }
	}

	$result = fof_do_query("select count( feed_id ) as count, feed_id as id from ".FOF_FEED_TABLE.", ".FOF_ITEM_TABLE." where ".FOF_FEED_TABLE.".id = ".FOF_ITEM_TABLE.".feed_id group by feed_id order by ".FOF_FEED_TABLE.".title");

	while($row = $result->FetchRow())
	{
	  for($i=0; $i<count($feeds); $i++)
	  {
	  	if($feeds[$i]['id'] == $row['id'])
	  	{
	  		$feeds[$i]['items'] = $row['count'];
	  	}
	  }
	}


	return $feeds;
}

function fof_view_title($feed=NULL, $what="new", $when=NULL, $start=NULL, $limit=NULL)
{
	$title = "feed on feeds";

	if(!is_null($when) && $when != "")
	{
		$title .= ' - ' . $when ;
	}
	if(!is_null($feed) && $feed != "")
	{
		$r = fof_feed_row($feed);
		$title .=' - ' . htmlspecialchars($r['title']);
	}
	if(is_numeric($start))
	{
		if(!is_numeric($limit)) $limit = FOF_HOWMANY;
		$title .= " - items $start to " . ($start + $limit);
	}
	if($what != "all")
	{
		$title .=' - new items';
	}
	else
	{
		$title .= ' - all items';
	}

	return $title;
}

function fof_get_items($feed=NULL, $what="new", $when=NULL, $start=NULL, $limit=NULL)
{

	if(!is_null($when) && $when != "")
	{
	  if($when == "today")
	  {
		$whendate = date( "Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60) );
	  }
	  else
	  {
		$whendate = $when;
	  }

	  $begin = strtotime($whendate);
	  $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
	  $end = $begin + (24 * 60 * 60);

	  $tomorrow = date( "Y/m/d", $begin + (24 * 60 * 60) );
	  $yesterday = date( "Y/m/d", $begin - (24 * 60 * 60) );
	}

	if(is_numeric($start))
	{
		if(!is_numeric($limit))
		{
			$limit = FOF_HOWMANY;
		}

		$limit_clause = " limit $start, $limit ";
	}

	$query = "select ".FOF_ITEM_TABLE.".read as item_read, ".FOF_FEED_TABLE.".title as feed_title, ".FOF_FEED_TABLE.".link as feed_link, ".FOF_FEED_TABLE.".description as feed_description, ".FOF_ITEM_TABLE.".id as item_id, ".FOF_ITEM_TABLE.".link as item_link, ".FOF_ITEM_TABLE.".title as item_title, UNIX_TIMESTAMP(".FOF_ITEM_TABLE.".timestamp) as timestamp, ".FOF_ITEM_TABLE.".content as item_content, ".FOF_ITEM_TABLE.".dcdate as dcdate, ".FOF_ITEM_TABLE.".dccreator as dccreator, ".FOF_ITEM_TABLE.".dcsubject as dcsubject from ".FOF_FEED_TABLE.", ".FOF_ITEM_TABLE." where ".FOF_ITEM_TABLE.".feed_id=".FOF_FEED_TABLE.".id";

	if(!is_null($feed) && $feed != "")
	{
	  $query .= " and ".FOF_FEED_TABLE.".id = $feed";
	}

	if(!is_null($when) && $when != "")
	{
	  $query .= " and UNIX_TIMESTAMP(".FOF_ITEM_TABLE.".timestamp) > $begin and UNIX_TIMESTAMP(".FOF_ITEM_TABLE.".timestamp) < $end";
	}

	if($what != "all")
	{
	  $query .= " and ".FOF_ITEM_TABLE.".read is null";
	}

	$query .= " order by timestamp desc $limit_clause";

	return fof_do_query($query);
}

function fof_get_nav_links($feed=NULL, $what="new", $when=NULL, $start=NULL, $limit=NULL)
{
	$string = "";

	if(!is_null($when) && $when != "")
	{
	  if($when == "today")
	  {
		$whendate = date( "Y/m/d", time() - (FOF_TIME_OFFSET * 60 * 60) );
	  }
	  else
	  {
		$whendate = $when;
	  }

	  $begin = strtotime($whendate);
	  $begin = $begin + (FOF_TIME_OFFSET * 60 * 60);
	  $end = $begin + (24 * 60 * 60);

	  $tomorrow = date( "Y/m/d", $begin + (24 * 60 * 60) );
	  $yesterday = date( "Y/m/d", $begin - (24 * 60 * 60) );

		$string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$yesterday&amp;how=$how&amp;howmany=$howmany\">[&laquo; $yesterday]</a> ";
		if($when != "today") $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=today&amp;how=$how&amp;howmany=$howmany\">[today]</a> ";
		if($when != "today") $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$tomorrow&amp;how=$how&amp;howmany=$howmany\">[$tomorrow &raquo;]</a> ";
	}

	if(is_numeric($start))
	{
		if(!is_numeric($limit)) $limit = FOF_HOWMANY;

		$earlier = $start + $limit;
		$later = $start - $limit;

		$string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$earlier&amp;howmany=$limit\">[&laquo; previous $limit]</a> ";
		if($later >= 0) $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;howmany=$limit\">[current items]</a> ";
		if($later >= 0) $string .= "<a href=\"view.php?feed=$feed&amp;what=$what&amp;when=$when&amp;how=paged&amp;which=$later&amp;howmany=$limit\">[next $limit &raquo;]</a> ";
	}

	return $string;
}

function fof_do_query($sql, $live=0)
{
    $dbcon = AMP_Registry::getDbcon( );

	// echo "[$sql]<br>";
    if ( AMP_DISPLAYMODE_DEBUG ) AMP_DebugSQL( $sql, 'Feeds on Feeds');

	if($live)
	{
		return $dbcon->Execute( $sql );
	}
	else
	{
		$result = $dbcon->Execute( $sql );
		if(!$result) die("Cannot query database.  Have you run <a href=\"install.php\"><code>install.php</code></a>? MySQL says: <b>". $dbcon->ErrorMsg() . "</b>");
		return $result;
	}
}

function fof_rss_age($url)
{
	global $fof_rss_cache;

	$filename = $fof_rss_cache->file_name( $url );

	if ( file_exists( $filename ) )
	{
		// find how long ago the file was added to the cache
		// and whether that is longer then MAX_AGE
		$mtime = filemtime( $filename );
		$age = time() - $mtime;
		return $age;
	}
	else
	{
		return -1;
	}
}

function fof_getRSSLocation($html, $location){
    if(!$html or !$location){
        return false;
    }else{
        #search through the HTML, save all <link> tags
        # and store each link's attributes in an associative array
        preg_match_all('/<link\s+(.*?)\s*\/?>/si', $html, $matches);
        $links = $matches[1];
        $final_links = array();
        $link_count = count($links);
        for($n=0; $n<$link_count; $n++){
            $attributes = preg_split('/\s+/s', $links[$n]);
            foreach($attributes as $attribute){
                $att = preg_split('/\s*=\s*/s', $attribute, 2);
                if(isset($att[1])){
                    $att[1] = preg_replace('/([\'"]?)(.*)\1/', '$2', $att[1]);
                    $final_link[strtolower($att[0])] = $att[1];
                }
            }
            $final_links[$n] = $final_link;
        }
        #now figure out which one points to the RSS file
        for($n=0; $n<$link_count; $n++){
            if(strtolower($final_links[$n]['rel']) == 'alternate'){
                if(strtolower($final_links[$n]['type']) == 'application/rss+xml'){
                    $href = $final_links[$n]['href'];
                }
                if(!$href and strtolower($final_links[$n]['type']) == 'text/xml'){
                    #kludge to make the first version of this still work
                    $href = $final_links[$n]['href'];
                }
                if($href){
                    if(strstr($href, "http://") !== false){ #if it's absolute
                        $full_url = $href;
                    }else{ #otherwise, 'absolutize' it
                        $url_parts = parse_url($location);
                        #only made it work for http:// links. Any problem with this?
                        $full_url = "http://$url_parts[host]";
                        if(isset($url_parts['port'])){
                            $full_url .= ":$url_parts[port]";
                        }
                        if($href{0} != '/'){ #it's a relative link on the domain
                            $full_url .= dirname($url_parts['path']);
                            if(substr($full_url, -1) != '/'){
                                #if the last character isn't a '/', add it
                                $full_url .= '/';
                            }
                        }
                        $full_url .= $href;
                    }
                    return $full_url;
                }
            }
        }
        return false;
    }
}

function fof_render_feed_link($row)
{
	$link = htmlspecialchars($row['link']);
	$description = htmlspecialchars($row['description']);
	$title = htmlspecialchars($row['title']);
	$url = htmlspecialchars($row['url']);

	$s = "<b><a href=\"$link\" title=\"$description\">$title</a></b> ";
	$s .= "<a href=\"$url\">(rss)</a>";

	return $s;
}

function fof_opml_to_array($opml)
{
	$rx = "/xmlurl=\"(.*?)\"/mi";

	if (preg_match_all($rx, $opml, $m))
	{
		for($i = 0; $i < count($m[0]) ; $i++)
		{
			$r[] = $m[1][$i];
		}
  }

  return $r;
}

function fof_add_feed($url)
{
	if(!$url) return;

	$url = trim($url);

	if(substr($url, 0, 7) != 'http://')
	{
	  $url = 'http://' . $url;
	}

	print "Attempting to subscribe to <a href=\"$url\">$url</a>...<br>";

	if($row = fof_is_subscribed($url))
	{
		print "You are already subscribed to " . fof_render_feed_link($row) . "<br><br>";
		return true;
	}

	$rss = fetch_rss( $url );

	if(!$rss->channel && !$rss->items)
	{
		echo "&nbsp;&nbsp;<font color=\"darkgoldenrod\">URL is not RSS or is invalid.</font><br>";
		if(!$rss) echo "&nbsp;&nbsp;(error was: <B>" . magpie_error() . "</b>)<br>";
	  echo "&nbsp;&nbsp;<a href=\"http://feeds.archive.org/validator/check?url=$url\">The validator may give more information.</a><br>";

		echo "<br>Attempting autodiscovery...<br><br>";

		$r = _fetch_remote_file ($url);
		$c = $r->results;

		if($c && $r->status >= 200 && $r->status < 300)
		{
			$l = fof_getRSSLocation($c, $url);
			if($l)
			{
				echo "Autodiscovery found <a href=\"$l\">$l</a>.<br>";

				echo "Attempting to subscribe to <a href=\"$l\">$l</a>...<br>";

				if($row = fof_is_subscribed($l))
				{
					print "<br>You are already subscribed to " . fof_render_feed_link($row) . "<br>";
					return true;
				}

				$rss = fetch_rss( $l );

				if(!$rss->channel && !$rss->items)
				{
					echo "&nbsp;&nbsp;<font color=\"red\">URL is not RSS, giving up.</font><br>";
					echo "&nbsp;&nbsp;(error was: <B>" . magpie_error() . "</b>)<br>";
					echo "&nbsp;&nbsp;<a href=\"http://feeds.archive.org/validator/check?url=$l\">The validator may give more information.</a><br>";
                    return false;

				}
				else
				{
					fof_actually_add_feed($l, $rss);
					echo "&nbsp;&nbsp;<font color=\"green\"><b>Subscribed.</b></font><br><br>";
                    return true;
				}
			}
			else
			{
				echo "<font color=\"red\"><b>Autodiscovery failed.  Giving up.</b></font><br>";
                return false;
			}
		}
		else
		{
			echo "<font color=\"red\"><b>Can't load URL.  Giving up.</b></font><br>";
            return false;
		}
	}
	else
	{
		fof_actually_add_feed($url, $rss);
		echo "<font color=\"green\"><b>Subscribed.</b></font><br>";
        return true;
	}
}

function fof_actually_add_feed($url, $rss)
{
	$title = mysql_escape_string($rss->channel['title']);
	$link = mysql_escape_string($rss->channel['link']);
	$description = mysql_escape_string($rss->channel['description']);

	$sql = "insert into ".FOF_FEED_TABLE." (url,title,link,description) values ('$url','$title','$link','$description')";
	fof_do_query($sql);

	fof_update_feed($url, 0);
}

function fof_is_subscribed($url)
{

	$safe_url = mysql_escape_string($url);

	$result = fof_do_query("select url, title, link, id from ".FOF_FEED_TABLE." where url = '$safe_url'");

	if( $result->RecordCount() == 0)
	{
		return false;
	}
	else
	{
		$row = $result->FetchRow();
		return $row;
	}
}

function fof_feed_row($id)
{

	$result = fof_do_query("select url, title, link, id from ".FOF_FEED_TABLE." where id = '$id'");

	if($result->RecordCount( ) == 0)
	{
		return false;
	}
	else
	{
		$row = $result->FetchRow();
		return $row;
	}
}

function fof_update_feed($url)
{
	global $FOF_FEED_TABLE, $FOF_ITEM_TABLE;

	$FOF_FEED_TABLE = FOF_FEED_TABLE; 
    $FOF_ITEM_TABLE = FOF_ITEM_TABLE;

	if(!$url) return 0;

	$rss = fetch_rss( $url );

	if(!$rss)
	{
		print "Error: <B>" . magpie_error() . "</b> ";
		print "<a href=\"http://feeds.archive.org/validator/check?url=$url\">try to validate it?</a> ";
		return 0;
	}

	$title = mysql_escape_string($rss->channel['title']);
	$link = $rss->channel['link'];
	$description = mysql_escape_string($rss->channel['description']);

	$safeurl = mysql_escape_string( $url );
	$result = fof_do_query("select id, url from ".FOF_FEED_TABLE." where url='$safeurl'");

	$row = $result->FetchRow();
	$feed_id = $row['id'];

	$items = $rss->items;

	foreach ($items as $item)
	{
		if (AMP_CONTENT_RSS_CUSTOMFORMAT == 'true') {
			$link = mysql_escape_string($item['source']);
        	$contacts = mysql_escape_string($item['contacts']);
        	$subtitle = mysql_escape_string($item['subtitle']);
        	$custom1 = mysql_escape_string($item['media_text']);
        } else {
        	$link = mysql_escape_string($item['link']);
        }
        
		$title = mysql_escape_string($item['title']);
		$content = mysql_escape_string($item['description']);

		if($item['content']['encoded'])
		{
			$content = mysql_escape_string($item['content']['encoded']);
		}

		if($item['atom_content'])
		{
			$content = mysql_escape_string($item['atom_content']);
		}

        if (isset($item['dc'])) {
            $dcdate = strtotime(mysql_escape_string($item['dc']['date']));
            $dcdate=date("Y-m-d", $dcdate);
            $dccreator = mysql_escape_string($item['dc']['creator']);
            $dcsubject = mysql_escape_string($item['dc']['subject']);
        } else {
            $dcdate=strtotime(mysql_escape_string($item['pubdate']));
            $dcdate=date("Y-m-d", $dcdate);
            $dccreator = mysql_escape_string($item['author']);
            $dcsubject = mysql_escape_string($item['category']);
        }

		if(!$link)
		{
			$link = $item['guid'];
		}

		if(!$title)
		{
			$title = "[no title]";
		}

		$result = fof_do_query("select id from ".FOF_ITEM_TABLE." where feed_id='$feed_id' and link='$link'");
		$row = $result->FetchRow();
		$id = $row['id'];

		if($result->RecordCount( ) == 0)
		{
			$n++;

			if (AMP_CONTENT_RSS_CUSTOMFORMAT == 'true') {
				$sql = "insert into ".FOF_ITEM_TABLE." (feed_id,link,title,content,dcdate,dccreator,dcsubject,contacts,subtitle,custom1) values ('$feed_id','$link','$title','$content','$dcdate','$dccreator','$dcsubject','$contacts','$subtitle','$custom1')";
			} else {
						$sql = "insert into ".FOF_ITEM_TABLE." (feed_id,link,title,content,dcdate,dccreator,dcsubject) values ('$feed_id','$link','$title','$content','$dcdate','$dccreator','$dcsubject')";
			}

			$result = fof_do_query($sql);
		}
		else
		{
			$ids[] = $id;
		}

	}

	if(defined('FOF_KEEP_DAYS'))
	{
		$keep_days = FOF_KEEP_DAYS;

		if(count($ids) != 0)
		{
			$first = 1;

			foreach ($ids as $id)
			{
				if($first)
				{
					$stat = "($id";
					$first = 0;
				}
				else
				{
					$stat .= ", $id";
				}
			}

			$stat .= ")";

			$sql =  "delete from ".FOF_ITEM_TABLE." where feed_id = $feed_id and `read`=1 and id not in $stat and to_days( CURDATE(  )  )  - to_days( timestamp )  > $keep_days";


			fof_do_query($sql);
		}
	}

	return $n;
}

/*
 balanceTags

 Balances Tags of string using a modified stack.

 @param text      Text to be balanced
 @return          Returns balanced text
 @author          Leonard Lin (leonard@acm.org)
 @version         v1.1
 @date            November 4, 2001
 @license         GPL v2.0
 @notes
 @changelog
             1.2  ***TODO*** Make better - change loop condition to $text
             1.1  Fixed handling of append/stack pop order of end text
                  Added Cleaning Hooks
             1.0  First Version
*/

function fof_balanceTags($text) {

	$tagstack = array(); $stacksize = 0; $tagqueue = ''; $newtext = '';

	# WP bug fix for comments - in case you REALLY meant to type '< !--'
	$text = str_replace('< !--', '<    !--', $text);
	# WP bug fix for LOVE <3 (and other situations with '<' before a number)
	$text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

	while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
		$newtext = $newtext . $tagqueue;

		$i = strpos($text,$regex[0]);
		$l = strlen($tagqueue) + strlen($regex[0]);

		// clear the shifter
		$tagqueue = '';
		// Pop or Push
		if ($regex[1][0] == "/") { // End Tag
			$tag = strtolower(substr($regex[1],1));
			// if too many closing tags
			if($stacksize <= 0) {
				$tag = '';
				//or close to be safe $tag = '/' . $tag;
			}
			// if stacktop value = tag close value then pop
			else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
				$tag = '</' . $tag . '>'; // Close Tag
				// Pop
				array_pop ($tagstack);
				$stacksize--;
			} else { // closing tag not at top, search for it
				for ($j=$stacksize-1;$j>=0;$j--) {
					if ($tagstack[$j] == $tag) {
					// add tag to tagqueue
						for ($k=$stacksize-1;$k>=$j;$k--){
							$tagqueue .= '</' . array_pop ($tagstack) . '>';
							$stacksize--;
						}
						break;
					}
				}
				$tag = '';
			}
		} else { // Begin Tag
			$tag = strtolower($regex[1]);

			// Tag Cleaning

			// Push if not img or br or hr
			if($tag != 'br' && $tag != 'img' && $tag != 'hr') {
				$stacksize = array_push ($tagstack, $tag);
			}

			// Attributes
			// $attributes = $regex[2];
			$attributes = $regex[2];
			if($attributes) {
				$attributes = ' '.$attributes;
			}
			$tag = '<'.$tag.$attributes.'>';
		}
		$newtext .= substr($text,0,$i) . $tag;
		$text = substr($text,$i+$l);
	}

	// Clear Tag Queue
	$newtext = $newtext . $tagqueue;

	// Add Remaining text
	$newtext .= $text;

	// Empty Stack
	while($x = array_pop($tagstack)) {
		$newtext = $newtext . '</' . $x . '>'; // Add remaining tags to close
	}

	// WP fix for the bug with HTML comments
	$newtext = str_replace("< !--","<!--",$newtext);
	$newtext = str_replace("<    !--","< !--",$newtext);

	return $newtext;
}


?>
