<?php
require_once( 'unit_tests/config.php' );

class TestArticleInc extends UnitTestCase {

	var $dbcon;
	var $rs;

	function TestArticleInc() {
		$this->UnitTestCase('article.inc.php functional test');
	}

	function setUp() {
		$this->dbcon =& new MockADODB_mysql($this);
		$this->rs =& new MockADORecordSet_mysql($this);
		$this->dbcon->setReturnReference('CacheExecute', $this->rs);

	}

	function testLoads() {
		$dbcon =& $this->dbcon;
		ob_start();
		include('AMP/Article/article.inc.php');
		$page = ob_get_clean();
	}

	function testPreview() {
		$MM_id = 415;
		$_GET['preview'] = 1;
		$dbcon =& $this->dbcon;
		$dbcon->expectOnce('CacheExecute', array("SELECT * FROM articles WHERE id = $MM_id"));
		ob_start();
		include('AMP/Article/article.inc.php');
		$page = ob_get_clean();
		$dbcon->tally();
	}

	function testTitles() {
		$rs =& $this->rs;
		$rs->setReturnValue('Fields', 'Article Test Title', array('title'));
		$rs->setReturnValue('Fields', 'Article Test Subtitle', array('subtitile'));
		$dbcon =& $this->dbcon;
		ob_start();
		include('AMP/Article/article.inc.php');
		$page = ob_get_clean();
		$this->assertWantedPattern('/<p class="title">Article Test Title<\/p>/', $page);
		$this->assertWantedPattern('/<span class="subtitle">Article Test Subtitle<\/span><br>/', $page);
	}	

	function testContents() {
	}
}

UnitRunner_instantiate(__File__);
?>
