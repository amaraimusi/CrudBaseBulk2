<?php

namespace App\Controller;

use CrudBase\PdoDao;
use CrudBase\PDOSessionHandler;

//■■■□□□■■■□□□
// use Illuminate\Http\Request;
use App\Consts;
// use App\Http\Controllers\Controller;
// use Illuminate\Support\Arr;
// use Illuminate\Support\Facades\DB;
// use CrudBase\CrudBase;
// use App\Consts\ConstCrudBase;

/**
 * 基本コントローラ(プレーン版）
 * 
 * @since 2023-9-1
 * @version 0.01
 */
class CrudBaseController{
	
	private $dao; // データベースアクセスオブジェクト
	
	private $screen_code; // 画面コード
	
	private $sessionKeys; // セッションのキーリスト
	
	// コンストラクタ
	public function __construct($screen_code) {
		global $g_env;
		
		$dbConf = [
				'host' => $g_env['DB_HOST'], // ホスト名
				'db_name' => $g_env['DB_NAME'], // データベース名
				'user' => $g_env['DB_USER'], // DBユーザー名
				'pw' => $g_env['DB_PASS'], // DBパスワード
		];
		
		$this->dao = PdoDao::getInstance($dbConf);
		
		$pdo = $this->dao->getPdo();
		
		$handler = new PDOSessionHandler($pdo);
		
		session_set_save_handler(
				[$handler, 'open'],
				[$handler, 'close'],
				[$handler, 'read'],
				[$handler, 'write'],
				[$handler, 'destroy'],
				[$handler, 'gc']
				);
		
		// セッションを開始する
		session_start();

		$this->screen_code = $screen_code;
		
	}
	
	
	/**
	 * 新バージョン判定
	 *
	 * 	旧画面バージョンと現在の画面バージョンが一致するなら新バージョンフラグをOFFにする。
	 * 	旧画面バージョンと現在の画面バージョンが不一致なら新バージョンフラグをONにする。
	 * @param [] $sesSearches セッション検索データ
	 * @param string $this_page_version 画面バージョン
	 * @return int 新バージョンフラグ  0:バージョン変更なし（通常）, 1:新しいバージョン
	 */
	public function judgeNewVersion($sesSearches, $this_page_version){
		
		$old_page_version = $sesSearches['this_page_version'] ?? '';
		$new_version = 0;
		if($old_page_version != $this_page_version){
			$new_version = 1;
		}
		return $new_version;
	}
	
	
	/**
	 * ビューをレンダリング
	 * @param string $view_path ビューのパス
	 * @param string $dataSet ビューへ送るパラメータ
	 * @return string
	 */
	public function render($view_path, $dataSet){
		
		global $g_env;
		
		$view_file_path = $g_env['app_path'] . "\\View\\" . $view_path . ".php";
		
		
		extract($dataSet);
		ob_start();
		
		include $view_file_path;
		$html = ob_get_contents();
		ob_end_clean();
		
		return $html;
	}
	
	
	/**
	 * ユーザー情報を取得する
	 *
	 * @return [] <mixied> ユーザー情報
	 */
	public function getUserInfo($param=[]){
		
		// ユーザー情報の構造
		$userInfo = [
				'id'=> 0,
				'user_id'=> 0,
				'name' => 'none',
				'username' => 'none',
				'user_name' => 'none',
				'update_user' => 'none',
				'ip_addr' => '',
				'user_agent' => '',
				'email'=>'',
				'role' => 'oparator',
				'delete_flg' => 0,
				'nickname' => 'none',
				'authority_wamei'=>'',
				'authority_name'=>'',
				'authority_level'=>0, // 権限レベル(権限が強いほど大きな数値）
		];
		
		//■■■□□□■■■□□□
// 		if(\Auth::id()){// idは未ログインである場合、nullになる。
// 			$userInfo['id'] = \Auth::id(); // ユーザーID
// 			$userInfo['user_id'] = $userInfo['id'];
// 			$userInfo['name'] = \Auth::user()->name; // ユーザー名
// 			$userInfo['username'] = $userInfo['name'] ;
// 			$userInfo['user_name'] = $userInfo['name'];
// 			$userInfo['update_user'] = $userInfo['name'];
// 			$userInfo['email'] = \Auth::user()->email; // メールアドレス
// 			$userInfo['role'] = \Auth::user()->role; // 権限
// 			$userInfo['nickname'] = \Auth::user()->nickname ?? $userInfo['name']; // ニックネーム
			
// 		}
		
		$userInfo['ip_addr'] = $_SERVER["REMOTE_ADDR"];// IPアドレス
		$userInfo['user_agent'] = $_SERVER['HTTP_USER_AGENT']; // ユーザーエージェント
		
		//■■■□□□■■■□□□
// 		if(!empty($userInfo['id'])){
// 			$users = \DB::select("SELECT * FROM users WHERE id={$userInfo['id']}");
// 			$users = $users[0];
// 			$userInfo['role'] = $users->role;
// 			$userInfo['delete_flg'] = $users->delete_flg;
			
// 		}
		
		// 権限が空であるならオペレータ扱いにする
		if(empty($userInfo['role'])){
			$userInfo['role'] = 'oparator';
		}
		
		// 権限まわり
		$role = $userInfo['role'];
		$userInfo['authority'] = $this->getAuthority($role);
		$userInfo['authority_wamei'] = $userInfo['authority']['wamei'];
		$userInfo['authority_name'] = $userInfo['authority']['name'];
		$userInfo['authority_level'] = $userInfo['authority']['level'];
		
		return $userInfo;
	}
	
	/**
	 * 権限情報を取得する
	 * @return [] 権限情報
	 */
	public function getAuthorityInfo(){
		return \App\Consts\ConstCrudBase::AUTHORITY_INFO;
	}
	
	
	/**
	 * パス情報を取得する
	 * @return [] パス情報
	 */
	protected function getPaths(){
		
		global $g_env;
		
		$public_url = $g_env['public_url'];
		$current_url_full = $_SERVER['REQUEST_URI'];
		$current_path = $url_path = parse_url($current_url_full, PHP_URL_PATH);
		$url_path = $g_env['url_path']; // // 例→/CrudBaseBulk2/public/neko/index
		$main_path = $g_env['main_path']; // // 例→/CrudBaseBulk2/public
		$class_name = $g_env['class_name']; // // 例→neko
		$method_name = $g_env['method_name']; // // 例→index
		$model_name = $g_env['model_name']; // // 例→Neko
		$base_path = $g_env['base_path']; // // 例→C:\Users\user\git\CrudBaseBulk2
		$app_path = $g_env['app_path']; // // 例→C:\Users\user\git\CrudBaseBulk2\app
		$public_path = $g_env['public_path']; // // 例→C:\Users\user\git\CrudBaseBulk2\public
		
		return [
				'public_url' => $public_url,
				'current_path' => $current_path,
				'current_url_full' => $current_url_full,
				'url_path' => $url_path,
				'main_path' => $main_path,
				'class_name' => $class_name,
				'method_name' => $method_name,
				'model_name' => $model_name,
				'base_path' => $base_path,
				'app_path' => $app_path,
				'public_path' => $public_path,
		];
	}
	
	
	
	
	
	// ■■■□□□■■■□□□以下は吟味中
	

	/**
	 * デフォルトページ情報を取得する
	 * @param [] $crudBaseData
	 * @return [] デフォルトページ情報
	 */
	private function getDefPages(&$crudBaseData){
		
		$defPages = [];
		if(!empty($crudBaseData['defPages'])){
			$defPages = $crudBaseData['defPages'];
		}
		
		if(empty($defPages['page_no'])) $defPages['page_no'] = 0;
		if(empty($defPages['row_limit'])) $defPages['row_limit'] = 50;
		
		$def_sort_feild =  $crudBaseData['def_sort_feild']; // デフォルトソートフィールド
		$def_sort_type =  $crudBaseData['def_sort_type']; // デフォルトソートタイプ 0:昇順 1:降順
		if(empty($defPages['sort_field'])) $defPages['sort_field'] = $def_sort_feild;
		if(empty($defPages['sort_desc'])) $defPages['sort_desc'] = $def_sort_type;
		
		return $defPages;
	}
	
	
	/**
	 *  レビューモード用ユーザー情報を取得
	 * @param [] $userInfo
	 * @return [] $userInfo
	 */
	public function getUserInfoForReviewMode(){
		
		$userInfo = $this->getUserInfo();
		
		$userInfo['id'] = -1;
		$userInfo['user_id'] = $userInfo['id'];
		$userInfo['update_user'] = 'dummy';
		$userInfo['username'] = $userInfo['update_user'];
		$userInfo['update_user'] = $userInfo['update_user'];
		$userInfo['ip_addr'] = 'dummy_ip';
		$userInfo['user_agent'] = 'dummy_user_agent';
		$userInfo['email'] = 'dummy@example.com';
		$userInfo['role'] = 'admin';
		$userInfo['delete_flg'] = 0;
		$userInfo['nickname'] = '見本ユーザー';
		$userInfo['review_mode'] = 1; // 見本モードON;
		
		$userInfo['authority'] = $this->getAuthority($role);
		$userInfo['authority_wamei'] = $userInfo['authority']['wamei'];
		$userInfo['authority_name'] = $userInfo['authority']['name'];
		$userInfo['authority_level'] = $userInfo['authority']['level'];
		
		return $userInfo;
	}
	

	

	/**
	 * 権限に紐づく権限エンティティを取得する
	 * @param string $role 権限
	 * @return array 権限エンティティ
	 */
	private function getAuthority($role){

		// 権限情報を取得する
		$authorityData = $this->getAuthorityInfo();
		
		$authority = [];
		if(!empty($authorityData[$role])){
			$authority = $authorityData[$role];
		}
		
		return $authority;
	}
	

	/**
	 * ユーザーをアプリケーションからログアウトさせる
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function logout(Request $request)
	{
		\Auth::logout();
		
		$request->session()->invalidate();
		
		$request->session()->regenerateToken();
		
		return redirect('/');
	}
	
	/**
	 * データをCSVファイルとしてダウンロードする。(UTF-8）
	 *
	 * @param string $csv_file CSVファイル名
	 * @param array  $data データ		エンティティ配列型
	 * @param bool $bom_flg BOMフラグ  0:BOMなし（デフォ）,  1:BOM有
	 */
	protected function csvOutput($csv_file, $data, $bom_flg=0){
		
		$buf = "";
		
		// BOM付きutf-8のファイルである場合
		if(!empty($bom_flg)){
			$buf = "¥xEF¥xBB¥xBF";
		}
		
		// CSVデータの作成
		if(!empty($data)){
			$i=0;
			foreach($data as $ent){
				foreach($ent as $v){
					$cell[$i][] = $v;
				}
				$buf .= implode(",",$cell[$i])."\n";
				$i++;
			}
		}
		
		// CSVファイルのヘッダーを書き出す
		header ("Content-disposition: attachment; filename=" . $csv_file);
		header ("Content-type: application/octet-stream; name=" . $csv_file);
		
		print($buf); // CSVデータの書き出し
		
	}
	
	
	/**
	 * テンプレートからファイルパスを組み立てる
	 * @param array $FILES $_FILES
	 * @param string $path_tmpl ファイルパステンプレート
	 * @param array $ent エンティティ
	 * @param string $field
	 * @param string $date
	 * @return string ファイルパス
	 */
	protected function makeFilePath(&$FILES, $path_tmpl, $ent, $field, $date=null){
		
		// $_FILESにアップロードデータがなければ、既存ファイルパスを返す
		if(empty($FILES[$field])){
			return $ent[$field];
		}
		
		$fp = $path_tmpl;
		
		if(empty($date)){
			$date = date('Y-m-d H:i:s');
		}
		$u = strtotime($date);
		
		// ファイル名を置換
		$fn = $FILES[$field]['name']; // ファイル名を取得
		
		// ファイル名が半角英数字でなければ、日時をファイル名にする。（日本語ファイル名は不可）
		if (!preg_match("/^[a-zA-Z0-9-_.]+$/", $fn)) {
			
			// 拡張子を取得
			$pi = pathinfo($fn);
			$ext = $pi['extension'];
			if(empty($ext)) $ext = 'png';
			$fn = date('Y-m-d_his',$u) . '.' . $ext;// 日時ファイル名の組み立て
		}
		
		$fp = str_replace('%fn', $fn, $fp);
		
		// フィールドを置換
		$fp = str_replace('%field', $field, $fp);
		
		// 日付が空なら現在日時をセットする
		$Y = date('Y',$u);
		$m = date('m',$u);
		$d = date('d',$u);
		$H = date('H',$u);
		$i = date('i',$u);
		$s = date('s',$u);
		
		$fp = str_replace('%Y', $Y, $fp);
		$fp = str_replace('%m', $m, $fp);
		$fp = str_replace('%d', $d, $fp);
		$fp = str_replace('%H', $H, $fp);
		$fp = str_replace('%i', $i, $fp);
		$fp = str_replace('%s', $s, $fp);
		
		return $fp;
		
	}
	
	/**
	 * DBテーブルからDBフィールドデータを取得します。
	 * @param string $tbl_name DBテーブル名
	 * @return [] 各フィールドの詳細情報
	 */
	public function getDbFieldData($tbl_name){
		
		$dbFieldData0 = $this->getFieldDataFromDb($tbl_name);
		$dbFieldData = [];
		foreach($dbFieldData0 as $ent){
			$field = $ent->Field;
			$dbFieldData[$field] = (array)$ent;
		}
		
		// 型長とデータ型を取得する
		foreach($dbFieldData as &$fEnt){
			if(empty($fEnt['Type'])) continue;
			$data_type = $fEnt['Type'];
			
			// 型長を取得する
			$matches = null;
			preg_match('/\d+/', $data_type, $matches);
			$fEnt['long'] = $matches[0] ?? null;
			
			// データ型を取得する
			$fEnt['type'] = preg_replace('/\([^)]+\)/', '', $data_type); // カッコとカッコ内の文字列を削除した文字列を取得する
			
		}
		unset($fEnt);
		
		return $dbFieldData;
	}
	
	/**
	 * // 値が空であればデフォルトをセットします。
	 * @param [] $ent エンティティ
	 * @param [] $dbFieldData DBフィールドデータ→getDbFieldDataメソッドで取得したフィールドデータ
	 */
	public function setDefalutToEmpty($ent, $dbFieldData){
		foreach($ent as $field=>$value){
			if(empty($dbFieldData[$field])) continue;
			$fEnt = $dbFieldData[$field];
			$type = $fEnt['type'];
			$default = $fEnt['Default'];
			if($type == 'int' || $type="float" || $type='double'){
				if(empty($value)){
					$ent[$field] = $default;
				}
			}
			
			if($type == 'date'){
				if($value=='0000-00-00' || $value == '0000/00/00'){
					$ent[$field] = $default;
				}
			}
		}
		
		return $ent;
	}
	
	
	/**
	 * DBテーブルから各フィールドの詳細情報を取得します。
	 * @param string $tbl_name DBテーブル名
	 * @return [] 各フィールドの詳細情報
	 */
	private function getFieldDataFromDb($tbl_name){
		$sql="SHOW FULL COLUMNS FROM {$tbl_name}";
		$res = DB::select($sql);
		
		return $res;
	}

	/**
	 * 次のソート番号を取得する
	 * @param string $tbl_name DBテーブル名
	 * @param string $order 方向タイプ asc:昇順用（最大数値）, desc:降順用（最小数値）
	 * @return int ソート番号
	 */
	public function getNextSortNo($tbl_name, $order = 'asc'){
		
		$sort_no = 0;
		
		if($order == 'asc'){
			
			$sql="SELECT  MAX(sort_no) AS next_sort_no FROM {$tbl_name};";
			$res = DB::select($sql);
			
			if($res){
				$sort_no = $res[0]->next_sort_no;
				$sort_no++;
			}
		}else{
			$sql="SELECT  MIN(sort_no) AS next_sort_no FROM {$tbl_name};";
			$res = DB::select($sql);
			
			if($res){
				$sort_no = $res[0]->next_sort_no;
				$sort_no--;
			}
		}
		
		return $sort_no;
	}
	
	/**
	 * CSRFトークンを発行する
	 * @return string CSRFトークン
	 */
	public function makeCsrfToken($length = 8)
	{
		return base_convert(mt_rand(pow(36, $length - 1), pow(36, $length) - 1), 10, 36);
	}
	
	
	/**
	 * セッションからデータを取得する
	 * @param string $key キー
	 * @return mixed 値
	 */
	public function getFromSession($key){
		$key2 = $this->screen_code . $key;
		return $_SESSION[$key2] ?? null;
	}
	
	
	/**
	 * セッションからデータを削除する
	 * @param string $key
	 * @param mixed $value
	 */
	public function setToSession($key, $value){
		$key2 = $this->screen_code . $key;
		$_SESSION[$key2] = $value;
		
		$this->sessionKeys[] = $key;
		
		$this->sessionKeys = array_unique($this->sessionKeys); // 重複をクリア
		
	}
	
	
	/**
	 * セッションに保管している当画面が関係するデータをクリアする
	 */
	public function clearSession(){
		foreach($this->sessionKeys as $key){
			$key2 = $this->screen_code . $key;
			unset($_SESSION[$key2]);
		}

		$this->sessionKeys = [];
	}
	

}