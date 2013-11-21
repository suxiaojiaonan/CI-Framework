<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BOI_Controller extends CI_Controller {
    
    protected $_csrf_cookie_name;
    protected $_csrf_expire;
    protected $_csrf_hash;
    protected $_method = "get";//form method
    
    function __construct()
    {
        parent::__construct();

        //POSTの場合はCSRF処理
        //if($this->_method == "post") {
        //    //クロスサイトリクエストフォージェリー対策
        //    $this->csrf_security();
        //}
    }
    
    //JSONデータ返却
    function api_result($status, $params=NULL) {
        
        //結果配列生成
        $api_result_data = array("status" => $status);
        
        if(!empty($params)) {
            //結果配列生成
            $api_result_data = array_merge($api_result_data, $params);
        }
        
        //結果出力
        header('Content-type: application/json');
        echo json_encode($api_result_data);
        exit();
    }
    
    public function csrf_security()
    {
        //security config load
        $this->config->load('security');
        
        //コンフィグ情報取得
        $this->_csrf_cookie_name = $this->config->item('csrf_cookie_name');
        $this->_csrf_expire = $this->config->item('csrf_expire');
        
        //md5乱数生成
        $str = substr((md5(date("YmdD His"))), 0, 10); 

        $this->session->set_userdata();
        // ポスト時にみ
        if (count($_POST) == 0)
        {
                return $this->csrf_set_cookie();
        }

        // Do the tokens exist in both the _POST and _COOKIE arrays?
        if ( ! isset($_POST[$this->_csrf_token_name]) OR 
                 ! isset($_COOKIE[$this->_csrf_cookie_name]))
        {
                $this->csrf_show_error();
        }

        // Do the tokens match?
        if ($_POST[$this->_csrf_token_name] != $_COOKIE[$this->_csrf_cookie_name])
        {
                $this->csrf_show_error();
        }

        // We kill this since we're done and we don't want to 
        // polute the _POST array
        unset($_POST[$this->_csrf_token_name]);

        // Nothing should last forever
        unset($_COOKIE[$this->_csrf_cookie_name]);
        $this->_csrf_set_hash();
        $this->csrf_set_cookie();

        log_message('debug', "CSRF token verified ");

        return $this;
    }
        
        /**
	 * Set Cross Site Request Forgery Protection Cookie
	 *
	 * @return	object
	 */
	public function csrf_set_cookie()
	{
		$expire = time() + $this->_csrf_expire;
		$secure_cookie = (config_item('cookie_secure') === TRUE) ? 1 : 0;

		if ($secure_cookie)
		{
			$req = isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : FALSE;

			if ( ! $req OR $req == 'off')
			{
				return FALSE;
			}
		}

		setcookie($this->_csrf_cookie_name, $this->_csrf_hash, $expire, config_item('cookie_path'), config_item('cookie_domain'), $secure_cookie);

		log_message('debug', "CRSF cookie Set");
		
		return $this;
	}
}

/* End of file BOI_Controller.php */
/* Location: ./application/core/BOI_Controller.php */