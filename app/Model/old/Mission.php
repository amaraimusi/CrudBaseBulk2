<?php
App::uses('Model', 'Model');
App::uses('CrudBase', 'Model');

/**
 * 任務のモデルクラス
 *
 * 任務画面用のDB関連メソッドを定義しています。
 * 任務テーブルと関連付けられています。
 *
 * @date 2015-9-16	新規作成
 * @author k-uehara
 *
 */
class Mission extends AppModel {


	/// 任務テーブルを関連付け
	public $name='Mission';


	/// バリデーションはコントローラクラスで定義
	public $validate = null;
	
	
	public function __construct() {
		parent::__construct();
		
		// CrudBaseロジッククラスの生成
		if(empty($this->CrudBase)) $this->CrudBase = new CrudBase();
	}
	
	/**
	 * 任務エンティティを取得
	 *
	 * 任務テーブルからidに紐づくエンティティを取得します。
	 *
	 * @param int $id 任務ID
	 * @return array 任務エンティティ
	 */
	public function findEntity($id){

		$conditions='id = '.$id;

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
						'conditions' => $conditions,
				)
		);

		$ent=array();
		if(!empty($data)){
			$ent=$data['Mission'];
		}
		



		return $ent;
	}

	/**
	 * 任務画面の一覧に表示するデータを、任務テーブルから取得します。
	 * 
	 * @note
	 * 検索条件、ページ番号、表示件数、ソート情報からDB（任務テーブル）を検索し、
	 * 一覧に表示するデータを取得します。
	 * 
	 * @param array $kjs 検索条件情報
	 * @param int $page_no ページ番号
	 * @param int $row_limit 表示件数
	 * @param string sort ソートフィールド
	 * @param int sort_desc ソートタイプ 0:昇順 , 1:降順
	 * @return array 任務画面一覧のデータ
	 */
	public function findData($kjs,$page_no,$row_limit,$sort_field,$sort_desc){

		//条件を作成
		$conditions=$this->createKjConditions($kjs);
		
		// オフセットの組み立て
		$offset=null;
		if(!empty($row_limit)) $offset = $page_no * $row_limit;
		
		// ORDER文の組み立て
		$order = $sort_field;
		if(empty($order)) $order='sort_no';
		if(!empty($sort_desc)) $order .= ' DESC';
		
		$option=array(
            'conditions' => $conditions,
            'limit' =>$row_limit,
            'offset'=>$offset,
            'order' => $order,
        );
		
		//DBからデータを取得
		$data = $this->find('all',$option);

		//データ構造を変換（2次元配列化）
		$data2=array();
		foreach($data as $i=>$tbl){
			foreach($tbl as $ent){
				foreach($ent as $key => $v){
					$data2[$i][$key]=$v;
				}
			}
		}
		
		return $data2;
	}
	
	
	/**
	 * 一覧データを取得する
	 */
	public function findData2(&$crudBaseData){

		$kjs = $crudBaseData['kjs'];//検索条件情報
		$pages = $crudBaseData['pages'];//ページネーション情報

		$data = $this->findData($kjs,$pages['page_no'],$pages['row_limit'],$pages['sort_field'],$pages['sort_desc']);
		
		return $data;
	}

	
	
	/**
	 * SQLのダンプ
	 * @param  $option
	 */
	private function dumpSql($option){
		$dbo = $this->getDataSource();
		
		$option['table']=$dbo->fullTableName($this->Mission);
		$option['alias']='Mission';
		
		$query = $dbo->buildStatement($option,$this->Mission);
		
		Debugger::dump($query);
	}



	/**
	 * 検索条件情報からWHERE情報を作成。
	 * @param array $kjs	検索条件情報
	 * @return string WHERE情報
	 */
	private function createKjConditions($kjs){

		$cnds=null;
		
		$this->CrudBase->sql_sanitize($kjs); // SQLサニタイズ
		
		// --- Start kjConditions
		
		if(!empty($kjs['kj_id'])){
			$cnds[]="Mission.id = {$kjs['kj_id']}";
		}
		
		if(!empty($kjs['kj_mission_name'])){
		    $cnds[]="Mission.mission_name LIKE '%{$kjs['kj_mission_name']}%'";
		}
		if(!empty($kjs['kj_hina_file_id'])){
			$cnds[]="Mission.hina_file_id = '{$kjs['kj_hina_file_id']}'";
		}
		if(!empty($kjs['kj_from_path'])){
		    $cnds[]="Mission.from_path LIKE '%{$kjs['kj_from_path']}%'";
		}
		if(!empty($kjs['kj_from_scr_code'])){
		    $cnds[]="Mission.from_scr_code LIKE '%{$kjs['kj_from_scr_code']}%'";
		}
		if(!empty($kjs['kj_from_db_name'])){
		    $cnds[]="Mission.from_db_name LIKE '%{$kjs['kj_from_db_name']}%'";
		}
		if(!empty($kjs['kj_from_tbl_name'])){
		    $cnds[]="Mission.from_tbl_name LIKE '%{$kjs['kj_from_tbl_name']}%'";
		}
		if(!empty($kjs['kj_from_wamei'])){
		    $cnds[]="Mission.from_wamei LIKE '%{$kjs['kj_from_wamei']}%'";
		}
		if(!empty($kjs['kj_to_path'])){
		    $cnds[]="Mission.to_path LIKE '%{$kjs['kj_to_path']}%'";
		}
		if(!empty($kjs['kj_to_scr_code'])){
		    $cnds[]="Mission.to_scr_code LIKE '%{$kjs['kj_to_scr_code']}%'";
		}
		if(!empty($kjs['kj_to_db_name'])){
		    $cnds[]="Mission.to_db_name LIKE '%{$kjs['kj_to_db_name']}%'";
		}
		if(!empty($kjs['kj_to_tbl_name'])){
		    $cnds[]="Mission.to_tbl_name LIKE '%{$kjs['kj_to_tbl_name']}%'";
		}
		if(!empty($kjs['kj_to_wamei'])){
		    $cnds[]="Mission.to_wamei LIKE '%{$kjs['kj_to_wamei']}%'";
		}
		
		if(!empty($kjs['kj_sort_no']) || $kjs['kj_sort_no'] ==='0' || $kjs['kj_sort_no'] ===0){
			$cnds[]="Mission.sort_no = {$kjs['kj_sort_no']}";
		}
		
		$kj_delete_flg = $kjs['kj_delete_flg'];
		if(!empty($kjs['kj_delete_flg']) || $kjs['kj_delete_flg'] ==='0' || $kjs['kj_delete_flg'] ===0){
			if($kjs['kj_delete_flg'] != -1){
			   $cnds[]="Mission.delete_flg = {$kjs['kj_delete_flg']}";
			}
		}

		if(!empty($kjs['kj_update_user'])){
			$cnds[]="Mission.update_user = '{$kjs['kj_update_user']}'";
		}

		if(!empty($kjs['kj_ip_addr'])){
			$cnds[]="Mission.ip_addr = '{$kjs['kj_ip_addr']}'";
		}
		
		if(!empty($kjs['kj_user_agent'])){
			$cnds[]="Mission.user_agent LIKE '%{$kjs['kj_user_agent']}%'";
		}

		if(!empty($kjs['kj_created'])){
			$kj_created=$kjs['kj_created'].' 00:00:00';
			$cnds[]="Mission.created >= '{$kj_created}'";
		}
		
		if(!empty($kjs['kj_modified'])){
			$kj_modified=$kjs['kj_modified'].' 00:00:00';
			$cnds[]="Mission.modified >= '{$kj_modified}'";
		}
		
		// --- End kjConditions
		
		$cnd=null;
		if(!empty($cnds)){
			$cnd=implode(' AND ',$cnds);
		}

		return $cnd;

	}

	/**
	 * エンティティをDB保存
	 *
	 * 任務エンティティを任務テーブルに保存します。
	 *
	 * @param array $ent 任務エンティティ
	 * @param array $option オプション
	 * @return array 任務エンティティ（saveメソッドのレスポンス）
	 */
	public function saveEntity($ent,$option=array()){
		
		
		// 新規入力であるなら新しい順番をエンティティにセットする。
		if($option['form_type']=='new_inp' ){
			if(empty($option['ni_tr_place'])){
				$ent['sort_no'] = $this->CrudBase->getLastSortNo($this); // 末尾順番を取得する
			}else{
				$ent['sort_no'] = $this->CrudBase->getFirstSortNo($this); // 先頭順番を取得する
			}
		}

		//DBに登録('atomic' => false　トランザクションなし）
		$ent = $this->save($ent, array('atomic' => false,'validate'=>false));

		//DBからエンティティを取得
		$ent = $this->find('first',
				array(
						'conditions' => "id={$ent['Mission']['id']}"
				));

		$ent=$ent['Mission'];
		if(empty($ent['delete_flg'])) $ent['delete_flg'] = 0;

		return $ent;
	}




	/**
	 * 全データ件数を取得
	 *
	 * limitによる制限をとりはらった、検索条件に紐づく件数を取得します。
	 *  全データ件数はページネーション生成のために使われています。
	 *
	 * @param array $kjs 検索条件情報
	 * @return int 全データ件数
	 */
	public function findDataCnt($kjs){

		//DBから取得するフィールド
		$fields=array('COUNT(id) AS cnt');
		$conditions=$this->createKjConditions($kjs);

		//DBからデータを取得
		$data = $this->find(
				'first',
				Array(
						'fields'=>$fields,
						'conditions' => $conditions,
				)
		);

		$cnt=$data[0]['cnt'];
		return $cnt;
	}
	
	
	
	/**
	 * 雛ファイルリストを取得する
	 */
	public function getHinaFileList(){
		if(empty($this->HinaFile)){
			App::uses('HinaFile','Model');
			$this->HinaFile=ClassRegistry::init('HinaFile');
		}
		
		//SELECT情報
		$fields=array(
				'id',
				'hina_file_name',
		);
		
		//WHERE情報
		$conditions=array("delete_flg = 0");
		
		//ORDER情報
		$order=array('sort_no');
		
		//オプション
		$option=array(
				'fields'=>$fields,
				'conditions'=>$conditions,
				'order'=>$order,
		);
		
		//DBから取得
		$data=$this->HinaFile->find('all',$option);
		
		//2次元配列に構造変換する。
		if(!empty($data)){
			$data=Hash::combine($data, '{n}.HinaFile.id','{n}.HinaFile.hina_file_name');
		}
		
		return $data;
	}
	


}