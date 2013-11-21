<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BOI_Model extends CI_Model
{
    
    protected $table_name;//テーブル名
    protected $uniq_key;//一意ID
    
    /**
     * コンストラクタ
     */
    function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->library('boi_cache');
    }
    
    /**
     * DBコネクションをマスタに切り替え
     */
    function _master()
    {
        $this->load->database('master');
    }
 
    /**
     * テーブル名を取得
     */
    function _get_table_name()
    {
        return rtrim(strtolower(get_class($this)), '_model');
    }
 
    /**
     * 全件取得
     */
    function get_all()
    {        
        // クエリの実行
        $query = $this->db->get($this->table_name);
        $result = $query->result_array();
        return $result ? $result : array();
    }
 
    /**
     * 全件取得で配列のキーがuniq_key
     */
    function get_struct()
    {
        // クエリの実行
        $query = $this->db->get($this->table_name);
        $tmp_result = $query->result_array();
        
        //結果配列
        $result = array();
        
        //ユニークキーを配列キーに変更
        foreach ($tmp_result as $value) {
            $result[$value[$this->uniq_key]] = $value;
        }
        
        return $result ? $result : array();
    }
    
    /**
     * ユニークキーの一致するレコードを取得
     */
    function get_by_id($id=null)
    {
        if(is_null($id)) 
        {
            return array();
        }
        
        // クエリの実行
        $query = $this->db->get_where($this->table_name, array($this->uniq_key => $id));
        $result = $query->result_array();
               
        return $result[0] ? $result[0] : array();
    }
    
    /**
     * データ更新
     */
    function update($data=array(), $where=array()) {
        return $this->db->update($this->table_name, $data, $where); 
    }
    
    /**
     * データ挿入
     */
    function insert($data=array()) {
        return $this->db->insert($this->table_name, $data); 
    }
    
    /**
     * トランザクション開始
     */
    function begin() {
        $this->db->trans_begin();
    }
    
    /**
     * トランザクション処理コミット
     */
    function commit() {
        
        if ($this->db->trans_status() === FALSE)
        {
            $this->db->trans_rollback();
            return FALSE;
        }
        else
        {
            $this->db->trans_commit();
            return TRUE;
        }
    }
    
    /**
     * ロールバック
     */
    function rollback() {
            return $this->db->trans_rollback();
    }
    
    /**
     * 対象ユーザーのテーブルキャッシュ削除
     */
    function delete_user_cache($user_id) {
       
        //キー
        $key = $user_id."::".$this->table_name;
        
        //キャッシュ削除
        $this->boi_cache->delete($key);
    }
}
 
// END BOI_Controller Class
 
/* End of file BOI_Controller.php */
/* Location: ./application/core/BOI_Controller.php */