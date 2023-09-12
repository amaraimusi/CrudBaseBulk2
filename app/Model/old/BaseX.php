<?php 

namespace App\Model;

use CrudBase\PdoDao;

/**
 * 基本モデル
 * @version 0.0.1
 * @since 2023-8-28
 * @author kenzy
 *
 */
class BaseX{
	
	private $dao; // データベースアクセスオブジェクト
	
	public function __construct(){
		global $g_env;

		$dbConf = [
				'host' => $g_env['DB_HOST'], // ホスト名
				'db_name' => $g_env['DB_NAME'], // データベース名
				'user' => $g_env['DB_USER'], // DBユーザー名
				'pw' => $g_env['DB_PASS'], // DBパスワード
		];
		
		$this->dao = new PdoDao($dbConf);
		
	}
	
	
	/**
	 * SQLクエリを実行する
	 * @param string $sql
	 * @return [] レスポンス
	 */
	public function query($sql){
		return $this->dao->query($sql);
		
	}
	
	
}