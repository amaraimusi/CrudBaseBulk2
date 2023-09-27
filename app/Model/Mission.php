<?php

namespace App\Model;

use App\Model\CrudBase;

/**
 * 任務管理画面のモデルクラス
 * @version 1.0.0
 * @since 2023-9-27
 * @author amaraimusi
 *
 */
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
			'update_user_id',
			'update_user',
			'ip_addr',
			'created_at',
			'updated_at',

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
					'outer_list'=>'hinaFileList',
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
				'update_user_id' => [], // 更新ユーザーID
				'update_user' => [], // 更新者
				'ip_addr' => [], // IPアドレス
				'created_at' => [], // 生成日時
				'updated_at' => [], // 更新日時

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
			$whereList[] = "
				CONCAT( 
					/* CBBXS-5017 */
					IFNULL(missions.mission_name, '') , 
					IFNULL(missions.from_path, '') , 
					IFNULL(missions.from_scr_code, '') , 
					IFNULL(missions.from_db_name, '') , 
					IFNULL(missions.from_tbl_name, '') , 
					IFNULL(missions.from_wamei, '') , 
					IFNULL(missions.to_path, '') , 
					IFNULL(missions.to_scr_code, '') , 
					IFNULL(missions.to_db_name, '') , 
					IFNULL(missions.to_tbl_name, '') , 
					IFNULL(missions.to_wamei, '') , 
					IFNULL(missions.update_user, '') , 
					IFNULL(missions.ip_addr, '') , 

					/* CBBXE */
					''
				 ) LIKE '%{$searches['main_search']}%'";
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
				/* CBBXS-5019 */
				missions.mission_name as mission_name,
				missions.hina_file_id as hina_file_id,
				missions.from_path as from_path,
				missions.from_scr_code as from_scr_code,
				missions.from_db_name as from_db_name,
				missions.from_tbl_name as from_tbl_name,
				missions.from_wamei as from_wamei,
				missions.to_path as to_path,
				missions.to_scr_code as to_scr_code,
				missions.to_db_name as to_db_name,
				missions.to_tbl_name as to_tbl_name,
				missions.to_wamei as to_wamei,

				/* CBBXE */
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
		

		// id
		if(!empty($searches['id'])){
			$whereList[] = "missions.`id` = {$searches['id']}";
		}
		
		// CBBXS-5024
		// id
		if(!empty($searches['id'])){
			$whereList[] = "missions.`id` = '{$searches['id']}'";
		}

		// 任務名
		if(!empty($searches['mission_name'])){
			$whereList[] = "missions.`mission_name` LIKE '%{$searches['mission_name']}%'";
		}

		// 雛ファイルID
		if(!empty($searches['hina_file_id'])){
			$whereList[] = "missions.`hina_file_id` = '{$searches['hina_file_id']}'";
		}

		// 複製元パス
		if(!empty($searches['from_path'])){
			$whereList[] = "missions.`from_path` LIKE '%{$searches['from_path']}%'";
		}

		// 複製元画面コード
		if(!empty($searches['from_scr_code'])){
			$whereList[] = "missions.`from_scr_code` LIKE '%{$searches['from_scr_code']}%'";
		}

		// 複製元DB名
		if(!empty($searches['from_db_name'])){
			$whereList[] = "missions.`from_db_name` LIKE '%{$searches['from_db_name']}%'";
		}

		// 複製元テーブル名
		if(!empty($searches['from_tbl_name'])){
			$whereList[] = "missions.`from_tbl_name` LIKE '%{$searches['from_tbl_name']}%'";
		}

		// 複製元和名
		if(!empty($searches['from_wamei'])){
			$whereList[] = "missions.`from_wamei` LIKE '%{$searches['from_wamei']}%'";
		}

		// 複製先パス
		if(!empty($searches['to_path'])){
			$whereList[] = "missions.`to_path` LIKE '%{$searches['to_path']}%'";
		}

		// 複製先画面コード
		if(!empty($searches['to_scr_code'])){
			$whereList[] = "missions.`to_scr_code` LIKE '%{$searches['to_scr_code']}%'";
		}

		// 複製先DB名
		if(!empty($searches['to_db_name'])){
			$whereList[] = "missions.`to_db_name` LIKE '%{$searches['to_db_name']}%'";
		}

		// 複製先テーブル名
		if(!empty($searches['to_tbl_name'])){
			$whereList[] = "missions.`to_tbl_name` LIKE '%{$searches['to_tbl_name']}%'";
		}

		// 複製先和名
		if(!empty($searches['to_wamei'])){
			$whereList[] = "missions.`to_wamei` LIKE '%{$searches['to_wamei']}%'";
		}

		// 順番
		if(!empty($searches['sort_no'])){
			$whereList[] = "missions.`sort_no` = '{$searches['sort_no']}'";
		}

		// 無効フラグ
		if(!empty($searches['delete_flg']) || $searches['delete_flg'] ==='0' || $searches['delete_flg'] ===0){
			if($searches['delete_flg'] != -1){
				$whereList[]="missions.delete_flg = {$searches['delete_flg']}";
			}
		}

		// 更新ユーザーID
		if(!empty($searches['update_user_id'])){
			$whereList[] = "missions.`update_user_id` = '{$searches['update_user_id']}'";
		}

		// 更新者
		if(!empty($searches['update_user'])){
			$whereList[] = "missions.`update_user` LIKE '%{$searches['update_user']}%'";
		}

		// IPアドレス
		if(!empty($searches['ip_addr'])){
			$whereList[] = "missions.`ip_addr` LIKE '%{$searches['ip_addr']}%'";
		}

		// 生成日時
		if(!empty($searches['created_at'])){
			$whereList[] = "missions.`created_at` >= '{$searches['created_at']}'";
		}
		
		// 更新日時
		if(!empty($searches['updated_at'])){
			$whereList[] = "missions.`updated_at` >= '{$searches['updated_at']}'";
		}
		

		// CBBXE

		// 順番
		if(!empty($searches['sort_no'])){
			$whereList[] = "missions.`sort_no` = {$searches['sort_no']}";
		}
		
		// 無効フラグ
		if(!empty($searches['delete_flg'])){
			$whereList[] = "missions.`delete_flg` = {$searches['delete_flg']}";
		}else{
			$whereList[] = "missions.`delete_flg` = 0";
		}
		
		// 更新者
		if(!empty($searches['update_user'])){
			$whereList[] = "users.`username` = `{$searches['update_user']}`";
		}

		// IPアドレス
		if(!empty($searches['ip_addr'])){
			$whereList[] = "missions.`ip_addr` LIKE '%{$searches['ip_addr']}%'";
		}

		// 生成日時
		if(!empty($searches['created_at'])){
			$whereList[] = "missions.`created_at` >= '{$searches['created_at']}'";
		}

		// 更新日
		if(!empty($searches['updated_at'])){
			$whereList[] = "missions.`updated_at` >= '{$searches['updated_at']}'";
		}

		
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
	
	
	// CBBXS-5029
	/**
	 *  雛ファイルIDリストを取得する
	 *  @return [] 雛ファイルIDリスト
	 */
	public function getHinaFileList(){
		
		$sql = "
			SELECT `id`, `hina_file_name`
			FROM hina_files
			WHERE `delete_flg` = 0;
		";
		
		$res = $this->query($sql);
		
		$list = [];
		foreach($res as $ent){
			$id = $ent['id'];
			$list[$id] = $ent['hina_file_name'];
		}

		return $list;
	}
	

	// CBBXE
	
	
	

}

