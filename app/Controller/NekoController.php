<?php 

namespace App\Controller;

use App\Model\Neko;

class NekoController extends BaseXController {
	
	public function __construct() {
		parent::__construct();  // 基本クラスのコンストラクタを呼び出す
	}
	
	public function index(){
		
		$dataSet = [];
		
		
		return $this->render('Neko/index', $dataSet);
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