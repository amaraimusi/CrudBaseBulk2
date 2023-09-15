<?php
namespace App\Model;

use CrudBase\PdoDao;
use CrudBase\SaveData;

/**
 * モデルクラスのベースクラス
 * 
 * @desc 各管理画面のモデルで共通するメソッドを記述する。
 * @version 1.0.0
 * @since 2022-7-4
 * @author kenji uehara
 *
 */
class CrudBase{
	
	private $dao; // データベースアクセスオブジェクト
	private $saveData; // データ保存クラスオブジェクト
    
	public function __construct(){
		global $g_dao;
		$this->dao = $g_dao;
		$this->saveData = new SaveData($g_dao);
    	
    }
    
    
    /**
     * SQLクエリを実行する
     * @param string $sql
     * @return [] レスポンス
     */
    public function query($sql){
    	
    	global $g_dao;
    	return $g_dao->query($sql);

    }
    
    
    /**
     * SQLインジェクションサニタイズ
     * @param mixed $data 文字列および配列に対応
     * @return mixed サニタイズ後のデータ
     */
    public function sqlSanitizeW(&$data){
        $this->sql_sanitize($data);
        return $data;
    }
    
    
    /**
     * SQLインジェクションサニタイズ(配列用)
     *
     * @note
     * SQLインジェクション対策のためデータをサニタイズする。
     * 高速化のため、引数は参照（ポインタ）にしている。
     *
     * @param array サニタイズデコード対象のデータ
     * @return void
     */
    public function sql_sanitize(&$data){
        
        if(is_array($data)){
            foreach($data as &$val){
                $this->sql_sanitize($val);
            }
            unset($val);
        }elseif(gettype($data)=='string'){
            $data = $this->sqlSanitize($data);// SQLインジェクション のサニタイズ
        }else{
            // 何もしない
        }
    }

    
    /**
     * SQLサニタイズ(※なるべくこの関数にたよらずプリペアド方式を用いること）
     * @param string $text
     * @return string SQLサニタイズ後のテキスト
     */
    public function sqlSanitize($text) {
    	$text = trim($text);
    	
    	// 文字列がUTF-8でない場合、UTF-8に変換する
    	if(!mb_check_encoding($text, 'UTF-8')){
    		$text = str_replace(['\\', '/', '\'', '"', '`',' OR '], '', $text);
    		$text = mb_convert_encoding($text, 'UTF-8');
    	}
    	
    	// SQLインジェクションのための特殊文字をエスケープする
    	$search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a", "`");
    	$replace = array("\\\\", "\\0", "\\n", "\\r", "\\'", "\\\"", "\\Z", "");
    	
    	$text = str_replace($search, $replace, $text);
    	
    	return $text;
    }
    
    
    /**
     * フィールドデータへＤＢからのフィールド詳細情報を追加
     * @param [] $fieldData フィールドデータ
     * @param string $tbl_name DBテーブル名
     * @return [] フィールド詳細情報を追加したフィールドデータ
     */
    protected function addFieldDetailsFromDB(&$fieldData, $tbl_name){
    	
    	$fieldDataDb = $this->getFieldDataFromDb($tbl_name); // DBテーブルから各フィールドの詳細情報を取得します。
    	
    	foreach($fieldDataDb as $entD){
    		$field = $entD['Field'];
    		if (empty($fieldData[$field])) $fieldData[$field] = [];
    		$fEnt = $fieldData[$field];
    		
    		$fEnt['Field'] = $entD['Field'];
    		$fEnt['Type'] = $entD['Type'];
    		$fEnt['Collation'] = $entD['Collation'];
    		$fEnt['Null'] = $entD['Null'];
    		$fEnt['Key'] = $entD['Key'];
    		$fEnt['Extra'] = $entD['Extra'];
    		$fEnt['Privileges'] = $entD['Privileges'];
    		$fEnt['Comment'] = $entD['Comment'];
    		$fEnt['Default'] = $entD['Default'];
    		
    		$fieldData[$field] = $fEnt;
    	}
    	
    	// 型長とデータ型を取得する
    	foreach($fieldData as &$fEnt){
    		if(empty($fEnt['Type'])) continue;
    		$data_type = $fEnt['Type'];
    		
    		// 型長を取得する
    		$matches = null;
    		preg_match('/\d+/', $data_type, $matches);
    		$fEnt['long'] = $matches[0] ?? null;
    		
    		// データ型を取得する
    		$fEnt['type'] = preg_replace('/\([^)]+\)/', '', $data_type); // カッコとカッコ内の文字列を削除した文字列を取得する
    		
    		if($fEnt['Default'] == 'current_timestamp()'){
    			$fEnt['Default'] = null;
    		}
    		
    	}
    	unset($fEnt);

    	return $fieldData;
    }
   
    
    /**
     * DBテーブルから各フィールドの詳細情報を取得します。
     * @param string $tbl_name DBテーブル名
     * @return [] 各フィールドの詳細情報
     */
    protected function getFieldDataFromDb($tbl_name){
    	$sql="SHOW FULL COLUMNS FROM {$tbl_name}";
    	$res = $this->query($sql);

    	return $res;
    }
    
    
    /**
     * フィールドデータに登録対象フラグを追加します。
     * @param [] $fieldData フィールドデータ
     * @param [] $fillable 登録対象フィルタデータ
     */
    protected function addRegFlgToFieldData(&$fieldData, $fillable){
    	
    	foreach($fillable as $fill_field){
    		if(empty($fieldData[$fill_field])){
    			$fieldData[$fill_field]['reg_flg'] = 0;
    		}else{
    			$fieldData[$fill_field]['reg_flg'] = 1;
    		}
    	}
    	
    	return $fieldData;
    	
    }
    
    /**
     * 更新ユーザーなど共通フィールドをデータにセットする。
     * @param [] $data データ（エンティティの配列）
	 * @param [] $userInfo ユーザー情報
     * @return [] 共通フィールドセット後のデータ
     */
    public function setCommonToData($data, $userInfo){

    	$update_user_id = $userInfo['id'];
    	
    	// IPアドレス
    	$ip_addr = $_SERVER["REMOTE_ADDR"];
    	
    	// 本日
    	$today = date('Y-m-d H:i:s');
    	
    	// データにセットする
    	foreach($data as $i => $ent){
    		
    		$ent['update_user_id'] = $update_user_id;
    		$ent['ip_addr'] = $ip_addr;
    		
    		// idが空（新規入力）なら生成日をセットし、空でないなら除去
    		if(empty($ent['id'])){
    			$ent['created_at'] = $today;
    		}else{
    			unset($ent['created_at']);
    		}
    		
    		$ent['updated_at'] = $today;
    		
    		$data[$i] = $ent;
    	}
    	
    	return $data;
    	
    }
    
    
    /**
     * DaoオブジェクトのGetter
     * @return  IDao Daoオブジェクト
     */
    public function getDao(){
    	return $this->dao;
    }
    
    
    /**
     * エンティティをDBの保存する。 idが存在すればUPDATE,　idが空であればINSERTになる。
     * @param string $tbl_name テーブル名
     * @param [] $ent エンティティ
	 * @return []
	 *   - ent エンティティ(INSERTの場合、idがセットされる）
	 *   - rEnt 処理結果エンティティ
	 *   - err_msg エラーメッセージ
     */
    public function save($tbl_name, $ent){
    	$res = $this->saveData->save($tbl_name, $ent);
    	return $res['ent'];
    }
    
    
    /**
     * データをDB保存する
	 * 
	 * @note
	 * idフィールドが主キー、オートインクリメントであるテーブルが対象。
	 * 1行でもDB登録に失敗すると一旦すべてロールバックする。
	 * トランザクション制御が内部でなされている。idをレスポンスとして取得する仕様上、この制御ははずせず。
	 * 
	 * @param string $tbl_name テーブル名
	 * @param array $data データ（エンティティ配列型）
	 * @return array
	 *   - rData 処理結果データ
	 *   - err_msg エラーメッセージ
	 */
    public function saveAll($tbl_name, $data){
    	$res = $this->saveData->saveAll($tbl_name, $data);
    	return $res;
    }
    
    
    /**
    * idを指定して抹消（データベースからDELETE）
    * @param string $tbl_name
    * @param int $id
    */
    public function destroy($tbl_name, $id){
    	if(empty($id)) return;
    	if(!is_numeric($id)) return;
    	
    	$sql = "DELETE FROM {$tbl_name} WHERE id = {$id}";
    	return $this->query($sql);
    }
    

}