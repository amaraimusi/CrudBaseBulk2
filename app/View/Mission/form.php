

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
	
	<!-- CBBXS-2007 -->
			<div class="cbf_inp_wrap">
				<div class='cbf_inp' >ID: </div>
				<div class='cbf_input'>
					<span class="id"></span>
				</div>
			</div>
		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >任務名: </div>
			<div class='cbf_input'>
				<input type="text" name="mission_name" class="valid " value=""  maxlength="255" title="255文字以内で入力してください" />
				<label class="text-danger" for="mission_name"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp_label' >雛ファイルID: </div>
			<div class='cbf_input'>
				<?php $cbh->selectX('hina_file_id',null,$hinaFileIdList,null);?>
				<label class="text-danger" for="hina_file_id"></label>
			</div>
		</div>

		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製元パス: </div>
			<div class='cbf_input'>
				<input type="text" name="from_path" class="valid " value=""  maxlength="1024" title="1024文字以内で入力してください" />
				<label class="text-danger" for="from_path"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製元画面コード: </div>
			<div class='cbf_input'>
				<input type="text" name="from_scr_code" class="valid " value=""  maxlength="64" title="64文字以内で入力してください" />
				<label class="text-danger" for="from_scr_code"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製元DB名: </div>
			<div class='cbf_input'>
				<input type="text" name="from_db_name" class="valid " value=""  maxlength="64" title="64文字以内で入力してください" />
				<label class="text-danger" for="from_db_name"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製元テーブル名: </div>
			<div class='cbf_input'>
				<input type="text" name="from_tbl_name" class="valid " value=""  maxlength="64" title="64文字以内で入力してください" />
				<label class="text-danger" for="from_tbl_name"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製元和名: </div>
			<div class='cbf_input'>
				<input type="text" name="from_wamei" class="valid " value=""  maxlength="256" title="256文字以内で入力してください" />
				<label class="text-danger" for="from_wamei"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製先パス: </div>
			<div class='cbf_input'>
				<input type="text" name="to_path" class="valid " value=""  maxlength="1024" title="1024文字以内で入力してください" />
				<label class="text-danger" for="to_path"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製先画面コード: </div>
			<div class='cbf_input'>
				<input type="text" name="to_scr_code" class="valid " value=""  maxlength="64" title="64文字以内で入力してください" />
				<label class="text-danger" for="to_scr_code"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製先DB名: </div>
			<div class='cbf_input'>
				<input type="text" name="to_db_name" class="valid " value=""  maxlength="64" title="64文字以内で入力してください" />
				<label class="text-danger" for="to_db_name"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製先テーブル名: </div>
			<div class='cbf_input'>
				<input type="text" name="to_tbl_name" class="valid " value=""  maxlength="64" title="64文字以内で入力してください" />
				<label class="text-danger" for="to_tbl_name"></label>
			</div>
		</div>


		<div class="cbf_inp_wrap">
			<div class='cbf_inp' >複製先和名: </div>
			<div class='cbf_input'>
				<input type="text" name="to_wamei" class="valid " value=""  maxlength="256" title="256文字以内で入力してください" />
				<label class="text-danger" for="to_wamei"></label>
			</div>
		</div>


			<div class="cbf_inp_wrap">
				<div class='cbf_inp_label' >無効フラグ：</div>
				<div class='cbf_input'>
					<input type="checkbox" name="delete_flg" class="valid"  />
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