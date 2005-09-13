<?php

require_once('RSSWriter/rss10.inc');

class AMP_RSSWriter extends RSSWriter {

	var $_lastModified;

	function AMP_RSSWriter($uri, $title, $description, $meta=array()) {
		parent::RSSWriter($uri, $title, $description, $meta);
	}

	function items() {
		foreach ($this->items as $item) {
			print "  <item rdf:about=\"" .  htmlspecialchars($item["uri"]) . "\">\n";
			foreach ($item as $key => $value) {
				if ($key!="uri") {
					if (is_array($value)) {
						$keys = array_keys($value);
						if (strpos($keys[0], ':' ) !== false) {
							print "    <${key}>\n";
							foreach ($value as $k1 => $v1) {
								print "      <${k1}>" . htmlspecialchars($v1) . "</${k1}>\n";
							}
							print "    </${key}>\n";
						} else {
							foreach ($value as $v1) {
								print "    <${key}>" . htmlspecialchars($v1) . "</${key}>\n";
							}
						}
					} else {
						print "    <${key}>" . htmlspecialchars($value) . "</${key}>\n";
					}
				}
			}
			print "  </item>\n\n";
		}
	}

	function lastModified($timestamp = null) {
		if(isset($timestamp)) {
			$this->_lastModified = $timestamp;
		}
		return $this->_lastModified;
	}

    function execute() {

		if($this->httpConditionalGet($this->lastModified())) {
			$this->serialize();
		}
    }


    function httpConditionalGet($timestamp) {
        header('Last-Modified: '.($last_modified = gmdate('r', $timestamp)));
        header('ETag: "'.$timestamp.'"');
        $client_etag = isset($_SERVER['HTTP_IF_NONE_MATCH'])
                        ? $_SERVER['HTTP_IF_NONE_MATCH'] : NULL;
        $client_lm   = isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])
                        ? $_SERVER['HTTP_IF_MODIFIED_SINCE'] : NULL;

        if( ($client_etag and $client_etag == '"'.$timestamp.'"') #etag
            or
            ($client_lm and ($client_lm == $last_modified or $timestamp == strtotime($client_lm))) #last-modified
        ) {
			header('HTTP/1.1 304 Not Modified');
			return false;
		}

		return true;
	}
}
?>
