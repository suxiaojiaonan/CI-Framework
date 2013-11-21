<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_sample extends BOI_Controller {
    
    function __construct()
    {
        parent::__construct();
        // Load Model
        $this->load->model('get_sample_model');
    }
    
    function index() {
       
        $user_id = $this->input->get('user_id');
        
        try {
            //必須入力検証
            if( !$user_id ) {
                throw new Exception('validation failed : required is user id');
            }
            //数値検証
            if( !is_numeric($user_id) ) {
                throw new Exception('validation failed : user_id is not a numeric');
            }
        } catch (Exception $e) {
            //ログ出力
             log_message('error', $e->getMessage());
             //JSONデータ返却（エラー）
             $this->api_result(2);
        }

        $ret = $this->get_sample_model->get_user_info($user_id);
        
        if($ret) {
            //JSONデータ返却（正常）
            $this->api_result(1, array("user_info" => $ret));
        } else {
            //JSONデータ返却（エラー）
            $this->api_result(2);
        }
    }
}

/* End of file Espresso_controller.php */
/* Location: ./application/core/Espresso_Controller.php */