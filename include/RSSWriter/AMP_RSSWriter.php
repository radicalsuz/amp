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
				$key = trim($key);
				if ($key!="uri") {
					if(($attr = strpos($key, ' ')) !== false) {
						$end_key = substr($key, 0, $attr);
					} else {
						$end_key = $key;
					}
					if (is_array($value)) {
						$keys = array_keys($value);
						if (strpos($keys[0], ':' ) !== false) {
							print "    <${key}>\n";
							foreach ($value as $k1 => $v1) {
								if(($attr = strpos($k1, ' ')) !== false) {
									$end_k1 = substr($k1, 0, $attr);
								} else {
									$end_k1 = $k1;
								}
								if(!$this->isCdata($v1))
									$v1 = htmlspecialchars($v1);
								print "      <${k1}>" . $v1 . "</${end_k1}>\n";
							}
							print "    </${end_key}>\n";
						} else {
							foreach ($value as $v1) {
								if(!$this->isCdata($v1))
									$v1 = htmlspecialchars($v1);
								print "    <${key}>" . $v1 . "</${end_key}>\n";
							}
						}
					} else {
						if(!$this->isCdata($value))
							$value = htmlspecialchars($value);
						print "    <${key}>" . $value . "</${end_key}>\n";
					}
				}
			}
			print "  </item>\n\n";
		}
	}

	function isCdata($content) {
		return preg_match('/^<!\[CDATA\[.*\]\]>$/ims', $content);
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

    function preamble() {
        header("Content-type: text/xml");
        print '<?xml version="1.0" encoding="'.AMP_SITE_CONTENT_ENCODING.'"?>
<rdf:RDF 
         xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
         xmlns="http://purl.org/rss/1.0/"
         xmlns:mn="http://usefulinc.com/rss/manifest/"
';
        foreach ($this->modules as $prefix => $uri) {
            print "         xmlns:${prefix}=\"${uri}\"\n";
        }
        print ">\n\n";
    }
}
?>
