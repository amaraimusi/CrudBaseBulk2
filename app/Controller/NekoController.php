<?php 

namespace App\Controller;

use App\Model\Neko;

class NekoController extends BaseXController {
	
	public function __construct() {
		parent::__construct();  // 基本クラスのコンストラクタを呼び出す
		// その他の初期化処理
	}
	
	public function index(){
		
		
		
		dump('NekoController');//■■■□□□■■■□□□)
	}
	public function bark(){
		
		//require_once 'Model/Neko.php';
		$model = new Neko();
		$model->test();
		
		dump('にゃおーん');//■■■□□□■■■□□□)
		$dataSet = [];
		
		return $this->render('Neko/bark', $dataSet);
	}
	

	

}