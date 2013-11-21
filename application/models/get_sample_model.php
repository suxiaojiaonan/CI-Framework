<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Get_sample_model extends BOI_Model
{    
    public function __construct()
    {
        parent::__construct();
        
    }

    public function get_user_info($user_id)
    {
        //dao load
        $this->load->model('dao/user');
        
        //データ取得
        $users = $this->user->get_user_all();
        
        //結果配列定義
        $result = array();
        
        //***********ここからビジネスロジック************//
        if(isset($users[$user_id])) {
            $result["user_id"] = (isset($users[$user_id]["userId"])) ? $users[$user_id]["userId"] : 0;
            $result["money"] = (isset($users[$user_id]["money"])) ? $users[$user_id]["money"] : 0;
            $result["point"] = (isset($users[$user_id]["point"])) ? $users[$user_id]["point"] : 0;
            $result["wepon_point"] = (isset($users[$user_id]["weponPoint"])) ? $users[$user_id]["weponPoint"] : 0;
            $result["gacha_tiket"] = (isset($users[$user_id]["gachaTiket"])) ? $users[$user_id]["gachaTiket"] : 0;
            $result["skill_attack"] = (isset($users[$user_id]["skillAttack"])) ? $users[$user_id]["skillAttack"] : 0;
            $result["skill_difence"] = (isset($users[$user_id]["skillDifence"])) ? $users[$user_id]["skillDifence"] : 0;
        } 

        return $result ? $result : array();
    }
}

/* End of file Sample_model.php */
/* Location: ./application/model/Sample_model.php */