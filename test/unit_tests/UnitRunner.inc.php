<?php
function UnitRunner_instantiate( $filename ){
 	$obj_name = basename( $filename , '.php' ) ;
	if(! defined('RUNNER') && class_exists( $obj_name) ) {
		define('RUNNER', true);

		$test = &new $obj_name('AMP '. substr($obj_name, 4) .' Unit Tests');
		$test->run(new HtmlReporter());
	}
}
?>
