<?php

require_once('RSSWriter/rss10.inc');

class AMP_RSSWriter extends RSSWriter {

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
    function execute() {
        $this->serialize();
    }
}
?>
