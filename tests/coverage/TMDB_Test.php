<?php
class TMDB_Test extends PHPUnit_Framework_TestCase {
	public function testGetInstance() {
		$instance = \TMDB\Client::getInstance();
		$this->assertInstanceOf("\\TMDB\\Client", $instance);
		$this->assertEmpty($instance->error);
	}
}
