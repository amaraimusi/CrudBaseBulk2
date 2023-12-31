<?php 

namespace App\Controller;

use App\Model\Neko;
use CrudBase\Request;
use CrudBase\CrudBase;

/**
 * ネコ管理画面のコントーラクラス
 * @version 1.0.0
 * @since 2023-9-27
 * @author amaraimusi
 *
 */
class NekoController extends CrudBaseController {
	
	// 画面のバージョン → 開発者はこの画面を修正したらバージョンを変更すること。バージョンを変更するとキャッシュやセッションのクリアが自動的に行われます。
	public $this_page_version = '1.0.0';
	
	private $screen_code = 'neko_sample';
	
	public function __construct() {
		parent::__construct($this->screen_code);  // 基本クラスのコンストラクタを呼び出す
	}
	
	/**
	 * 一覧画面の表示アクション
	 * @return string
	 */
	public function index(){
		
		$request = new Request();

		$sesSearches = $this->getFromSession('neko_searches_key'); // セッションからセッション検索データを受け取る
		if($sesSearches == null ) $sesSearches = [];
		
		
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
					
					// CBBXS-5000
					'id' => $request->id, // id
					'neko_val1' => $request->neko_val1, // ネコ数値・範囲1
					'neko_val2' => $request->neko_val2, // ネコ数値・範囲2
					'neko_name' => $request->neko_name, // neko_name
					'neko_date_ym' => $request->neko_date_ym, // ネコ日付・年月
					'neko_date1' => $request->neko_date1, // ネコ日付・範囲1
					'neko_date2' => $request->neko_date2, // ネコ日付・範囲2
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
		$this->setToSession('neko_searches_key', $searches); // セッションに検索データを書き込む
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		$paths = $this->getPaths(); // パス情報を取得する
		
		$csrf_token = $this->makeCsrfToken(); // CSRFトークンを発行する
		$this->setToSession('neko_csrf_token', $csrf_token);
		
		$model = new Neko();
		$fieldData = $model->getFieldData();
		$res = $model->getData($searches, ['def_per_page' => $def_per_page]);
		$data = $res['data'];
		$data_count = $res['total'];

		// CBBXS-5001
		$nekoTypeList = $model->getNekoTypeList(); // ネコ種別リスト
		// CBBXE
		
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
				
				// CBBXS-5002
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
				// CBBXS-5003
				'nekoTypeList'=>$nekoTypeList,
				// CBBXE
		]);
	}
	
	
	/**
	 * SPA型・入力フォームの登録アクション | 新規入力アクション、編集更新アクション、複製入力アクションに対応しています。
	 * @return string
	 */
	public function reg_action(){
		
		// CSRFトークン（Cross-site Request Forgery）による正規のページからアクセスが行われていることを証明確認する。
		if($this->getFromSession('neko_csrf_token') != $_POST['_token']){
			echo '・セッションタイムアウト（時間切れ）です。もう一度やりなおしてください。<br>・正規ページからアクセスしていますか？';
			die();
		}
		
		$json=$_POST['key1'];
		$res = json_decode($json,true);

		$ent = $res['ent'];
		
		// IDフィールドです。 IDが空である場合、 新規入力アクションという扱いになります。なお、複製入力アクションは新規入力アクションに含まれます。
		$id = !empty($ent['id']) ? $ent['id'] : null;
		
		// DBテーブルからDBフィールド情報を取得します。
		$dbFieldData = $this->getDbFieldData('nekos');

		// 値が空であればデフォルトをセットします。
		$ent = $this->setDefalutToEmpty($ent, $dbFieldData);
		
		$model = new Neko();
		
		$existEnt = $model->getEntityById($id); // DB更新前のエンティティを既存エンティティとして取得する
		
		// エンティティに値未セットのフィールドがあれば、DBから取得した既存の値をセットする。
		foreach($existEnt as $exist_key => $exist_value){
			if (!array_key_exists($exist_key, $ent)) {
				$ent[$exist_key] = $exist_value;
			}
		}
		
 		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
 		$ent['delete_flg'] = 0;
 		$ent['update_user_id'] = $userInfo['user_id'];
 		$ent['ip_addr'] = $userInfo['ip_addr'];
 		$ent['updated_at'] = date('Y-m-d H:i:s');

		if(empty($id)){
			$ent['sort_no'] =$this->getNextSortNo('nekos', 'asc');
		}

		$ent = $model->save('nekos', $ent); // DBへ登録（INSERT、UPDATE兼用）
		
		// CBBXS-5005
		// ▼ ファイルアップロード関連
		$fileUploadK = CrudBase::factoryFileUploadK();
		$front_img_fn = $ent['img_fn'];
		$exist_img_fn = $existEnt['img_fn'] ?? '';
		$fRes = $fileUploadK->uploadForSpa('neko', $_FILES, $ent, 'img_fn', $front_img_fn, $exist_img_fn);
		if($fRes['db_reg_flg']){
			$ent['img_fn'] = $fRes['reg_fp'];
			$ent = $model->save('nekos', $ent); // DBへ登録（INSERT、UPDATE兼用）
		}
		// CBBXE

		$json = json_encode($ent, JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
		
		return $json;
	}
	
	
	/**
	 * 削除/削除取消アクション(無効/有効アクション）
	 */
	public function disabled(){
		
		// CSRFトークン（Cross-site Request Forgery）による正規のページからアクセスが行われていることを証明確認する。
		if($this->getFromSession('neko_csrf_token') != $_POST['_token']){
			echo '・セッションタイムアウト（時間切れ）です。もう一度やりなおしてください。<br>・正規ページからアクセスしていますか？';
			die();
		}
		
		$json=$_POST['key1'];
		$res = json_decode($json,true);
		
		$userInfo = $this->getUserInfo(); // ログインユーザーのユーザー情報を取得する
		
		$json=$_POST['key1'];
		
		$param = json_decode($json,true);//JSON文字を配列に戻す
		$id = $param['id'];
		$action_flg =  $param['action_flg'];
		
		$ent = ['id' => $id];
		
		if(empty($action_flg)){
			$ent['delete_flg'] = 0; // 削除フラグをOFFにする
		}else{
			$ent['delete_flg'] = 1; // 削除フラグをONにする
		}
		
		$ent['update_user_id'] = $userInfo['id'];
		$ent['ip_addr'] = $userInfo['ip_addr'];
		$ent['updated_at'] = date('Y-m-d H:i:s');
		
		$model = new Neko();
		$model->save('nekos', $ent);
		
		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	
	/**
	 * 抹消アクション(無効/有効アクション）
	 */
	public function destroy(){
		
		// CSRFトークン（Cross-site Request Forgery）による正規のページからアクセスが行われていることを証明確認する。
		if($this->getFromSession('neko_csrf_token') != $_POST['_token']){
			echo '・セッションタイムアウト（時間切れ）です。もう一度やりなおしてください。<br>・正規ページからアクセスしていますか？';
			die();
		}
		
		$json=$_POST['key1'];
		
		$param = json_decode($json,true);//JSON文字を配列に戻す
		$id = $param['id'];

		$model = new Neko();
		$model->destroy('nekos', $id);// idを指定して抹消（データベースからDELETE）
		
		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	
	/**
	 * Ajax | ソート後の自動保存
	 *
	 * @note
	 * バリデーション機能は備えていない
	 *
	 */
	public function auto_save(){
		
		// CSRFトークン（Cross-site Request Forgery）による正規のページからアクセスが行われていることを証明確認する。
		if($this->getFromSession('neko_csrf_token') != $_POST['_token']){
			echo '・セッションタイムアウト（時間切れ）です。もう一度やりなおしてください。<br>・正規ページからアクセスしていますか？';
			die();
		}
		
		$json=$_POST['key1'];
		
		$data = json_decode($json,true);//JSON文字を配列に戻す

		$model = new Neko();
		$model->saveAll('nekos', $data);
		
		$res = ['success'];
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	
	
	/**
	 * CSVダウンロード
	 *
	 * 一覧画面のCSVダウンロードボタンを押したとき、一覧データをCSVファイルとしてダウンロードします。
	 */
	public function csv_download(){
		
		$searches = $this->getFromSession('neko_searches_key');// セッションからセッション検索データを受け取る
		
		$model = new Neko();
		$res = $model->getData($searches, ['use_type'=>'csv'] );
		$data = $res['data'];
		
		// データ件数が0件ならCSVダウンロードを中断し、一覧画面にリダイレクトする。
		$count = count($data);
		if($count == 0){
			// リダイレクト
			global $g_env;
			$redirect_url = $g_env['origin_url'] . $g_env['public_url'] . '/neko';
			header("Location: {$redirect_url}");
			exit();
		}

		// ダブルクォートで値を囲む
		foreach($data as &$ent){
			foreach($ent as $field => $value){
				if(mb_strpos($value,'"')!==false){
					$value = str_replace('"', '""', $value);
				}
				$value = '"' . $value . '"';
				$ent[$field] = $value;
			}
		}
		unset($ent);
		
		//列名配列を取得
		$clms=array_keys($data[0]);
		
		//データの先頭行に列名配列を挿入
		array_unshift($data,$clms);
		
		//CSVファイル名を作成
		$date = new \DateTime();
		$strDate=$date->format("Y-m-d");
		$fn='neko'.$strDate.'.csv';
		
		//CSVダウンロード
		$this->csvOutput($fn, $data);
		
	}
	
	
	/**
	 * AJAX | 一覧のチェックボックス複数選択による一括処理
	 * @return string
	 */
	public function ajax_pwms(){
		
		// CSRFトークン（Cross-site Request Forgery）による正規のページからアクセスが行われていることを証明確認する。
		if($this->getFromSession('neko_csrf_token') != $_POST['_token']){
			echo '・セッションタイムアウト（時間切れ）です。もう一度やりなおしてください。<br>・正規ページからアクセスしていますか？';
			die();
		}
		
		$json_param=$_POST['key1'];
		
		$param=json_decode($json_param,true);//JSON文字を配列に戻す
		
		// IDリストを取得する
		$ids = $param['ids'];
		
		// アクション種別を取得する
		$kind_no = $param['kind_no'];
		
		// ユーザー情報を取得する
		$userInfo = $this->getUserInfo();
		
		$model = new Neko();
		
		// アクション種別ごとに処理を分岐
		switch ($kind_no){
			case 10:
				$model->switchDeleteFlg($ids, 0, $userInfo); // 有効化
				break;
			case 11:
				$model->switchDeleteFlg($ids, 1 ,$userInfo); // 削除化(無効化）
				break;
			default:
				return "'kind_no' is unknown value";
		}
		
		return 'success';
	}
	
	
	


	

}