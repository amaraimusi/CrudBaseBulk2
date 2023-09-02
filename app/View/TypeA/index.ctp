<?php
$this->CrudBase->setModelName('TypeA');

// CSSファイルのインクルード
$cssList = $this->CrudBase->getCssList();
$this->assign('css', $this->Html->css($cssList));

// JSファイルのインクルード
$jsList = $this->CrudBase->getJsList();
$jsList[] = 'TypeA/index'; // 当画面専用JavaScript
$this->assign('script', $this->Html->script($jsList,array('charset'=>'utf-8')));

?>




<h2>タイプA</h2>

タイプAの検索閲覧および編集する画面です。<br>
<br>

<?php
	$this->Html->addCrumb("トップ",'/');
	$this->Html->addCrumb("タイプA");
	echo $this->Html->getCrumbs(" > ");
?>

<?php echo $this->element('CrudBase/crud_base_new_page_version');?>
<div id="err" class="text-danger"><?php echo $errMsg;?></div>


<!-- 検索条件入力フォーム -->
<div style="margin-top:5px">
	<?php 
		echo $this->Form->create('TypeA', array('url' => true ));
	?>

	
	<div style="clear:both"></div>
	
	<div id="detail_div" style="display:none">
		
		<?php 
		
		$this->CrudBase->inputKjText($kjs,'kj_type_a_name','タイプ名',300);
		$this->CrudBase->inputKjText($kjs,'kj_par_id','親ID',80);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_eq_field_name','フィールド名条件【完全一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_in_field_name','フィールド名条件【部分一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_eq_field_type','フィールド型条件【完全一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_in_field_type','フィールド型条件【部分一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_type_long1','型長さ条件1',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_type_long2','型長さ条件2',300);
		$this->CrudBase->inputKjFlg($kjs,'kj_cnd_null_flg','NULLフラグ条件',300);
		$this->CrudBase->inputKjFlg($kjs,'kj_cnd_p_key_flg','主キーフラグ条件',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_eq_def_val','デフォルト値条件【完全一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_in_def_val','デフォルト値条件【部分一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_eq_extra','補足条件【完全一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_in_extra','補足条件【部分一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_eq_comment','コメント条件【完全一致】',300);
		$this->CrudBase->inputKjText($kjs,'kj_cnd_in_comment','コメント条件【部分一致】',300);
		
		$this->CrudBase->inputKjId($kjs); 
		$this->CrudBase->inputKjHidden($kjs,'kj_sort_no');
		$this->CrudBase->inputKjDeleteFlg($kjs);
		$this->CrudBase->inputKjText($kjs,'kj_update_user','更新者',150);
		$this->CrudBase->inputKjText($kjs,'kj_ip_addr','更新IPアドレス',200);
		$this->CrudBase->inputKjCreated($kjs);
		$this->CrudBase->inputKjModified($kjs);
		$this->CrudBase->inputKjLimit($kjs);

		echo $this->element('CrudBase/crud_base_cmn_inp');
		
		?>

		
		
		<?php 
		
		echo $this->Form->submit('検索', array('name' => 'search','class'=>'btn btn-success','div'=>false,));
		
		echo $this->element('CrudBase/crud_base_index');
		
		$csv_dl_url = $this->html->webroot . 'type_a/csv_download';
		$this->CrudBase->makeCsvBtns($csv_dl_url);
		?>
	

	<div style="margin-top:40px">
		
	</div>

	</div><!-- detail_div -->

	<div id="func_btns" >
		
			<div class="line-left">
				<button type="button" onclick="$('#detail_div').toggle(300);" class="btn btn-default btn-sm">
					<span class="glyphicon glyphicon-cog"></span>
				</button>

			</div>
			
			<div class="line-middle"></div>
			
			<div class="line-right">
				<a href="<?php echo $home_url; ?>" class="btn btn-info" title="この画面を最初に表示したときの状態に戻します。（検索状態、列並べの状態を初期状態に戻します。）">
					<span class="glyphicon glyphicon-certificate"  ></span></a>
				<?php 
					// 新規入力ボタンを作成
					$newBtnOption = array(
							'scene'=>'<span class="glyphicon glyphicon-plus"></span>追加'
					);
					$this->CrudBase->newBtn($newBtnOption);
				?>

			</div>



	</div>
	<div style="clear:both"></div>
	<?php echo $this->Form->end()?>

	
</div>

<input type="button" value="ツリーマップ" class="btn btn-info" onclick="jQuery('#map_tbl').toggle(300);" />
<div id="map_tbl" style="display:none">
<?php echo $map_tbl; ?>
</div>


<br />

<div id="total_div">
	<table><tr>
		<td>件数:<?php echo $data_count ?></td>
		<td><a href="#help_lists" class="livipage btn btn-info btn-xs" title="ヘルプ"><span class="glyphicon glyphicon-question-sign"></span></a></td>
	</tr></table>
</div>


<div style="margin-bottom:5px">
	<?php echo $pages['page_index_html'];//ページ目次 ?>
</div>



<div id="crud_base_auto_save_msg" style="height:20px;" class="text-success"></div>
<!-- 一覧テーブル -->
<table id="type_a_tbl" border="1"  class="table table-striped table-bordered table-condensed">

<thead>
<tr>
	<?php
	foreach($field_data as $ent){
		$row_order=$ent['row_order'];
		echo "<th class='{$ent['id']}'>{$pages['sorts'][$row_order]}</th>";
	}
	?>
	<th></th>
</tr>
</thead>
<tbody>
<?php

// td要素出力を列並モードに対応させる
$this->CrudBase->startClmSortMode($field_data);

foreach($data as $i=>$ent){

	echo "<tr id=i{$ent['id']}>";
	// --- Start field_table
	$this->CrudBase->tdId($ent,'id',array('checkbox_name'=>'pwms'));
	
	$this->CrudBase->tdStr($ent,'type_a_name');
	$this->CrudBase->tdPlain($ent,'par_id');
	$this->CrudBase->tdStr($ent,'cnd_eq_field_name');
	$this->CrudBase->tdStr($ent,'cnd_in_field_name');
	$this->CrudBase->tdStr($ent,'cnd_eq_field_type');
	$this->CrudBase->tdStr($ent,'cnd_in_field_type');
	$this->CrudBase->tdPlain($ent,'cnd_type_long1');
	$this->CrudBase->tdPlain($ent,'cnd_type_long2');
	$this->CrudBase->tdFlg($ent,'cnd_null_flg');
	$this->CrudBase->tdFlg($ent,'cnd_p_key_flg');
	$this->CrudBase->tdStr($ent,'cnd_eq_def_val');
	$this->CrudBase->tdStr($ent,'cnd_in_def_val');
	$this->CrudBase->tdStr($ent,'cnd_eq_extra');
	$this->CrudBase->tdStr($ent,'cnd_in_extra');
	$this->CrudBase->tdStr($ent,'cnd_eq_comment');
	$this->CrudBase->tdStr($ent,'cnd_in_comment');
	
	$this->CrudBase->tdPlain($ent,'sort_no');
	$this->CrudBase->tdDeleteFlg($ent,'delete_flg');
	$this->CrudBase->tdPlain($ent,'update_user');
	$this->CrudBase->tdPlain($ent,'ip_addr');
	$this->CrudBase->tdPlain($ent,'created');
	$this->CrudBase->tdPlain($ent,'modified');
	// --- End field_table
	
	$this->CrudBase->tdsEchoForClmSort();// 列並に合わせてTD要素群を出力する
	
	// 行のボタン類
	echo "<td><div class='btn-group'>";
	$id = $ent['id'];
	echo  "<input type='button' value='↑↓' onclick='rowExchangeShowForm(this)' class='row_exc_btn btn btn-info btn-xs' />";
	$this->CrudBase->rowDeleteBtn($ent); // 削除ボタン
	$this->CrudBase->rowEnabledBtn($ent); // 有効ボタン
	$this->CrudBase->rowEditBtn($id);
	$this->CrudBase->rowPreviewBtn($id);
	$this->CrudBase->rowCopyBtn($id);
	$this->CrudBase->rowEliminateBtn($ent);// 抹消ボタン
	echo "</div></td>";
	
	echo "</tr>";
}

?>
</tbody>
</table>

<?php echo $this->element('CrudBase/crud_base_pwms'); // 複数選択による一括処理 ?>

<!-- 新規入力フォーム -->
<div id="ajax_crud_new_inp_form" class="panel panel-primary">

	<div class="panel-heading">
		<div class="pnl_head1">新規入力</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-primary btn-sm" onclick="closeForm('new_inp')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	<div class="panel-body">
	<div class="err text-danger"></div>
	
	<div style="display:none">
    	<input type="hidden" name="form_type">
    	<input type="hidden" name="row_index">
    	<input type="hidden" name="sort_no">
	</div>
	<table><tbody>
	
		<tr><td>タイプ名: </td><td>
			<input type="text" name="type_a_name" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="type_a_name"></label>
		</td></tr>
		<tr><td>親ID: </td><td>
			<input type="text" name="par_id" class="valid" value=""  pattern="^[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="par_id"></label>
		</td></tr>
		<tr><td>フィールド名条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_field_name" class="valid" value=""  maxlength="64" title="64文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_field_name"></label>
		</td></tr>
		<tr><td>フィールド名条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_field_name" class="valid" value=""  maxlength="64" title="64文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_field_name"></label>
		</td></tr>
		<tr><td>フィールド型条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_field_type" class="valid" value=""  maxlength="32" title="32文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_field_type"></label>
		</td></tr>
		<tr><td>フィールド型条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_field_type" class="valid" value=""  maxlength="32" title="32文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_field_type"></label>
		</td></tr>
		<tr><td>型長さ条件1: </td><td>
			<input type="text" name="cnd_type_long1" class="valid" value=""  maxlength="11" title="11文字以内で入力してください" />
			<label class="text-danger" for="cnd_type_long1"></label>
		</td></tr>
		<tr><td>型長さ条件2: </td><td>
			<input type="text" name="cnd_type_long2" class="valid" value=""  maxlength="11" title="11文字以内で入力してください" />
			<label class="text-danger" for="cnd_type_long2"></label>
		</td></tr>
		<tr><td>NULLフラグ条件: </td><td>
			<select name="cnd_null_flg" >
				<option value="">-- NULLフラグ条件 --</option>
				<option value="0">無効</option>
				<option value="1">有効</option>
			</select>
			<label class="text-danger" for="cnd_null_flg"></label>
		</td></tr>
		<tr><td>主キーフラグ条件: </td><td>
			<select name="cnd_p_key_flg" >
				<option value="">-- 主キーフラグ条件 --</option>
				<option value="0">無効</option>
				<option value="1">有効</option>
			</select>
			<label class="text-danger" for="cnd_p_key_flg"></label>
		</td></tr>
		<tr><td>デフォルト値条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_def_val" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_def_val"></label>
		</td></tr>
		<tr><td>デフォルト値条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_def_val" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_def_val"></label>
		</td></tr>
		<tr><td>補足条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_extra" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_extra"></label>
		</td></tr>
		<tr><td>補足条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_extra" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_extra"></label>
		</td></tr>
		<tr><td>コメント条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_comment" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_comment"></label>
		</td></tr>
		<tr><td>コメント条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_comment" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_comment"></label>
		</td></tr>
		
	</tbody></table>
	

	<button type="button" onclick="newInpReg();" class="btn btn-success">
		<span class="glyphicon glyphicon-ok"></span>
	</button>

	</div><!-- panel-body -->
</div>



<!-- 編集フォーム -->
<div id="ajax_crud_edit_form" class="panel panel-primary">

	<div class="panel-heading">
		<div class="pnl_head1">編集</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-primary btn-sm" onclick="closeForm('edit')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	<div style="display:none">
    	<input type="hidden" name="sort_no">
	</div>
	<div class="panel-body">
	<div class="err text-danger"></div>
	<table><tbody>

		<!-- Start ajax_form_edit_start -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		
		<tr><td>タイプ名: </td><td>
			<input type="text" name="type_a_name" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="type_a_name"></label>
		</td></tr>
		<tr><td>親ID: </td><td>
			<input type="text" name="par_id" class="valid" value=""  pattern="^[0-9]+$" maxlength="11" title="数値を入力してください" />
			<label class="text-danger" for="par_id"></label>
		</td></tr>
		<tr><td>フィールド名条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_field_name" class="valid" value=""  maxlength="64" title="64文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_field_name"></label>
		</td></tr>
		<tr><td>フィールド名条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_field_name" class="valid" value=""  maxlength="64" title="64文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_field_name"></label>
		</td></tr>
		<tr><td>フィールド型条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_field_type" class="valid" value=""  maxlength="32" title="32文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_field_type"></label>
		</td></tr>
		<tr><td>フィールド型条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_field_type" class="valid" value=""  maxlength="32" title="32文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_field_type"></label>
		</td></tr>
		<tr><td>型長さ条件1: </td><td>
			<input type="text" name="cnd_type_long1" class="valid" value=""  maxlength="11" title="11文字以内で入力してください" />
			<label class="text-danger" for="cnd_type_long1"></label>
		</td></tr>
		<tr><td>型長さ条件2: </td><td>
			<input type="text" name="cnd_type_long2" class="valid" value=""  maxlength="11" title="11文字以内で入力してください" />
			<label class="text-danger" for="cnd_type_long2"></label>
		</td></tr>
		<tr><td>NULLフラグ条件: </td><td>
			<select name="cnd_null_flg" >
				<option value="">-- NULLフラグ条件 --</option>
				<option value="0">無効</option>
				<option value="1">有効</option>
			</select>
			<label class="text-danger" for="cnd_null_flg"></label>
		</td></tr>
		<tr><td>主キーフラグ条件: </td><td>
			<select name="cnd_p_key_flg" >
				<option value="">-- 主キーフラグ条件 --</option>
				<option value="0">無効</option>
				<option value="1">有効</option>
			</select>
			<label class="text-danger" for="cnd_p_key_flg"></label>
		</td></tr>
		<tr><td>デフォルト値条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_def_val" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_def_val"></label>
		</td></tr>
		<tr><td>デフォルト値条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_def_val" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_def_val"></label>
		</td></tr>
		<tr><td>補足条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_extra" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_extra"></label>
		</td></tr>
		<tr><td>補足条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_extra" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_extra"></label>
		</td></tr>
		<tr><td>コメント条件【完全一致】: </td><td>
			<input type="text" name="cnd_eq_comment" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_eq_comment"></label>
		</td></tr>
		<tr><td>コメント条件【部分一致】: </td><td>
			<input type="text" name="cnd_in_comment" class="valid" value=""  maxlength="256" title="256文字以内で入力してください" />
			<label class="text-danger" for="cnd_in_comment"></label>
		</td></tr>
		
		<tr><td>削除： </td><td>
			<input type="checkbox" name="delete_flg" class="valid"  />
		</td></tr>
		<!-- Start ajax_form_edit_end -->
	</tbody></table>
	
	

	<button type="button"  onclick="editReg();" class="btn btn-success">
		<span class="glyphicon glyphicon-ok"></span>
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_edit_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_edit_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div>



<!-- 削除フォーム -->
<div id="ajax_crud_delete_form" class="panel panel-danger">

	<div class="panel-heading">
		<div class="pnl_head1">削除</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-default btn-sm" onclick="closeForm('delete')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	
	<div class="panel-body" style="min-width:300px">
	<table><tbody>

		<!-- Start ajax_form_new -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		

		<tr><td>タイプA名: </td><td>
			<span class="type_a_name"></span>
		</td></tr>


		<!-- Start ajax_form_end -->
	</tbody></table>
	<br>
	

	<button type="button"  onclick="deleteReg();" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span>　削除する
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_delete_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_delete_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div>



<!-- 抹消フォーム -->
<div id="ajax_crud_eliminate_form" class="panel panel-danger">

	<div class="panel-heading">
		<div class="pnl_head1">抹消</div>
		<div class="pnl_head2"></div>
		<div class="pnl_head3">
			<button type="button" class="btn btn-default btn-sm" onclick="closeForm('eliminate')"><span class="glyphicon glyphicon-remove"></span></button>
		</div>
	</div>
	
	<div class="panel-body" style="min-width:300px">
	<table><tbody>

		<!-- Start ajax_form_new -->
		<tr><td>ID: </td><td>
			<span class="id"></span>
		</td></tr>
		

		<tr><td>タイプA名: </td><td>
			<span class="type_a_name"></span>
		</td></tr>


		<!-- Start ajax_form_end -->
	</tbody></table>
	<br>
	

	<button type="button"  onclick="eliminateReg();" class="btn btn-danger">
		<span class="glyphicon glyphicon-remove"></span>　抹消する
	</button>
	<hr>
	
	<input type="button" value="更新情報" class="btn btn-default btn-xs" onclick="$('#ajax_crud_eliminate_form_update').toggle(300)" /><br>
	<aside id="ajax_crud_eliminate_form_update" style="display:none">
		更新日時: <span class="modified"></span><br>
		生成日時: <span class="created"></span><br>
		ユーザー名: <span class="update_user"></span><br>
		IPアドレス: <span class="ip_addr"></span><br>
		ユーザーエージェント: <span class="user_agent"></span><br>
	</aside>
	

	</div><!-- panel-body -->
</div>


<br />

<!-- 埋め込みJSON -->
<div style="display:none">
	
</div>



<!-- ヘルプ用  -->
<input type="button" class="btn btn-info btn-sm" onclick="$('#help_x').toggle()" value="ヘルプ" />
<div id="help_x" class="help_x" style="display:none">
	<h2>ヘルプ</h2>

	<?php echo $this->element('CrudBase/crud_base_help');?>


</div>























