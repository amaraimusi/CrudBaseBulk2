<?php 


namespace App\Model;

/**
 * ネコ管理画面のモデルクラス
 * @since 2023-9-3
 * @version 0.0.1
 *
 */
class Neko extends BaseX{
	
	public function __construct(){
		parent::__construct();  // 基本クラスのコンストラクタを呼び出す
	}
	
	
	// ■■■□□□■■■□□□
	public function test(){
		
		$sql = "SELECT * FROM missions LIMIT 1";
		$res = $this->query($sql);
		
	}
}