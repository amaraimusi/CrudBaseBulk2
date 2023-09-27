<?php 
namespace App\Helper;

$ver_str = '?v=' . $this_page_version;
$cbh = new CrudBaseHelper($crudBaseData);
$public_url = $crudBaseData['paths']['public_url'];
$app_path = $crudBaseData['paths']['app_path'];
$debug_mode = $crudBaseData['debug_mode'];
?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="google" content="notranslate" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>任務管理画面 | ワクガンス</title>
	<link rel='shortcut icon' href='<?php echo $public_url; ?>/img/favicon.png' />
	
	<link href="<?php echo $public_url; ?>/node_modules/bootstrap/dist/css/bootstrap.min.css<?php echo $ver_str; ?>" rel="stylesheet">
	<link href="<?php echo $public_url; ?>/node_modules/bootstrap-icons/font/bootstrap-icons.min.css<?php echo $ver_str; ?>" rel="stylesheet">
	<link href="<?php echo $public_url; ?>/css/common/common.css<?php echo $ver_str; ?>" rel="stylesheet">
	<?php echo $cbh->crudBaseCss($debug_mode, $this_page_version); ?>
	<link href="<?php echo $public_url; ?>/css/Mission/index.css<?php echo $ver_str; ?>" rel="stylesheet">
	
	<script src="<?php echo $public_url; ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js<?php echo $ver_str; ?>"></script>
	<script src="<?php echo $public_url; ?>/node_modules/vue/dist/vue.min.js<?php echo $ver_str; ?>"></script>
	<script src="<?php echo $public_url; ?>/node_modules/jquery/dist/jquery.min.js<?php echo $ver_str; ?>"></script>	<!-- jquery-3.3.1.min.js -->
	<?php echo $cbh->crudBaseJs($debug_mode, $this_page_version); ?>
	<script src="<?php echo $public_url; ?>/js/Mission/index.js<?php echo $ver_str; ?>"></script>
</head>
<body>

<?php include($app_path . '\View\Layout\common_header.php'); ?>
<div class="container-fluid">

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="<?php echo $public_url; ?>">ホーム</a></li>
	<li class="breadcrumb-item active" aria-current="page">任務管理画面(見本版)</li>
  </ol>
</nav>

<div id="err" class="text-danger"></div>

<main>

<!-- 検索フォーム -->
<form id="searchForm" method="GET" action="" >
	
	<div><?php echo $cbh->searchFormText('main_search', '検索', ['title'=>'任務名、備考を部分検索します']); ?></div>
	
	<div style="display:inline-block;">
		<div id="search_dtl_div" style="display:none;">

			<div><?php echo $cbh->searchFormId(); ?></div>
			
			<!-- CBBXS-5030 -->
			<div><?php echo $cbh->searchFormText('mission_name', '任務名'); ?></div>
			<div><?php echo $cbh->searchFormSelect('hina_file_id', '雛ファイルID', $hinaFileList); ?></div>
			<div><?php echo $cbh->searchFormText('from_path', '複製元パス'); ?></div>
			<div><?php echo $cbh->searchFormText('from_scr_code', '複製元画面コード'); ?></div>
			<div><?php echo $cbh->searchFormText('from_db_name', '複製元DB名'); ?></div>
			<div><?php echo $cbh->searchFormText('from_tbl_name', '複製元テーブル名'); ?></div>
			<div><?php echo $cbh->searchFormText('from_wamei', '複製元和名'); ?></div>
			<div><?php echo $cbh->searchFormText('to_path', '複製先パス'); ?></div>
			<div><?php echo $cbh->searchFormText('to_scr_code', '複製先画面コード'); ?></div>
			<div><?php echo $cbh->searchFormText('to_db_name', '複製先DB名'); ?></div>
			<div><?php echo $cbh->searchFormText('to_tbl_name', '複製先テーブル名'); ?></div>
			<div><?php echo $cbh->searchFormText('to_wamei', '複製先和名'); ?></div>

			<!-- CBBXE -->
			<div><?php echo $cbh->searchFormInt('sort_no', '順番'); ?></div>
			<div><?php echo $cbh->searchFormText('ip_addr', 'IPアドレス'); ?></div>
			<div><?php echo $cbh->searchFormDelete(); ?></div>
			<div><?php echo $cbh->searchFormText('update_user', '更新者'); ?></div>
			<div><?php echo $cbh->searchFormCreated(); ?></div>
			<div><?php echo $cbh->searchFormUpdated(); ?></div>
			<div><?php echo $cbh->searchFormLimit(); ?></div>

			<button type="button" class ="btn btn-outline-secondary" onclick="$('#search_dtl_div').toggle(300);">＜ 閉じる</button>
			
		</div>
	</div>
	<div style="display:inline-block;">
		<button type="button" onclick="searchAction();" class ="btn btn-outline-primary">検索</button>
		<button type="button" class ="btn btn-outline-secondary" onclick="$('#search_dtl_div').toggle(300);">詳細</button>
		<button type="button" class="btn btn-outline-secondary" onclick="clearA()">クリア</button>

	</div>
</form>

<div style="margin-top:0.4em;">

	<!-- CrudBase設定 -->
	<div class="tool_btn_w">
		<div id="crud_base_config"></div>
	</div>

	<div class="tool_btn_w">
		<a href="mission/csv_download" class="btn btn-secondary">CSV</a>
	</div>
	
	<!-- 列表示切替機能 -->
	<div class="tool_btn_w">
		<button class="btn btn-secondary" onclick="$('#csh_div_w').toggle(300);">列表示切替</button>
		<div id="csh_div_w" style="width:100vw;" >
			<div id="csh_div" ></div><!-- 列表示切替機能の各種チェックボックスの表示場所 -->
		</div>
	</div>
	
	<div class="tool_btn_w">
		<button type="button" class="btn btn-success" onclick="clickCreateBtn();">新規登録</button>
	</div>
</div>



<?php echo $cbh->pagenation(); // ページネーション ?>

<table id="main_tbl" class="table table-striped table-bordered table-condensed">
	<thead>
		<tr>
			<th data-field='id'><?php echo $cbh->sortLink($searches, 'mission', 'id', 'ID'); ?></th>
			<!-- CBBXS-5035 -->
			<th data-field='mission_name'><?php echo $cbh->sortLink($searches, 'mission', 'mission_name', '任務名'); ?></th>
			<th data-field='hina_file_id'><?php echo $cbh->sortLink($searches, 'mission', 'hina_file_id', '雛ファイルID'); ?></th>
			<th data-field='from_path'><?php echo $cbh->sortLink($searches, 'mission', 'from_path', '複製元パス'); ?></th>
			<th data-field='from_scr_code'><?php echo $cbh->sortLink($searches, 'mission', 'from_scr_code', '複製元画面コード'); ?></th>
			<th data-field='from_db_name'><?php echo $cbh->sortLink($searches, 'mission', 'from_db_name', '複製元DB名'); ?></th>
			<th data-field='from_tbl_name'><?php echo $cbh->sortLink($searches, 'mission', 'from_tbl_name', '複製元テーブル名'); ?></th>
			<th data-field='from_wamei'><?php echo $cbh->sortLink($searches, 'mission', 'from_wamei', '複製元和名'); ?></th>
			<th data-field='to_path'><?php echo $cbh->sortLink($searches, 'mission', 'to_path', '複製先パス'); ?></th>
			<th data-field='to_scr_code'><?php echo $cbh->sortLink($searches, 'mission', 'to_scr_code', '複製先画面コード'); ?></th>
			<th data-field='to_db_name'><?php echo $cbh->sortLink($searches, 'mission', 'to_db_name', '複製先DB名'); ?></th>
			<th data-field='to_tbl_name'><?php echo $cbh->sortLink($searches, 'mission', 'to_tbl_name', '複製先テーブル名'); ?></th>
			<th data-field='to_wamei'><?php echo $cbh->sortLink($searches, 'mission', 'to_wamei', '複製先和名'); ?></th>

			<!-- CBBXE -->
			<th data-field='sort_no'><?php echo $cbh->sortLink($searches, 'mission', 'sort_no', '順番'); ?></th>
			<th data-field='delete_flg'><?php echo $cbh->sortLink($searches, 'mission', 'delete_flg', '無効フラグ'); ?></th>
			<th data-field='update_user_id'><?php echo $cbh->sortLink($searches, 'mission', 'update_user_id', '更新者'); ?></th>
			<th data-field='ip_addr'><?php echo $cbh->sortLink($searches, 'mission', 'ip_addr', 'IPアドレス'); ?></th>
			<th data-field='created_at'><?php echo $cbh->sortLink($searches, 'mission', 'created_at', '生成日時'); ?></th>
			<th data-field='updated_at'><?php echo $cbh->sortLink($searches, 'mission', 'updated_at', '更新日'); ?></th>

			<th class='js_btns' 'style="width:280px"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($data as $ent){?>
		<tr>
			<td><?php echo $cbh->tdId($ent['id']); ?></td>
			<!-- CBBXS-5040 -->
			<td><?php echo $cbh->tdStr($ent['mission_name']); ?></td>
			<td><?php echo $cbh->tdList($ent['hina_file_id'], $hinaFileList); ?></td>
			<td><?php echo $cbh->tdStr($ent['from_path']); ?></td>
			<td><?php echo $cbh->tdStr($ent['from_scr_code']); ?></td>
			<td><?php echo $cbh->tdStr($ent['from_db_name']); ?></td>
			<td><?php echo $cbh->tdStr($ent['from_tbl_name']); ?></td>
			<td><?php echo $cbh->tdStr($ent['from_wamei']); ?></td>
			<td><?php echo $cbh->tdStr($ent['to_path']); ?></td>
			<td><?php echo $cbh->tdStr($ent['to_scr_code']); ?></td>
			<td><?php echo $cbh->tdStr($ent['to_db_name']); ?></td>
			<td><?php echo $cbh->tdStr($ent['to_tbl_name']); ?></td>
			<td><?php echo $cbh->tdStr($ent['to_wamei']); ?></td>

			<!-- CBBXE -->
			<td><?php echo $cbh->tdStr($ent['sort_no']); ?></td>
			<td><?php echo $cbh->tdDeleteFlg($ent['delete_flg']); ?></td>
			<td><?php echo $cbh->tdStr($ent['update_user']); ?></td>
			<td><?php echo $cbh->tdStr($ent['ip_addr']); ?></td>
			<td><?php echo $cbh->tdStr($ent['created_at']); ?></td>
			<td><?php echo $cbh->tdStr($ent['updated_at']); ?></td>
			
			<td>

				<?php echo $cbh->rowExchangeBtn($searches) ?><!-- 行入替ボタン -->
				<button type="button" class="row_edit_btn btn btn-primary btn-sm" onclick="clickEditBtn(this)">編集</button>
				<button type="button" class="row_copy_btn btn btn-success btn-sm" onclick="clickCopyBtn(this)">複製</button>
				<?php echo $cbh->disabledBtn($searches, $ent['id']) ?><!-- 削除/削除取消ボタン（無効/有効ボタン） -->
				<?php echo $cbh->destroyBtn($searches, $ent['id']) ?><!-- 抹消ボタン -->
			</td>
		</tr>
		<?php } ?>
	</tbody>
</table>

<?php $cbh->divPwms($searches['delete_flg']); // 複数有効/削除の区分を表示する ?>

<?php echo $cbh->pagenation(); // ページネーション ?>


</main>


<?php include($app_path . '\View\Mission\form.php'); ?>

</div><!-- content -->

<?php include($app_path . '\View\Layout\common_footer.php'); ?>

<input type="hidden" id="crud_base_json" value='<?php echo $crud_base_json; ?>' />

</body>
</html>