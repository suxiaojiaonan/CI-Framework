<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class User extends BOI_Model
{
    //table名
    var $table_name = "user";
    
    //ユニークキー
    var $uniq_key = "userId";
    
    public function __construct()
    {
        parent::__construct();
    }
    
    public function get_user_all() {
        //キャッシュキー
        $key = __FUNCTION__."::".$this->table_name;
        
        //キーに対応したキャッシュ情報取得
        $result = $this->boi_cache->get($key);
        
        //キャッシュがあれば返却
        if($result) {
            return $result;
        }
        
        //DBからデータ取得
        $result = $this->get_struct();
        
        if($result) {
            //キャッシュ保存
            $this->boi_cache->add($key, $result);
        }
        
        //結果返却
        return $result;        
    }
    
    public function get_by_user_id($user_id) {
        //キャッシュキー
        $key = $user_id."::".$this->table_name;
        
        //キーに対応したキャッシュ情報取得
        $result = $this->boi_cache->get($key);
        
        //キャッシュがあれば返却
        if($result) {
            return $result;
        }
        
        //DBからデータ取得
        $result = $this->get_by_id($user_id);
        
        if($result) {
            //キャッシュ保存
            $this->boi_cache->add($key, $result);
        }
        
        //結果返却
        return $result;        
    }
}

/* End of file user.php */
/* Location: ./application/model/dao/user.php */