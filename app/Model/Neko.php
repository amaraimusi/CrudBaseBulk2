<?php 


namespace App\Model;

class Neko extends BaseX{
	
	public function __construct(){
		parent::__construct();  // 基本クラスのコンストラクタを呼び出す
	}
	
	public function test(){
		
		$sql = "SELECT * FROM missions LIMIT 1";
		$res = $this->query($sql);
		
	}
}