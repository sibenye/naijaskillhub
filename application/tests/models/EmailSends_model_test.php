<?php
class EmailSends_model_test extends TestCase {

    public function setUp() {
        // Reset CodeIgniter super object
        $this->resetInstance();

        // Create mock object for CI_Loader
        $loader = $this->getMockBuilder('CI_Loader')->setMethods([
                'database','email'
        ])->getMock();
        $loader->method('database')->willReturn($loader);
        $loader->method('email')->willReturn($loader);
        // Inject mock object into CodeIgniter super object
        $this->CI->load = $loader;

        if (! class_exists('CI_DB', false)) {
            // Define CI_DB class
            eval('class CI_DB extends CI_DB_query_builder { }');
        }

        $this->obj = new EmailSends_model();;
    }

    public function test_send_activation_email() {
        $user = new User();
        $user->id = 1;
        $user->emailAddress = 'emailAddress';

        $email = $this->getMockBuilder('CI_Email')->disableOriginalConstructor()->getMock();
        $email->expects($this->once())->method('send');

        $db = $this->getMockBuilder('CI_DB')->disableOriginalConstructor()->getMock();
        $db->method('update')->with(USERS_TABLE,$this->isType('array'), $this->isType('array'))->willReturn(TRUE);

        // Inject mock object into the model
        $this->obj->email = $email;
        $this->obj->db = $db;
        $this->obj->send_activation_email($user);

    }
}
