<?php
class Categories_model_test extends TestCase {
    public function setUp() {
        // Reset CodeIgniter super object
        $this->resetInstance();
        
        // Create mock object for CI_Loader
        $loader = $this->getMockBuilder('CI_Loader')->setMethods([ 
                'database'
        ])->getMock();
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
                        "text" => "News text"
                ],
                [ 
                        "id" => "2",
                        "title" => "News test 2",
                        "slug" => "news-test-2",
                        "text" => "News text 2"
                ]
        ];
        
        // Create mock object for CI_DB_result
        $db_result = $this->getMockBuilder('CI_DB_result')->disableOriginalConstructor()->getMock();
        $db_result->method('result_array')->willReturn($result_array);
        
        // Create mock object for CI_DB
        $db = $this->getMockBuilder('CI_DB')->disableOriginalConstructor()->getMock();
        $db->expects($this->once())->method('get')->with(CATEGORIES_TABLE)->willReturn($db_result);
        $db->expects($this->never())->method('get_where')->with(CATEGORIES_TABLE, $this->isType('array'));
        
        // Inject mock object into the model
        $this->obj->db = $db;
        $result = $this->obj->get_categories();
        $this->assertEquals($result_array, $result);
    }
    
    public function test_get_categoriesById() {
        $result_array = [
                [
                        "id" => "2",
                        "title" => "News test 2",
                        "slug" => "news-test-2",
                        "text" => "News text 2"
                ]
        ];
    
        // Create mock object for CI_DB_result
        $db_result = $this->getMockBuilder('CI_DB_result')->disableOriginalConstructor()->getMock();
        $db_result->method('result_array')->willReturn($result_array);
    
        // Create mock object for CI_DB
        $db = $this->getMockBuilder('CI_DB')->disableOriginalConstructor()->getMock();
        $db->expects($this->once())->method('get_where')->with(CATEGORIES_TABLE, $this->isType('array'))->willReturn($db_result);
        $db->expects($this->never())->method('get')->with(CATEGORIES_TABLE);
    
        // Inject mock object into the model
        $this->obj->db = $db;
        $result = $this->obj->get_categories(2);
        $this->assertEquals($result_array, $result);
    }
    
    public function test_post_new_categories() {
        $post_data = array( "name" => "testCat");
        
        $emptyArray = [];
        
        $data = array(
                'name' => $post_data["name"],
                'parentId' => null,
                'description' => null,
                'imageUrl' => null
        );
        
        // Create mock object for CI_DB_result
        $db_result = $this->getMockBuilder('CI_DB_result')->disableOriginalConstructor()->getMock();
        $db_result->method('row_array')->willReturn($emptyArray);
        
        // Create mock object for CI_DB
        $db = $this->getMockBuilder('CI_DB')->disableOriginalConstructor()->getMock();
        $db->expects($this->once())->method('get_where')->with(CATEGORIES_TABLE, array('name' => $post_data["name"]))->willReturn($db_result);
        $db->expects($this->once())->method('insert')->with(CATEGORIES_TABLE, $data);
        $db->expects($this->never())->method('update')->with(CATEGORIES_TABLE, $data, array('id' => $this->isType('integer')));
        
        // Inject mock object into the model
        $this->obj->db = $db;
        $this->obj->save_category($post_data);
    }
    
    public function test_update_existing_categories() {
        $post_data = array( "id" => 1, "name" => "testCat", "description" => "test");
    
        $existingCat = array( "id" => 1, "name" => "existingCat", "description" => null, "parentId" => null, "imageUrl" => null);
    
        $data = array(
                'name' => $post_data["name"],
                'parentId' => null,
                'description' => $post_data["description"],
                'imageUrl' => null
        );
    
        // Create mock object for CI_DB_result
        $db_result = $this->getMockBuilder('CI_DB_result')->disableOriginalConstructor()->getMock();
        $db_result->method('row_array')->willReturn($existingCat);
        
        $idOnly = array('id' => $post_data["id"]);
        $nameOnly = array('name' => $post_data["name"]);
    
        // Create mock object for CI_DB
        $db = $this->getMockBuilder('CI_DB')->disableOriginalConstructor()->getMock();
        $db->expects($this->exactly(2))->method('get_where')->with(CATEGORIES_TABLE, $this->isType('array'))->willReturn($db_result);
        $db->expects($this->never())->method('insert')->with(CATEGORIES_TABLE, $data);
        $db->expects($this->once())->method('update')->with(CATEGORIES_TABLE, $data, array('id' => $post_data["id"]));
    
        // Inject mock object into the model
        $this->obj->db = $db;
        $this->obj->save_category($post_data);
    }
}