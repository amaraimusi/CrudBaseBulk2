<?php
App::uses('CrudBaseController', 'Controller');
App::uses('PagenationForCake', 'Vendor/Wacg');

/**
 * 任務
 * 
 * 任務画面では任務一覧を検索閲覧、および編集ができます。
 * 
 * 
 * @date 2015-9-16	新規作成 
 * @author k-uehara
 *
 */
class MissionController extends CrudBaseController {

	/// 名称コード
	public $name = 'Mission';
	
	/// 使用しているモデル
	public $uses = array('Mission','CrudBase');
	
	/// オリジナルヘルパーの登録
	public $helpers = array('CrudBase');

	/// デフォルトの並び替え対象フィールド
	public $defSortFeild='Mission.id';
	
	/// デフォルトソートタイプ	  0:昇順 1:降順
	public $defSortType=1;
	
	/// 検索条件情報の定義
	public $kensakuJoken=array();

	/// 検索条件のバリデーション
	public $kjs_validate = array();

	///フィールドデータ
	public $field_data=array();

	/// 編集エンティティ定義
	public $entity_info=array();

	/// 編集用バリデーション
	public $edit_validate = array();
	
	// 当画面バージョン (バージョンを変更すると画面に新バージョン通知とクリアボタンが表示されます。）
	public $this_page_version = '1.9.1'; 



	public function beforeFilter() {
		
		if($_SERVER['SERVER_NAME']!='localhost'){
			echo 'This system localhost only!';
			die();
		}
		
		// 未ログイン中である場合、未認証モードの扱いでページ表示する。
		if(empty($this->Auth->user())){
			$this->Auth->allow(); // 未認証モードとしてページ表示を許可する。
		}
		
		parent::beforeFilter();
	
		$this->initCrudBase();// フィールド関連の定義をする。
	
	}

	/**
	 * indexページのアクション
	 *
	 * indexページでは任務一覧を検索閲覧できます。
	 * 一覧のidから詳細画面に遷移できます。
	 * ページネーション、列名ソート、列表示切替、CSVダウンロード機能を備えます。
	 */
	public function index() {
		
        // CrudBase共通処理（前）
		$crudBaseData = $this->indexBefore('Mission');//indexアクションの共通先処理(CrudBaseController)
		
		//一覧データを取得
		$data = $this->Mission->findData2($crudBaseData);

		// CrudBase共通処理（後）
		$crudBaseData = $this->indexAfter($crudBaseData);//indexアクションの共通後処理
		
		// 雛ファイルリストを取得する
		$hinaFileList = $this->Mission->getHinaFileList();
		$hina_file_json = json_encode($hinaFileList,JSON_HEX_TAG | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_HEX_APOS);
		
		
		$this->set($crudBaseData);
		$this->set(array(
			'title_for_layout'=>'任務',
			'data'=> $data,
			'hinaFileList' => $hinaFileList,
			'hina_file_json' => $hina_file_json,
		));
		
		//当画面系の共通セット
		$this->setCommon();


	}

	/**
	 * 詳細画面
	 * 
	 * 任務情報の詳細を表示します。
	 * この画面から入力画面に遷移できます。
	 * 
	 */
	public function detail() {
		
		$res=$this->edit_before('Mission');
		$ent=$res['ent'];
	

		$this->set(array(
				'title_for_layout'=>'任務・詳細',
				'ent'=>$ent,
		));
		
		//当画面系の共通セット
		$this->setCommon();
	
	}













	/**
	 * 入力画面
	 * 
	 * 入力フォームにて値の入力が可能です。バリデーション機能を実装しています。
	 * 
	 * URLクエリにidが付属する場合は編集モードになります。
	 * idがない場合は新規入力モードになります。
	 * 
	 */
	public function edit() {

		$res=$this->edit_before('Mission');
		$ent=$res['ent'];

		$this->set(array(
				'title_for_layout'=>'任務・編集',
				'ent'=>$ent,
		));
		
		//当画面系の共通セット
		$this->setCommon();

	}
	
	 /**
	 * 登録完了画面
	 * 
	 * 入力画面の更新ボタンを押し、DB更新に成功した場合、この画面に遷移します。
	 * 入力エラーがある場合は、入力画面へ、エラーメッセージと共にリダイレクトで戻ります。
	 */
	public function reg(){
		$res=$this->reg_before('Mission');
		$ent=$res['ent'];
		
		$regMsg="<p id='reg_msg'>更新しました。</p>";
		
		//★DB保存
		$this->Mission->begin();//トランザクション開始
		$ent=$this->Mission->saveEntity($ent);//登録
		$this->Mission->commit();//コミット

		$this->set(array(
				'title_for_layout'=>'任務・登録完了',
				'ent'=>$ent,
				'regMsg'=>$regMsg,
		));
		
		//当画面系の共通セット
		$this->setCommon();

	}
	
	
	
	
	/**
	 * DB登録
	 *
	 * @note
	 * Ajaxによる登録。
	 * 編集登録と新規入力登録の両方に対応している。
	 */
	public function ajax_reg(){
		App::uses('Sanitize', 'Utility');
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
	
		// JSON文字列をパースしてエンティティを取得する
		$json=$_POST['key1'];
		$ent = json_decode($json,true);
		
		// 登録パラメータ
		$reg_param_json = $_POST['reg_param_json'];
		$regParam = json_decode($reg_param_json,true);
	
	
		// アップロードファイルが存在すればエンティティにセットする。
		$upload_file = null;
		if(!empty($_FILES["upload_file"])){
			$upload_file = $_FILES["upload_file"]["name"];
			$ent['mission_fn'] = $upload_file;
		}
	
	
		// 更新ユーザーなど共通フィールドをセットする。
		$ent = $this->setCommonToEntity($ent);
	
		// エンティティをDB保存
		$this->Mission->begin();
		$ent = $this->Mission->saveEntity($ent,$regParam);
		$this->Mission->commit();//コミット

		if(!empty($upload_file)){
			
			// ファイルパスを組み立て
			$upload_file = $_FILES["upload_file"]["name"];
			$ffn = "game_rs/app{$id}/app_icon/{$fn}";
			
			// 一時ファイルを所定の場所へコピー（フォルダなければ自動作成）
			$this->copyEx($_FILES["upload_file"]["tmp_name"], $ffn);
	
	
		}

		$json_data=json_encode($ent,true);//JSONに変換
	
		return $json_data;
	}
	
	
	
	
	
	
	
	/**
	 * 削除登録
	 *
	 * @note
	 * Ajaxによる削除登録。
	 * 削除更新でだけでなく有効化に対応している。
	 * また、DBから実際に削除する抹消にも対応している。
	 */
	public function ajax_delete(){
		App::uses('Sanitize', 'Utility');
	
		$this->autoRender = false;//ビュー(ctp)を使わない。
	
		// JSON文字列をパースしてエンティティを取得する
		$json=$_POST['key1'];
		$ent0 = json_decode($json,true);
		
		// 登録パラメータ
		$reg_param_json = $_POST['reg_param_json'];
		$regParam = json_decode($reg_param_json,true);

	   // 抹消フラグ
	   $eliminate_flg = 0;
	   if(isset($regParam['eliminate_flg'])) $eliminate_flg = $regParam['eliminate_flg'];
	   
		// 削除用のエンティティを取得する
		$ent = $this->getEntForDelete($ent0['id']);
		$ent['delete_flg'] = $ent0['delete_flg'];
	
		// エンティティをDB保存
		$this->Mission->begin();
		if($eliminate_flg == 0){
			$ent = $this->Mission->saveEntity($ent,$regParam); // 更新
		}else{
		    $this->Mission->delete($ent['id']); // 削除
		}
		$this->Mission->commit();//コミット
	
		$ent=Sanitize::clean($ent, array('encode' => true));//サニタイズ（XSS対策）
		$json_data=json_encode($ent);//JSONに変換
	
		return $json_data;
	}
	
	
	/**
	* Ajax | 自動保存
	* 
	* @note
	* バリデーション機能は備えていない
	* 
	*/
	public function auto_save(){
		
		App::uses('Sanitize', 'Utility');
		
		$this->autoRender = false;//ビュー(ctp)を使わない。
		
		$json=$_POST['key1'];
		
		$data = json_decode($json,true);//JSON文字を配列に戻す
		
		// データ保存
		$this->Mission->begin();
		$this->Mission->saveAll($data);
		$this->Mission->commit();

		$res = array('success');
		
		$json_str = json_encode($res);//JSONに変換
		
		return $json_str;
	}
	

	
	
	/**
	 * CSVインポート | AJAX
	 *
	 * @note
	 *
	 */
	public function csv_fu(){
		$this->autoRender = false;//ビュー(ctp)を使わない。
		
		$this->csv_fu_base($this->Mission,array('id','mission_val','mission_name','mission_date','mission_group','mission_dt','note','sort_no'));
		
	}
	



	
	



	/**
	 * CSVダウンロード
	 *
	 * 一覧画面のCSVダウンロードボタンを押したとき、一覧データをCSVファイルとしてダウンロードします。
	 */
	public function csv_download(){
		$this->autoRender = false;//ビューを使わない。
	
		//ダウンロード用のデータを取得する。
		$data = $this->getDataForDownload();
		
		
		// ユーザーエージェントなど特定の項目をダブルクォートで囲む
		foreach($data as $i=>$ent){
			if(!empty($ent['user_agent'])){
				$data[$i]['user_agent']='"'.$ent['user_agent'].'"';
			}
		}

		
		
		//列名配列を取得
		$clms=array_keys($data[0]);
	
		//データの先頭行に列名配列を挿入
		array_unshift($data,$clms);
	
	
		//CSVファイル名を作成
		$date = new DateTime();
		$strDate=$date->format("Y-m-d");
		$fn='mission'.$strDate.'.csv';
	
	
		//CSVダウンロード
		App::uses('CsvDownloader','Vendor/Wacg');
		$csv= new CsvDownloader();
		$csv->output($fn, $data);
		 
	
	
	}
	
	

	
	
	//ダウンロード用のデータを取得する。
	private function getDataForDownload(){
		 
		
        //セッションから検索条件情報を取得
        $kjs=$this->Session->read('mission_kjs');
        
        // セッションからページネーション情報を取得
        $pages = $this->Session->read('mission_pages');

        $page_no = 0;
        $row_limit = 100000;
        $sort_field = $pages['sort_field'];
        $sort_desc = $pages['sort_desc'];

		//DBからデータ取得
	   $data=$this->Mission->findData($kjs,$page_no,$row_limit,$sort_field,$sort_desc);
		if(empty($data)){
			return array();
		}
	
		return $data;
	}
	

	/**
	 * 当画面系の共通セット
	 */
	private function setCommon(){

		
		// 新バージョンであるかチェックする。
		$new_version_flg = $this->checkNewPageVersion($this->this_page_version);
		
		$this->set(array(
				'header' => 'header_demo',
				'new_version_flg' => $new_version_flg, // 当ページの新バージョンフラグ   0:バージョン変更なし  1:新バージョン
				'this_page_version' => $this->this_page_version,// 当ページのバージョン
		));
	}
	

	/**
	 * CrudBase用の初期化処理
	 *
	 * @note
	 * フィールド関連の定義をする。
	 *
	 *
	 */
	private function initCrudBase(){

		/// 検索条件情報の定義
		$this->kensakuJoken=array(
		
		    array('name'=>'kj_id','def'=>null),
			array('name'=>'kj_mission_name','def'=>null),
			array('name'=>'kj_hina_file_id','def'=>null),
		    array('name'=>'kj_from_path','def'=>null),
		    array('name'=>'kj_from_scr_code','def'=>null),
		    array('name'=>'kj_from_db_name','def'=>null),
		    array('name'=>'kj_from_tbl_name','def'=>null),
		    array('name'=>'kj_from_wamei','def'=>null),
		    array('name'=>'kj_to_path','def'=>null),
		    array('name'=>'kj_to_scr_code','def'=>null),
		    array('name'=>'kj_to_db_name','def'=>null),
		    array('name'=>'kj_to_tbl_name','def'=>null),
		    array('name'=>'kj_to_wamei','def'=>null),
			array('name'=>'kj_sort_no','def'=>null),
			array('name'=>'kj_delete_flg','def'=>0),
			array('name'=>'kj_update_user','def'=>null),
			array('name'=>'kj_ip_addr','def'=>null),
			array('name'=>'kj_created','def'=>null),
			array('name'=>'kj_modified','def'=>null),
			array('name'=>'row_limit','def'=>50),
					
		);
		
		
		
		
		/// 検索条件のバリデーション
		$this->kjs_validate=array(
		
			'kj_id' => array(
					'naturalNumber'=>array(
							'rule' => array('naturalNumber', true),
							'message' => 'IDは数値を入力してください',
							'allowEmpty' => true
					),
			),
					
		    'kj_mission_name'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 255),
		            'message' => '任務名は255文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_from_path'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 1024),
		            'message' => '複製元パスは1024文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_from_scr_code'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 64),
		            'message' => '複製元画面コードは64文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_from_db_name'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 64),
		            'message' => '複製元DB名は64文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_from_tbl_name'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 64),
		            'message' => '複製元テーブル名は64文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_from_wamei'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 256),
		            'message' => '複製元和名は256文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_to_path'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 1024),
		            'message' => '複製先パスは1024文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_to_scr_code'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 64),
		            'message' => '複製先画面コードは64文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_to_db_name'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 64),
		            'message' => '複製先DB名は64文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_to_tbl_name'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 64),
		            'message' => '複製先テーブル名は64文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
		    'kj_to_wamei'=> array(
		        'maxLength'=>array(
		            'rule' => array('maxLength', 256),
		            'message' => '複製先和名は256文字以内で入力してください',
		            'allowEmpty' => true
		        ),
		    ),
			
			'kj_sort_no' => array(
				'custom'=>array(
					'rule' => array( 'custom', '/^[-]?[0-9]+?$/' ),
					'message' => '順番は整数を入力してください。',
					'allowEmpty' => true
				),
			),
				
			'kj_update_user'=> array(
					'maxLength'=>array(
							'rule' => array('maxLength', 50),
							'message' => '更新者は50文字以内で入力してください',
							'allowEmpty' => true
					),
			),
				
			'kj_ip_addr'=> array(
					'maxLength'=>array(
							'rule' => array('maxLength', 40),
							'message' => '更新IPアドレスは40文字以内で入力してください',
							'allowEmpty' => true
					),
			),
				
			'kj_created'=> array(
					'maxLength'=>array(
							'rule' => array('maxLength', 20),
							'message' => '生成日時は20文字以内で入力してください',
							'allowEmpty' => true
					),
			),
				
			'kj_modified'=> array(
					'maxLength'=>array(
							'rule' => array('maxLength', 20),
							'message' => '更新日時は20文字以内で入力してください',
							'allowEmpty' => true
					),
			),
		);
		
		
		
		
		
		///フィールドデータ
		$this->field_data = array('def'=>array(
		
			'id'=>array(
					'name'=>'ID',//HTMLテーブルの列名
					'row_order'=>'Mission.id',//SQLでの並び替えコード
					'clm_show'=>1,//デフォルト列表示 0:非表示 1:表示
			),
		    'mission_name'=>array(
		        'name'=>'任務名',
		        'row_order'=>'Mission.mission_name',
		        'clm_show'=>1,
		    ),
			'hina_file_id'=>array(
				'name'=>'雛ファイルID',
				'row_order'=>'Mission.hina_file_id',
				'clm_show'=>1,
			),
		    'from_path'=>array(
		        'name'=>'複製元パス',
		        'row_order'=>'Mission.from_path',
		        'clm_show'=>0,
		    ),
		    'from_scr_code'=>array(
		        'name'=>'複製元画面コード',
		        'row_order'=>'Mission.from_scr_code',
		        'clm_show'=>1,
		    ),
		    'from_db_name'=>array(
		        'name'=>'複製元DB名',
		        'row_order'=>'Mission.from_db_name',
		        'clm_show'=>1,
		    ),
		    'from_tbl_name'=>array(
		        'name'=>'複製元テーブル名',
		        'row_order'=>'Mission.from_tbl_name',
		        'clm_show'=>1,
		    ),
		    'from_wamei'=>array(
		        'name'=>'複製元和名',
		        'row_order'=>'Mission.from_wamei',
		        'clm_show'=>1,
		    ),
		    'to_path'=>array(
		        'name'=>'複製先パス',
		        'row_order'=>'Mission.to_path',
		        'clm_show'=>0,
		    ),
		    'to_scr_code'=>array(
		        'name'=>'複製先画面コード',
		        'row_order'=>'Mission.to_scr_code',
		        'clm_show'=>1,
		    ),
		    'to_db_name'=>array(
		        'name'=>'複製先DB名',
		        'row_order'=>'Mission.to_db_name',
		        'clm_show'=>1,
		    ),
		    'to_tbl_name'=>array(
		        'name'=>'複製先テーブル名',
		        'row_order'=>'Mission.to_tbl_name',
		        'clm_show'=>1,
		    ),
		    'to_wamei'=>array(
		        'name'=>'複製先和名',
		        'row_order'=>'Mission.to_wamei',
		        'clm_show'=>1,
		    ),
			'sort_no'=>array(
				'name'=>'順番',
				'row_order'=>'Mission.sort_no',
				'clm_show'=>0,
			),
			'delete_flg'=>array(
					'name'=>'削除フラグ',
					'row_order'=>'Mission.delete_flg',
					'clm_show'=>1,
			),
			'update_user'=>array(
					'name'=>'更新者',
					'row_order'=>'Mission.update_user',
					'clm_show'=>0,
			),
			'ip_addr'=>array(
					'name'=>'更新IPアドレス',
					'row_order'=>'Mission.ip_addr',
					'clm_show'=>0,
			),
			'created'=>array(
					'name'=>'生成日時',
					'row_order'=>'Mission.created',
					'clm_show'=>0,
			),
			'modified'=>array(
					'name'=>'更新日時',
					'row_order'=>'Mission.modified',
					'clm_show'=>1,
			),
		));

		// 列並び順をセットする
		$clm_sort_no = 0;
		foreach ($this->field_data['def'] as &$fEnt){
			$fEnt['clm_sort_no'] = $clm_sort_no;
			$clm_sort_no ++;
		}
		unset($fEnt);


	}


}