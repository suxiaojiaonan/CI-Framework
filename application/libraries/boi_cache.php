<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Boi_cache
{

	private $config;
	private $m;
	private $client_type;
	private $ci;
	protected $errors = array();

	public function __construct()
	{
		$this->ci =& get_instance();

		// memcachedコンフィグ読み込み
		$this->ci->load->config('memcached');
		$this->config = $this->ci->config->item('memcached');

		// クラスの存在確認
		$this->client_type = class_exists($this->config['config']['engine']) ? $this->config['config']['engine'] : FALSE;
                
                // php memcacheクライアント判定
		if($this->client_type)
		{
			
			switch($this->client_type)
			{
				case 'Memcached':
					$this->m = new Memcached();
					break;
				case 'Memcache':
					$this->m = new Memcache();
					// Set Automatic Compression Settings
					if ($this->config['config']['auto_compress_tresh'])
					{
						$this->setcompressthreshold($this->config['config']['auto_compress_tresh'], $this->config['config']['auto_compress_savings']);
					}
					break;
			}
			log_message('debug', "Memcached Library: " . $this->client_type . " Class Loaded");
                        
                        //接続
			$this->auto_connect();
		}
		else
		{
			log_message('error', "Memcached Library: Failed to load Memcached or Memcache Class");
		}
	}

	/*
	+-------------------------------------+
		Name: auto_connect
		Purpose: memcacheサーバーへの自動接続
		@param return : なし
	+-------------------------------------+
	*/
	private function auto_connect()
	{
		foreach($this->config['servers'] as $key=>$server)
		{
			if(!$this->add_server($server))
			{
				$this->errors[] = "Memcached Library: Could not connect to the server named $key";
				log_message('error', 'Memcached Library: Could not connect to the server named "'.$key.'"');
			}
			else
			{
				log_message('debug', 'Memcached Library: Successfully connected to the server named "'.$key.'"');
			}
		}
	}

	/*
	+-------------------------------------+
		Name: add_server
		Purpose:サーバーの追加
		@param return : TRUE or FALSE
	+-------------------------------------+
	*/
	public function add_server($server)
	{
		extract($server);
		return $this->m->addServer($host, $port, $weight);
	}

	/*
	+-------------------------------------+
		Name: add
		Purpose: キャッシュ保存
		@param return : TRUE or FALSE
	+-------------------------------------+
	*/
	public function add($key = NULL, $value = NULL, $expiration = NULL)
	{
		if(is_null($expiration))
		{
			$expiration = $this->config['config']['expiration'];
		}
		if(is_array($key))
		{
			foreach($key as $multi)
			{
				if(!isset($multi['expiration']) || $multi['expiration'] == '')
				{
					$multi['expiration'] = $this->config['config']['expiration'];
				}
				$this->add($this->key_name($multi['key']), $multi['value'], $multi['expiration']);
			}
		}
		else
		{
			switch($this->client_type)
			{
				case 'Memcache':
					$add_status = $this->m->add($this->key_name($key), $value, $this->config['config']['compression'], $expiration);
					break;

				default:
				case 'Memcached':
					$add_status = $this->m->add($this->key_name($key), $value, $expiration);
					break;
			}

			return $add_status;
		}
	}

	/*
	+-------------------------------------+
		Name: set
		Purpose: memcachedの設定を追加
		@param return : TRUE or FALSE
	+-------------------------------------+
	*/
	public function set($key = NULL, $value = NULL, $expiration = NULL)
	{
		if(is_null($expiration))
		{
			$expiration = $this->config['config']['expiration'];
		}
		if(is_array($key))
		{
			foreach($key as $multi)
			{
				if(!isset($multi['expiration']) || $multi['expiration'] == '')
				{
					$multi['expiration'] = $this->config['config']['expiration'];
				}
				$this->set($this->key_name($multi['key']), $multi['value'], $multi['expiration']);
			}
		}
		else
		{
			switch($this->client_type)
			{
				case 'Memcache':
					$add_status = $this->m->set($this->key_name($key), $value, $this->config['config']['compression'], $expiration);
					break;

				default:
				case 'Memcached':
					$add_status = $this->m->set($this->key_name($key), $value, $expiration);
					break;
			}

			return $add_status;
		}
	}

	/*
	+-------------------------------------+
		Name: get
		Purpose: 対応するキーの値を取得
		@param return : 配列　データ
	+-------------------------------------+
	*/
	public function get($key = NULL)
	{
		if($this->m)
		{
			if(is_null($key))
			{
				$this->errors[] = 'The key value cannot be NULL';
				return FALSE;
			}

			if(is_array($key))
			{
				foreach($key as $n=>$k)
				{
					$key[$n] = $this->key_name($k);
				}
				return $this->m->getMulti($key);
			}
			else
			{
				return $this->m->get($this->key_name($key));
			}
		}
		return FALSE;
	}


	/*
	+-------------------------------------+
		Name: delete
		Purpose: キャッシュ削除
		@param return : 
	+-------------------------------------+
	*/
	public function delete($key, $expiration = NULL)
	{
		if(is_null($key))
		{
			$this->errors[] = 'The key value cannot be NULL';
			return FALSE;
		}

		if(is_null($expiration))
		{
			$expiration = $this->config['config']['delete_expiration'];
		}

		if(is_array($key))
		{
			foreach($key as $multi)
			{
				$this->delete($multi, $expiration);
			}
		}
		else
		{
			return $this->m->delete($this->key_name($key), $expiration);
		}
	}

	/*
	+-------------------------------------+
		Name: replace
		Purpose: 対応すキーの値を変更
		@param return : none
	+-------------------------------------+
	*/
	public function replace($key = NULL, $value = NULL, $expiration = NULL)
	{
		if(is_null($expiration))
		{
			$expiration = $this->config['config']['expiration'];
		}
		if(is_array($key))
		{
			foreach($key as $multi)
			{
				if(!isset($multi['expiration']) || $multi['expiration'] == '')
				{
					$multi['expiration'] = $this->config['config']['expiration'];
				}
				$this->replace($multi['key'], $multi['value'], $multi['expiration']);
			}
		}
		else
		{
			switch($this->client_type)
			{
				case 'Memcache':
					$replace_status = $this->m->replace($this->key_name($key), $value, $this->config['config']['compression'], $expiration);
					break;

				default:
				case 'Memcached':
					$replace_status = $this->m->replace($this->key_name($key), $value, $expiration);
					break;
			}

			return $replace_status;
		}
	}

	/*
	+-------------------------------------+
		Name: flush
		Purpose: キャッシュクリア
		@param return : none
	+-------------------------------------+
	*/
	public function flush()
	{
		return $this->m->flush();
	}

	/*
	+-------------------------------------+
		Name: key_name
		Purpose: キーMD5暗号化
		@param return : md5 key
	+-------------------------------------+
	*/
	private function key_name($key)
	{
		return md5(strtolower($this->config['config']['prefix'].$key));
	}
}
/* End of file boi_cache.php */
/* Location: ./application/libraries/boi_cache.php */
