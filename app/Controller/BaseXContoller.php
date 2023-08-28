<?php 

namespace App\Controller;

class BaseXController {
	
	public function __construct() {
		// コンストラクタで何か基本的な処理を行う
		// 例えば、共通の設定をロードする
	}
	
	public function render($view_path, $dataSet){
		
		global $g_baseData;
		
		$view_file_path = $g_baseData['base_path'] . "\\View\\" . $view_path . ".php";
		
		
		extract($dataSet);
		ob_start();
		
		include $view_file_path;
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
}