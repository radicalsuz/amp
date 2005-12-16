<?php

require_once('AMP/BaseDB.php');
require_once('Modules/Podcast/Podcast.php');
require_once('XML/Serializer.php');

class Podcast_Output_RSS {

	var $podcast;

	function Podcast_Output_RSS(&$podcast) {
		$this->podcast =& $podcast;
	}

	function execute() {
		$podcast =& $this->podcast;
		$items =& $podcast->getItems();
		$options = array(
			XML_SERIALIZER_OPTION_INDENT			=> "\t",
			XML_SERIALIZER_OPTION_XML_DECL_ENABLED	=> true,
			XML_SERIALIZER_OPTION_XML_ENCODING		=> 'utf-8',
			XML_SERIALIZER_OPTION_MODE				=> XML_SERIALIZER_MODE_SIMPLEXML,
			XML_SERIALIZER_OPTION_ROOT_NAME			=> 'rss',
			XML_SERIALIZER_OPTION_ROOT_ATTRIBS		=> array(
				'version' => "2.0",
				'xmlns:itunes' => 'http://www.itunes.com/DTDs/Podcast-1.0.dtd'),
			XML_SERIALIZER_OPTION_ATTRIBUTES_KEY	=> "_attributes",
			XML_SERIALIZER_OPTION_ENTITIES			=> XML_SERIALIZER_ENTITIES_NONE,
			XML_SERIALIZER_OPTION_ENCODE_FUNC		=> 'htmlspecialchars',
			XML_SERIALIZER_OPTION_IGNORE_NULL		=> true
		);
		$serializer =& new XML_Serializer($options);
		$rss = array(
			"channel" => array(
				"title" 		=>	$podcast->getData('title'),
				"link" 			=>	$this->getLink($podcast),
				"description"	=>	$podcast->getData('description'),
				"copyright"		=>	$podcast->getData('copyright'),
				"generator"		=>	$this->getGenerator(),
				"lastBuildDate"	=>	$podcast->lastBuildDate(),
				"language"		=>	$podcast->getData('language'),
				"ttl"			=>	$podcast->getData('ttl'),
				"itunes:summary"=>	$podcast->getData('description'),
				"itunes:subtitle"=>	$podcast->getData('subtitle'),
				"itunes:author"=>	$podcast->getData('author'),
				"itunes:keywords"=>	join(' ', $this->allKeywords($items)),
				"itunes:owner"	=>	array(
					"itunes:name"=>	$podcast->getData('author'),
					"itunes:email"=>$podcast->getData('email')),
				"image"			=>	($url = $this->imageLink($podcast))?array(
					"url" 		=>	$url,
					"title"		=>	$podcast->getData('title'),
					"link"		=>	$this->getLink($podcast)):null,
				"itunes:image"	=>	array("_attributes" => array(
					"href" => $this->imageLink($podcast))),
				"itunes:category"=>	$this->categoriesToAttrArray($podcast->getData('category')),
				"item"			=>	$this->itemSetToRSSArray($podcast->getItems())
			)
		);

		$status = $serializer->serialize($rss);

		return $serializer->getSerializedData();
	}

	function itemSetToRSSArray($podcast_item_set) {
		if(!$podcast_item_set->isReady()) {
			return null;
		}
		while($item = $podcast_item_set->getData()) {
			$items[] = array(
				"title"			=>	$item['title'],
				"pubDate"		=>	date('D, j M Y G:i:s T', strtotime($item['date'])),
				"guid"			=>	$this->getGuid($item['file']),
				"enclosure"		=>  array("_attributes"	=>	array(
					"url"		=>	$this->getFileLink($item['file']),
					"length"	=>	Podcast::convert_time($item['length']),
					"type"		=>	"audio/mpeg")),
				"link"			=>	$this->getItemLink($item['id']),
				"itunes:author"	=>	$item['author'],
				"itunes:subtitle"=>	$item['subtitle'],
				"itunes:summary"=>	$item['description'],
				"description"	=>	$item['description'],
				"itunes:category"=>	$item['category'],
				"itunes:duration"=>	$item['length'],
				"itunes:explicit"=>	'no',
				"itunes:keywords"=>	$item['keywords']
			);	
		}
		return $items;
	}

	function allKeywords($podcast_item_set) {
		if(!$podcast_item_set->isReady()) {
			return null;
		}
		while($item = $podcast_item_set->getData()) {
			$keywords[] = $item['keywords'];
		}
		return $keywords;
	}
		
	function getItemLink($id) {
		return AMP_SITE_URL . 'podcast.php?item='.$id;
	}

	function getGuid($seed) {
		return $this->getFileLink($seed);
	}

	function getFileLink($file) {
		return AMP_SITE_URL . 'downloads/' . $file;
	}

	function categoriesToAttrArray($categories) {
		$categories = preg_split('/\s?,\s?/', $categories);
		if(!is_array($categories)) {
			$categories = array($categories);
		}
		foreach($categories as $category) {
			$attrArray[] = array("_attributes" => array("text" => $category));
		}
		return $attrArray;
	}

	function getLink($podcast) {
		return AMP_SITE_URL . 'podcast.php?id=' . $podcast->id;
	}

	function imageLink($podcast) {
		if($image = $podcast->getData('image')) {
			return AMP_SITE_URL . 'img/pic/' . $image;
		}
	}

	function getGenerator() {
		return "Activist Mobilization Platform ".AMP_SYSTEM_VERSION_ID;
	}
}
?>
