<?php
#SHOWS CONTENT FROM AN RSS FEED

class RssFeeds {

	var $dbcon;
	var $feed;
	var $url;
	var $rss;
	var $items;
	var $num_items =10;
	var $title;
	var $decription;
	
	function RssFeeds($dbcon) {
		$this->dbcon = $dbcon;
	}
		
	function magpieContent() {
		define('MAGPIE_DIR', 'FeedOnFeeds/magpierss/');
		require_once(MAGPIE_DIR.'rss_fetch.inc');
	
		$error_level_tmp = error_reporting();
		error_reporting( E_ERROR );		
		$this->rss = fetch_rss( $this->url );
		//print_r ($this->rss->items);
		error_reporting( $error_level_tmp );		
		$this->items = array_slice($this->rss->items, 0, $this->num_items);
		if (!$this->title) $this->title = $this->rss->channel['title'];
		if (!$this->description) $this->description = $this->rss->channel['description'];
		
	}
	
	function content_display(){
		$this->magpieContent();
		$output .= '<p class = "title">'.$this->title.'</p>';
		$output .= '<p>'.$this->description.'</p><br>';

		foreach ($this->items as $item) {

			$output .= '<a href="'.$item['link'].'" class="listtitle">'.$item['title'].'</a><br>';
			if ($item['pubdate']) {
				$output .= '<span class="bodygreystrong">'.$item['pubdate'].'</span><br>';
			}	
			if ($item['description']) {
				$output .= '<span class="text">'.$item['description'].'</span><br>';
			}
			$output .= '<br>';
					
		}
		return $output;

	}
	
	function list_display(){
	
	
	}

	function load_feed(){
		$sql = 'select * from px_feeds where id = ' . $this->feed;
		$F = $this->dbcon->CacheExecute($sql) or DIE($this->dbcon->ErrorMsg());
		$this->url = $F->Fields("url");
		$this->title = $F->Fields("title");
		$this->description = $F->Fields("description");

	}

	function load_list(){
		$sql = 'select url from px_feeds where id = ' .$this->feed;
		$F = $this->dbcon->CacheExecute($sql) or DIE($this->dbcon->ErrorMsg());
		$this->url = $F->Fields("url");
		
	}


}

?>