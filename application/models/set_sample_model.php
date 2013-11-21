<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Set_sample_model extends BOI_Model
{    
    public function __construct()
    {
        parent::__construct();
        
    }

    public function set_user_info($user_id, $money)
    {
        //dao load
        $this->load->model('dao/user');
        
        //接続をマスタに切り替える・・・これ怪しい・・・
        $this->_master();
        
        //更新ロジック開始
        try {
            //トランザクション開始
            $this->begin();
            
            //データ取得
            $user = $this->user->get_by_user_id($user_id);
            
            //ユーザーが存在しているか
            if($user) {
                //対象ユーザーの現在所持している金額
                $c_money = $user["money"];
                
                //合計金額計算
                $t_money = $c_money + $money;
                
                echo $t_money.":".$c_money.":".$money;
                
                //更新情報
                $update_data = array("money" => $t_money);
                $update_where = array("userId" => $user_id);
                
                //update実行
                if(!$this->user->update($update_data, $update_where)) {
                    throw new Exception('update is failed');
                }
                if(!$this->commit()) {
                    throw new Exception('update is failed');
                }
            } else {
                throw new Exception('not exist user_id');
            }
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            //rollback処理
            $this->rollback();
            return FALSE;
        }
        
        //キャッシュ情報削除
        $this->user->delete_user_cache($user_id);

        return TRUE;
    }
}

/* End of file Set_sample_model.php */
/* Location: ./application/model/Set_sample_model.php */