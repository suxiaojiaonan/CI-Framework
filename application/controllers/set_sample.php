<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_sample extends BOI_Controller {
    
    function __construct()
    {
        //form　method post
        $this->_method = "post";
        
        parent::__construct();

        // Load Model
        $this->load->model('set_sample_model');
    }
    
    function index() {
       
        $user_id = $this->input->get('user_id');
        $money = $this->input->get('money');
        
        try {
            //必須入力検証
            if( !$user_id ) {
                throw new Exception('validation failed : required is user id');
            }
            //数値検証
            if( !is_numeric($user_id) ) {
                throw new Exception('validation failed : user_id is not a numeric');
            }

            //数値検証
            if( !is_numeric($money) ) {
                throw new Exception('validation failed : money is not a numeric');
            }
        } catch (Exception $e) {
            //ログ出力
             log_message('error', $e->getMessage());
             //JSONデータ返却（エラー）
             $this->api_result(2);
        }

        $ret = $this->set_sample_model->set_user_info($user_id, $money);
        
        if($ret) {
            //JSONデータ返却（正常）
            $this->api_result(1, array("result" => 1));
        } else {
            //JSONデータ返却（エラー）
            $this->api_result(2);
        }
    }
}

/* End of file Set_sample.php */
/* Location: ./application/core/Set_sample.php */