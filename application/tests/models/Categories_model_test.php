<?php

class Categories_model_test extends TestCase {
	
	public function setUp()
	{
		// Reset CodeIgniter super object
		$this->resetInstance();
		
		// Create mock object for CI_Loader
		$loader = $this->getMockBuilder('CI_Loader')->setMethods(['database'])->getMock();
		$loader->method('database')->willReturn($loader);
		// Inject mock object into CodeIgniter super object
		$this->CI->load = $loader;
		
		if (! class_exists('CI_DB', false)) {
			// Define CI_DB class
			eval('class CI_DB extends CI_DB_query_builder { }');
		}
		
		$this->obj = new Categories_model();
	}
	
	public function test_get_categories() {
		
		$result_array = [
				[
						"id" => "1",
						"title" => "News test",
						"slug" => "news-test",
						"text" => "News text",
				],
				[
						"id" => "2",
						"title" => "News test 2",
						"slug" => "news-test-2",
						"text" => "News text 2",
				],
		];
		
		// Create mock object for CI_DB_result
		$db_result = $this->getMockBuilder('CI_DB_result')
		->disableOriginalConstructor()
		->getMock();
		$db_result->method('result_array')->willReturn($result_array);
		
		// Create mock object for CI_DB
		$db = $this->getMockBuilder('CI_DB')
		->disableOriginalConstructor()
		->getMock();
		$db->expects($this->once())
		->method('get')
		->with(CATEGORIES_TABLE)
		->willReturn($db_result);
		
		// Inject mock object into the model
		$this->obj->db = $db;
		$result = $this->obj->get_categories();
		$this->assertEquals($result_array, $result);
		
	}
}