<?php

namespace App\Model;

use App\Model\CrudBase;


class Neko extends CrudBase
{
	
	/**
	 * The attributes that are mass assignable.
	 * DB保存時、ここで定義してあるDBフィールドのみ保存対象にします。
	 * ここの存在しないDBフィールドは保存対象外になりますのでご注意ください。
	 *
	 * @var array<int, string>
	 */
	protected $fillable = [
			// CBBXS-3009
			'id',
			'neko_val',
			'neko_name',
			'neko_date',
			'neko_type',
			'neko_dt',
			'neko_flg',
			'img_fn',
			'note',
			'sort_no',
			'delete_flg',
			'update_user_id',
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
				'id' => [], // ID
				'neko_val' => [], // ネコ数値
				'neko_name' => [], // ネコ名
				'neko_date' => [], // ネコ日付
				'neko_type' => [ // 猫種別
					'outer_table' => 'neko_types',
					'outer_field' => 'neko_type_name', 
					'outer_list'=>'nekoTypeList',
				],
				'neko_dt' => [], // ネコ日時
				'neko_flg' => [
						'value_type'=>'flg',
				], // ネコフラグ
				'img_fn' => [], // 画像ファイル名
				'note' => [], // 備考
				'sort_no' => [], // 順番
				'delete_flg' => [
						'value_type'=>'delete_flg',
				], // 無効フラグ
				'update_user_id' => [], // 更新ユーザーID
				'ip_addr' => [], // IPアドレス
				'created_at' => [], // 生成日時
				'updated_at' => [], // 更新日
				'update_user' => [], // 更新者
		];
		
		// フィールドデータへＤＢからのフィールド詳細情報を追加
		$fieldData = $this->addFieldDetailsFromDB($fieldData, 'nekos');
		
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
			$whereList[] = "CONCAT( IFNULL(nekos.neko_name, '') , IFNULL(nekos.note, '') ) LIKE '%{$searches['main_search']}%'";
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
				nekos.id as id,
				nekos.neko_val as neko_val,
				nekos.neko_name as neko_name,
				nekos.neko_date as neko_date,
				nekos.neko_type as neko_type,
				nekos.neko_dt as neko_dt,
				nekos.neko_flg as neko_flg,
				nekos.img_fn as img_fn,
				nekos.note as note,
				nekos.sort_no as sort_no,
				nekos.delete_flg as delete_flg,
				nekos.update_user_id as update_user_id,
				users.username as update_user,
				nekos.ip_addr as ip_addr,
				nekos.created_at as created_at,
				nekos.updated_at as updated_at
			FROM
				nekos 
			LEFT JOIN users ON nekos.update_user_id = users.id
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
			$whereList[] = "nekos.`id` = {$searches['id']}";
		}

		// neko_val
		if(!empty($searches['neko_val'])){
			$whereList[] = "nekos.`neko_val` = {$searches['neko_val']}";
		}

		// neko_name
		if(!empty($searches['neko_name'])){
			$whereList[] = "nekos.`neko_name` LIKE '%{$searches['neko_name']}%'";
		}

		// neko_date
		if(!empty($searches['neko_date'])){
			$whereList[] = "nekos.`neko_date` = '{$searches['neko_date']}'";
		}

		// 猫種別
		if(!empty($searches['neko_type'])){
			$whereList[] = "nekos.`neko_type` = {$searches['neko_type']}";
		}

		// ネコ日付・範囲1
		if(!empty($searches['neko_date1'])){
			$whereList[]="nekos.neko_date >= '{$searches['neko_date1']}'";
		}
		
		// ネコ日付・範囲2
		if(!empty($searches['neko_date2'])){
			$whereList[]="nekos.neko_date <= '{$searches['neko_date2']}'";
		}

		// 画像ファイル名
		if(!empty($searches['img_fn'])){
			$whereList[] = "nekos.`img_fn` LIKE '%{$searches['img_fn']}%'";
		}

		// 備考
		if(!empty($searches['note'])){
			$whereList[] = "nekos.`note` LIKE '%{$searches['note']}%'";
		}

		// 順番
		if(!empty($searches['sort_no'])){
			$whereList[] = "nekos.`sort_no` = {$searches['sort_no']}";
		}
		
		// 無効フラグ
		if(!empty($searches['delete_flg'])){
			$whereList[] = "nekos.`delete_flg` = {$searches['delete_flg']}";
		}else{
			$whereList[] = "nekos.`delete_flg` = 0";
		}

		// 更新者
		if(!empty($searches['update_user'])){
			$whereList[] = "users.`username` = `{$searches['update_user']}`";
		}

		// IPアドレス
		if(!empty($searches['ip_addr'])){
			$whereList[] = "nekos.`ip_addr` LIKE '%{$searches['ip_addr']}%'";
		}

		// 生成日時
		if(!empty($searches['created_at'])){
			$whereList[] = "nekos.`created_at` >= '{$searches['created_at']}'";
		}

		// 更新日
		if(!empty($searches['updated_at'])){
			$whereList[] = "nekos.`updated_at` >= '{$searches['updated_at']}'";
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
		$sql = "SELECT * FROM nekos WHERE `id`={$id}";
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
		$rs = $this->saveAll('nekos', $data);
		
		return $rs;
		
	}
	
	
	// CBBXS-3021
	/**
	 *  ネコ種別リストを取得する
	 *  @return [] ネコ種別リスト
	 */
	public function getNekoTypeList(){
		
		$sql = "
			SELECT `id`, `neko_type_name`
			FROM neko_types
			WHERE `delete_flg` = 0;
		";
		
		$res = $this->query($sql);
		
		$list = [];
		foreach($res as $ent){
			$id = $ent['id'];
			$list[$id] = $ent['neko_type_name'];
		}

		return $list;
	}
	// CBBXE
	
	
	

}

