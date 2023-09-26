<?php

namespace App\Model;

use App\Model\CrudBase;


class Mission extends CrudBase
{
	
	/**
	 * The attributes that are mass assignable.
	 * DB保存時、ここで定義してあるDBフィールドのみ保存対象にします。
	 * ここの存在しないDBフィールドは保存対象外になりますのでご注意ください。
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
			// CBBXS-5009
			'id',
			'mission_name',
			'hina_file_id',
			'from_path',
			'from_scr_code',
			'from_db_name',
			'from_tbl_name',
			'from_wamei',
			'to_path',
			'to_scr_code',
			'to_db_name',
			'to_tbl_name',
			'to_wamei',
			'sort_no',
			'delete_flg',
			'update_user',
			'ip_addr',
			'created',
			'modified',

			// CBBXE
	];
	
	
	public function __construct(){
		parent::__construct();
		
	}
	
	
	/**
	 * フィールドデータを取得する
	 * @return [] $fieldData フィールドデータ
	 */
	public function getFieldData(){
		
		$fieldData = [
				// CBBXS-5014
				'id' => [], // id
				'mission_name' => [], // 任務名
				'hina_file_id' => [ // 猫種別
					'outer_table' => 'hina_files',
					'outer_field' => 'hina_file_name', 
					'outer_list'=>'hinaFile	List',
				],
				'from_path' => [], // 複製元パス
				'from_scr_code' => [], // 複製元画面コード
				'from_db_name' => [], // 複製元DB名
				'from_tbl_name' => [], // 複製元テーブル名
				'from_wamei' => [], // 複製元和名
				'to_path' => [], // 複製先パス
				'to_scr_code' => [], // 複製先画面コード
				'to_db_name' => [], // 複製先DB名
				'to_tbl_name' => [], // 複製先テーブル名
				'to_wamei' => [], // 複製先和名
				'sort_no' => [], // 順番
				'delete_flg' => [
						'value_type'=>'delete_flg',
				], // 無効フラグ
				'update_user' => [], // 更新者
				'ip_addr' => [], // IPアドレス
				'created' => [], // 生成日時
				'modified' => [], // 更新日

				// CBBXE
		];
		
		// フィールドデータへＤＢからのフィールド詳細情報を追加
		$fieldData = $this->addFieldDetailsFromDB($fieldData, 'missions');
		
		// フィールドデータに登録対象フラグを追加します。
		$fieldData = $this->addRegFlgToFieldData($fieldData, $this->fillable);

		return $fieldData;
	}
	
	
	/**
	 * DBから一覧データを取得する
	 * @param [] $searches 検索データ
	 * @param [] $param
	 *     - string use_type 用途タイプ 　index:一覧データ用（デフォルト）, csv:CSVダウンロード用
	 *     - int def_per_page  デフォルト制限行数
	 * @return [] 一覧データ
	 */
	public function getData($searches, $param=[]){
		
		$searches = $this->sqlSanitizeW($searches);
		
		$use_type = $param['use_type'] ?? 'index';
		$def_per_page = $param['def_per_page'] ?? 50;
		
		// 検索条件リストを作成
		$whereList = $this->makeWhereList($searches);
		
		// メイン検索
		if(!empty($searches['main_search'])){
			$whereList[] = "CONCAT( IFNULL(missions.mission_name, '') , IFNULL(missions.note, '') ) LIKE '%{$searches['main_search']}%'";
		}
		
		// 検索条件リストを連結する
		$where = implode(" AND ", $whereList);
		
		$sort_field = $searches['sort'] ?? 'sort_no'; // 並びフィールド
		$dire = 'asc'; // 並び向き
		if(!empty($searches['desc'])){
			$dire = 'desc';
		}
		$order = $sort_field . ' ' . $dire;
		
		// 一覧用のデータ取得。ページネーションを考慮している。
		$limit = '';
		if($use_type == 'index'){
			
			$per_page = $searches['per_page'] ?? $def_per_page; // 行制限数(一覧の最大行数) デフォルトは50行まで。
			$page =  $searches['page'] ?? 0;
			$offset = $per_page  * ($page - 1);
			$limit =" LIMIT {$per_page} OFFSET $offset" ;
			
		}

		$sql = "
			SELECT SQL_CALC_FOUND_ROWS 
				missions.id as id,
				missions.mission_val as mission_val,
				missions.mission_name as mission_name,
				missions.mission_date as mission_date,
				missions.mission_type as mission_type,
				missions.mission_dt as mission_dt,
				missions.mission_flg as mission_flg,
				missions.img_fn as img_fn,
				missions.note as note,
				missions.sort_no as sort_no,
				missions.delete_flg as delete_flg,
				missions.update_user_id as update_user_id,
				users.username as update_user,
				missions.ip_addr as ip_addr,
				missions.created_at as created_at,
				missions.updated_at as updated_at
			FROM
				missions 
			LEFT JOIN users ON missions.update_user_id = users.id
			WHERE 
				{$where}
			ORDER BY {$order}
			{$limit}
		";
			
		$data = $this->query($sql); // DBから一覧データを取得する
		
		// LIMIT制限を受けていないデータ件数を取得する
		$res = $this->query("SELECT FOUND_ROWS();");
		$total = 0;
		if(!empty($res)){
			$total = $res[0]['FOUND_ROWS()'];
		}

		return [
				'data' => $data,
				'total' => $total,
		];

	}
	
	/**
	 * 検索条件リストを作成
	 * @param [] $searches　検索データ
	 * @return [] 検索条件リスト
	 */
	private function makeWhereList($searches){
		
		$whereList = [];
		
		// SQLインジェクションのサニタイズ
		$searches = $this->sqlSanitizeW($searches);
		
		// CBBXS-3003

	    // id
	    if(!empty($searches['id'])){
	        $query = $query->where('missions.id',$searches['id']);
	    }

	    // 任務名
	    if(!empty($searches['mission_name'])){
	        $query = $query->where('missions.mission_name', 'LIKE', "%{$searches['mission_name']}%");
	    }

	    // 雛ファイルID
	    if(!empty($searches['hina_file_id'])){
	        $query = $query->where('missions.hina_file_id',$searches['hina_file_id']);
	    }

	    // 複製元パス
	    if(!empty($searches['from_path'])){
	        $query = $query->where('missions.from_path', 'LIKE', "%{$searches['from_path']}%");
	    }

	    // 複製元画面コード
	    if(!empty($searches['from_scr_code'])){
	        $query = $query->where('missions.from_scr_code', 'LIKE', "%{$searches['from_scr_code']}%");
	    }

	    // 複製元DB名
	    if(!empty($searches['from_db_name'])){
	        $query = $query->where('missions.from_db_name', 'LIKE', "%{$searches['from_db_name']}%");
	    }

	    // 複製元テーブル名
	    if(!empty($searches['from_tbl_name'])){
	        $query = $query->where('missions.from_tbl_name', 'LIKE', "%{$searches['from_tbl_name']}%");
	    }

	    // 複製元和名
	    if(!empty($searches['from_wamei'])){
	        $query = $query->where('missions.from_wamei', 'LIKE', "%{$searches['from_wamei']}%");
	    }

	    // 複製先パス
	    if(!empty($searches['to_path'])){
	        $query = $query->where('missions.to_path', 'LIKE', "%{$searches['to_path']}%");
	    }

	    // 複製先画面コード
	    if(!empty($searches['to_scr_code'])){
	        $query = $query->where('missions.to_scr_code', 'LIKE', "%{$searches['to_scr_code']}%");
	    }

	    // 複製先DB名
	    if(!empty($searches['to_db_name'])){
	        $query = $query->where('missions.to_db_name', 'LIKE', "%{$searches['to_db_name']}%");
	    }

	    // 複製先テーブル名
	    if(!empty($searches['to_tbl_name'])){
	        $query = $query->where('missions.to_tbl_name', 'LIKE', "%{$searches['to_tbl_name']}%");
	    }

	    // 複製先和名
	    if(!empty($searches['to_wamei'])){
	        $query = $query->where('missions.to_wamei', 'LIKE', "%{$searches['to_wamei']}%");
	    }

	    // 順番
	    if(!empty($searches['sort_no'])){
	        $query = $query->where('missions.sort_no',$searches['sort_no']);
	    }

	    // 無効フラグ
	    if(!empty($searches['delete_flg'])){
	        $query = $query->where('missions.delete_flg',$searches['delete_flg']);
	    }else{
	        $query = $query->where('missions.delete_flg', 0);
	    }

	    // 更新者
	    if(!empty($searches['update_user'])){
	        $query = $query->where('missions.update_user', 'LIKE', "%{$searches['update_user']}%");
	    }

	    // IPアドレス
	    if(!empty($searches['ip_addr'])){
	        $query = $query->where('missions.ip_addr', 'LIKE', "%{$searches['ip_addr']}%");
	    }

	    // 生成日時
	    if(!empty($searches['created'])){
	        $query = $query->where('missions.created',$searches['created']);
	    }

	    // 更新日
	    if(!empty($searches['modified'])){
	        $query = $query->where('missions.modified',$searches['modified']);
	    }

		// CBBXE
		
		return $whereList;
	}
	
	
	/**
	 * idに紐づくエンティティを取得する
	 * @param int id
	 * @return [] エンティティ
	 */
	public function getEntityById($id){
		if(empty($id)) return [];
		$sql = "SELECT * FROM missions WHERE `id`={$id}";
		$data = $this->query($sql);
		if(empty($data)) return [];
		return $data[0];
	}
	
	//■■■□□□■■■□□□
// 	/**
// 	 * 次の順番を取得する
// 	 * @return int 順番
// 	 */
// 	public function nextSortNo(){

// 		$res = $this->query("SELECT MAX(sort_no) AS max_sort_no;");
		
// 		if(empty($res)){
// 			return 0;
// 		}
		
// 		$sort_no = $res[0]['max_sort_no'];;
// 		$sort_no++;
		
// 		return $sort_no;
// 	}
	
	
// 	/**■■■□□□■■■□□□
// 	 * エンティティのDB保存
// 	 * @note エンティティのidが空ならINSERT, 空でないならUPDATEになる。
// 	 * @param [] $ent エンティティ
// 	 * @return [] エンティティ(insertされた場合、新idがセットされている）
// 	 */
// 	public function saveEntity(&$ent){
		
// 		if(empty($ent['id'])){
			
// 			// ▽ idが空であればINSERTをする。
// 			$ent = array_intersect_key($ent, array_flip($this->fillable)); // ホワイトリストによるフィルタリング
// 			$id = $this->insertGetId($ent); // INSERT
// 			$ent['id'] = $id;
// 		}else{
			
// 			// ▽ idが空でなければUPDATEする。
// 			$ent = array_intersect_key($ent, array_flip($this->fillable)); // ホワイトリストによるフィルタリング
// 			$this->updateOrCreate(['id'=>$ent['id']], $ent); // UPDATE
// 		}
		
// 		return $ent;
// 	}
	
	
// 	/**■■■□□□■■■□□□
// 	 * データのDB保存
// 	 * @param string $tbl_name テーブル名
// 	 * @param [] $data データ（エンティティの配列）
// 	 * @return [] データ(insertされた場合、新idがセットされている）
// 	 */
// 	public function saveAll($tbl_name, &$data){
		
// 		$data2 = [];
// 		foreach($data as &$ent){
// 			$data2[] = $this->save($tbl_name, $ent);
			
// 		}
// 		unset($ent);
// 		return $data2;
// 	}
	
	
	/**
	 * 削除フラグを切り替える
	 * @param array $ids IDリスト
	 * @param int $delete_flg 削除フラグ   0:有効  , 1:削除
	 * @param [] $userInfo ユーザー情報
	 */
	public function switchDeleteFlg($ids, $delete_flg, $userInfo){
		
		// IDリストと削除フラグからデータを作成する
		$data = [];
		foreach($ids as $id){
			$ent = [
					'id' => $id,
					'delete_flg' => $delete_flg,
			];
			$data[] = $ent;
			
		}
		
		// 更新ユーザーなど共通フィールドをデータにセットする。
		$data = $this->setCommonToData($data, $userInfo);

		// データを更新する
		$rs = $this->saveAll('missions', $data);
		
		return $rs;
		
	}
	
	
	// CBBXS-3021
	/**
	 *  雛ファイルID種別リストを取得する
	 *  @return [] 雛ファイルID種別リスト
	 */
	public function getHinaFileList(){
	    
	    $query = DB::table('hina_files')->
	       select(['id', 'hina_file_name'])->
	       where('delete_flg',0);
	    
	    $res = $query->get();
	    $list = [];
	    foreach($res as $ent){
	        $list[$ent->id] = $ent->hina_file_name;
	    }

	    return $list;
	}

	// CBBXE
	
	
	

}

