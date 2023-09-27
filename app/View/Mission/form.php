

<div id="form_spa" >
<div style="max-width:840px;margin: auto;">

	<div class="row">
		<div class="col-md-3">
			<h2 class="text-success js_create_mode" >新規入力</h2>
			<h2 class="text-primary js_edit_mode">編集</h2>
		</div>
		<div class="col-md-9" style="text-align:right">
		
			<span class="text-danger js_valid_err_msg">エラーメッセージ</span>
			<span class="text-success js_registering_msg"  >データベースに登録中です...</span>
			<button type="button" class="btn btn-success  btn-lg js_submit_btn js_create_mode" onclick="regAction();">登録</button>
			<button type="button" class="btn btn-warning  btn-lg js_submit_btn js_edit_mode" onclick="regAction();">変更</button>
			<button type="button" class="btn btn-outline-secondary btn-lg close" aria-label="閉じる" onclick="closeForm()" >閉じる</button>
		</div>
	</div>

	<div class="err text-danger"></div>
	
	
	
	<input type="hidden" name="sort_no">
	
	<div class="row js_edit_mode">
		<div class='col-md-12'>
			ID:<span data-display="id"></span>
			<input type="hidden" name="id" value=''  />
		</div>
	</div>
	
	<!-- CBBXS-5007 -->
	<div class="row mt-2">
		<div class='col-md-2 ' >任務名</div>
		<div class='col-md-10'>
			<input type="text" name="mission_name" class="form-control form-control-lg " value=""  maxlength="255"  required title="任務名は255文字以内で入力してください" />
			<span class="text-danger" data-valid-err='mission_name'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2' >雛ファイルID </div>
		<div class='col-md-10'>
			<?php echo $cbh->selectForInpForm('hina_file_id', $hinaFileList, '- 雛ファイルID -'); ?>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製元パス</div>
		<div class='col-md-10'>
			<input type="text" name="from_path" class="form-control form-control-lg " value=""  maxlength="1024"  required title="複製元パスは1024文字以内で入力してください" />
			<span class="text-danger" data-valid-err='from_path'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製元画面コード</div>
		<div class='col-md-10'>
			<input type="text" name="from_scr_code" class="form-control form-control-lg " value=""  maxlength="64"  required title="複製元画面コードは64文字以内で入力してください" />
			<span class="text-danger" data-valid-err='from_scr_code'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製元DB名</div>
		<div class='col-md-10'>
			<input type="text" name="from_db_name" class="form-control form-control-lg " value=""  maxlength="64"  required title="複製元DB名は64文字以内で入力してください" />
			<span class="text-danger" data-valid-err='from_db_name'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製元テーブル名</div>
		<div class='col-md-10'>
			<input type="text" name="from_tbl_name" class="form-control form-control-lg " value=""  maxlength="64"  required title="複製元テーブル名は64文字以内で入力してください" />
			<span class="text-danger" data-valid-err='from_tbl_name'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製元和名</div>
		<div class='col-md-10'>
			<input type="text" name="from_wamei" class="form-control form-control-lg " value=""  maxlength="256"  required title="複製元和名は256文字以内で入力してください" />
			<span class="text-danger" data-valid-err='from_wamei'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製先パス</div>
		<div class='col-md-10'>
			<input type="text" name="to_path" class="form-control form-control-lg " value=""  maxlength="1024"  required title="複製先パスは1024文字以内で入力してください" />
			<span class="text-danger" data-valid-err='to_path'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製先画面コード</div>
		<div class='col-md-10'>
			<input type="text" name="to_scr_code" class="form-control form-control-lg " value=""  maxlength="64"  required title="複製先画面コードは64文字以内で入力してください" />
			<span class="text-danger" data-valid-err='to_scr_code'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製先DB名</div>
		<div class='col-md-10'>
			<input type="text" name="to_db_name" class="form-control form-control-lg " value=""  maxlength="64"  required title="複製先DB名は64文字以内で入力してください" />
			<span class="text-danger" data-valid-err='to_db_name'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製先テーブル名</div>
		<div class='col-md-10'>
			<input type="text" name="to_tbl_name" class="form-control form-control-lg " value=""  maxlength="64"  required title="複製先テーブル名は64文字以内で入力してください" />
			<span class="text-danger" data-valid-err='to_tbl_name'></span>
		</div>
	</div>

	<div class="row mt-2">
		<div class='col-md-2 ' >複製先和名</div>
		<div class='col-md-10'>
			<input type="text" name="to_wamei" class="form-control form-control-lg " value=""  maxlength="256"  required title="複製先和名は256文字以内で入力してください" />
			<span class="text-danger" data-valid-err='to_wamei'></span>
		</div>
	</div>


	<!-- CBBXE -->

	<div class="row">
		<div class="col-md-3"></div>
		<div class="col-md-9" style="text-align:right">
		
			<span class="text-danger js_valid_err_msg">エラーメッセージ</span>
			<span class="text-success js_registering_msg"  >データベースに登録中です...</span>
			<button type="button" class="btn btn-success  btn-lg js_submit_btn js_create_mode" onclick="regAction();">登録</button>
			<button type="button" class="btn btn-warning  btn-lg js_submit_btn js_edit_mode" onclick="regAction();">変更</button>
			<button type="button" class="btn btn-outline-secondary btn-lg close" aria-label="閉じる" onclick="closeForm()" >閉じる</button>
		</div>
	</div>
	
	<div class="cbf_inp_wrap js_edit_mode" style="padding:5px;">
		<input type="button" value="更新情報" class="btn btn-secondary btn-sm" onclick="$('#edit_detail_info').toggle(300)" /><br>
		<aside id="edit_detail_info" style="display:none">
			<div>更新日時: <span data-display="updated_at"></span></div>
			<div>生成日時: <span data-display="created_at"></span></div>
			<div>更新ユーザー名: <span data-display="update_user"></span></div>
			<div>IPアドレス: <span data-display="ip_addr"></span></div>
		</aside>
	</div>

</div><!-- max-width -->
</div><!-- form_spa -->