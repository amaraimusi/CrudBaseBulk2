<?php 

namespace App\Controller;

use App\Model\Neko;
use CrudBase\Request;

class NekoController extends CrudBaseController {
	
	// 画面のバージョン → 開発者はこの画面を修正したらバージョンを変更すること。バージョンを変更するとキャッシュやセッションのクリアが自動的に行われます。
	public $this_page_version = '0.0.1';
	
	public function __construct() {
		parent::__construct();  // 基本クラスのコンストラクタを呼び出す
	}
	
	/**
	 * 一覧画面の表示アクション
	 * @return string
	 */
	public function index(){
		
		$request = new Request();

		$sesSearches = $_SESSION['neko_searches_key'] ?? [];// セッションからセッション検索データを受け取る
		
		// 新バージョンチェック  0:バージョン変更なし（通常）, 1:新しいバージョン
		$new_version = $this->judgeNewVersion($sesSearches, $this->this_page_version);
		
		$searches = []; // 検索データ
		
		// リクエストのパラメータが空でない、または新バージョンフラグがONである場合、リクエストから検索データを受け取る
		if(!empty($request->all()) || $new_version == 1){
			$searches = [
					'main_search' => $request->main_search, // メイン検索
					
					// CBBXS-3000
					'id' => $request->id, // id
					'neko_val' => $request->neko_val, // neko_val
					'neko_name' => $request->neko_name, // neko_name
					'neko_date' => $request->neko_date, // neko_date
					'neko_type' => $request->neko_type, // 猫種別
					'neko_dt' => $request->neko_dt, // neko_dt
					'neko_flg' => $request->neko_flg, // ネコフラグ
					'img_fn' => $request->img_fn, // 画像ファイル名
					'note' => $request->note, // 備考
					'sort_no' => $request->sort_no, // 順番
					'delete_flg' => $request->delete_flg, // 無効フラグ
					'update_user_id' => $request->update_user_id, // 更新者
					'ip_addr' => $request->ip_addr, // IPアドレス
					'created_at' => $request->created_at, // 生成日時
					'updated_at' => $request->updated_at, // 更新日
					
					// CBBXE
					
					'update_user' => $request->update_user, // 更新者
					'page' => $request->sort, // ページ番号
					'sort' => $request->sort, // 並びフィールド
					'desc' => $request->desc, // 並び向き
					'per_page' => $request->per_page, // 行制限数
			];
			
		}else{
			// リクエストのパラメータが空かつ新バージョンフラグがOFFである場合、セッション検索データを検索データにセットする
			$searches = $sesSearches;
		}
		
		$searches['this_page_version'] = $this->this_page_version; // 画面バージョン
		$searches['new_version'] = $new_version; // 新バージョンフラグ
		$_SESSION['neko_searches_key'] = $searches; // セッションに検索データを書き込む
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		$paths = $this->getPaths(); // パス情報を取得する
		$def_per_page = 20; // デフォルト制限行数
		
		$model = new Neko();
		$fieldData = $model->getFieldData();
		$data = $model->getData($searches, ['def_per_page' => $def_per_page]);
		$data = $res['data'];
		$total = $res['total'];

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