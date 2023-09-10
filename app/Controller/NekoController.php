<?php 

namespace App\Controller;

use App\Model\Neko;
use CrudBase\Request;

class NekoController extends CrudBaseController {
	
	// 画面のバージョン → 開発者はこの画面を修正したらバージョンを変更すること。バージョンを変更するとキャッシュやセッションのクリアが自動的に行われます。
	public $this_page_version = '0.0.2';
	
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

		$def_per_page = 20; // デフォルト・制限行数
		$def_sort_field = 'sort_no'; // デフォルト・ソートフィールド
		$def_desc = 0; // デフォルト・ ソート向き 0:昇順, 1:降順
		
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
					'page' => $request->page ?? 1, // ページ番号
					'sort' => $request->sort ?? $def_sort_field, // 並びフィールド
					'desc' => $request->desc ?? $def_desc, // 並び向き
					'per_page' => $request->per_page ?? $def_per_page, // 行制限数
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
		
		$csrf_token = $this->makeCsrfToken(); // CSRFトークンを発行する
		$_SESSION['neko_csrf_token'] = $csrf_token;
		
		$model = new Neko();
		$fieldData = $model->getFieldData();
		$res = $model->getData($searches, ['def_per_page' => $def_per_page]);
		$data = $res['data'];
		$data_count = $res['total'];

		$nekoTypeList = $model->getNekoTypeList(); // ネコ種別リスト
		
		global $g_env; // 環境データ
		
		$crudBaseData = [
				'csrf_token'=>$csrf_token,
				'data'=>$data,
				'data_count'=>$data_count,
				'searches'=>$searches,
				'userInfo'=>$userInfo,
				'paths'=>$paths,
				'fieldData'=>$fieldData,
				'model_name_c'=>'Neko', // モデル名（キャメル記法）
				'model_name_s'=>'neko', // モデル名（スネーク記法）
				'def_per_page'=>$def_per_page, // デフォルト制限行数
				'this_page_version'=>$this->this_page_version,
				'new_version' => $new_version,
				'debug_mode' => $g_env['debug_mode'] ?? 1, // デバッグモード
				
				// CBBXS-3020B
				'nekoTypeList'=>$nekoTypeList,
				// CBBXE
		];
		
		$crud_base_json = json_encode($crudBaseData, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);

		return $this->render('Neko/index', [
				'data'=>$data,
				'data_count'=>$data_count,
				'searches'=>$searches,
				'userInfo'=>$userInfo,
				'fieldData'=>$fieldData,
				'this_page_version'=>$this->this_page_version,
				'crudBaseData'=>$crudBaseData,
				'crud_base_json'=>$crud_base_json,
				// CBBXS-3020B
				'nekoTypeList'=>$nekoTypeList,
				// CBBXE
		]);
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