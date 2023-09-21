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
	<title>ネコ管理画面 | ワクガンス</title>
	<link rel='shortcut icon' href='<?php echo $public_url; ?>/img/favicon.png' />
	
	<link href="<?php echo $public_url; ?>/node_modules/bootstrap/dist/css/bootstrap.min.css<?php echo $ver_str; ?>" rel="stylesheet">
	<link href="<?php echo $public_url; ?>/node_modules/bootstrap-icons/font/bootstrap-icons.min.css<?php echo $ver_str; ?>" rel="stylesheet">
	<link href="<?php echo $public_url; ?>/css/common/common.css<?php echo $ver_str; ?>" rel="stylesheet">
	<?php echo $cbh->crudBaseCss($debug_mode, $this_page_version); ?>
	<link href="<?php echo $public_url; ?>/css/Neko/index.css<?php echo $ver_str; ?>" rel="stylesheet">
	
	<script src="<?php echo $public_url; ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js<?php echo $ver_str; ?>"></script>
	<script src="<?php echo $public_url; ?>/node_modules/vue/dist/vue.min.js<?php echo $ver_str; ?>"></script>
	<script src="<?php echo $public_url; ?>/node_modules/jquery/dist/jquery.min.js<?php echo $ver_str; ?>"></script>	<!-- jquery-3.3.1.min.js -->
	<?php echo $cbh->crudBaseJs($debug_mode, $this_page_version); ?>
	<script src="<?php echo $public_url; ?>/js/Neko/index.js<?php echo $ver_str; ?>"></script>
</head>
<body>

<?php include($app_path . '\View\Layout\common_header.php'); ?>
<div class="container-fluid">

<nav aria-label="breadcrumb">
  <ol class="breadcrumb">
	<li class="breadcrumb-item"><a href="<?php echo $public_url; ?>">ホーム</a></li>
	<li class="breadcrumb-item active" aria-current="page">ネコ管理画面(見本版)</li>
  </ol>
</nav>

<div id="err" class="text-danger"></div>

<main>

<!-- 検索フォーム -->
<form id="searchForm" method="GET" action="" >
	
	<div><?php echo $cbh->searchFormText('main_search', '検索', ['title'=>'ネコ名、備考を部分検索します']); ?></div>
	
	<div style="display:inline-block;">
		<div id="search_dtl_div" style="display:none;">

			<div><?php echo $cbh->searchFormId(); ?></div>
			<div><?php echo $cbh->searchFormText('neko_name', 'ネコ名', ['pattern'=>'[\u30A1-\u30FF]+', 'err_msg'=>'👈%display_nameはカタカナのみ入力可能です。']); ?></div>
			<div><?php echo $cbh->searchFormDateRng('neko_date', 'ネコ日付'); ?></div>
			<div><?php echo $cbh->searchFormSelect('neko_type', 'ネコ種別', $nekoTypeList); ?></div>
			<div><?php echo $cbh->searchFormDatetime('neko_dt', 'ネコ日時'); ?></div>
			<div><?php echo $cbh->searchFormFlg('neko_flg', 'ネコフラグ'); ?></div>
			
			<input type="search" placeholder="画像ファイル名" name="img_fn" value="<?php echo h($searches['img_fn'] ?? ''); ?>" class="form-control search_btn_x">
			<input type="search" placeholder="備考" name="note" value="<?php echo h($searches['note'] ?? ''); ?>" class="form-control search_btn_x">
			<input type="search" placeholder="順番" name="sort_no" value="<?php echo h($searches['sort_no'] ?? ''); ?>" class="form-control search_btn_x">
			<input type="search" placeholder="IPアドレス" name="ip_addr" value="<?php echo h($searches['ip_addr'] ?? ''); ?>" class="form-control search_btn_x">

			<button type="button" class ="btn btn-outline-secondary" onclick="$('#search_dtl_div').toggle(300);">＜ 閉じる</button>
			<?php echo $cbh->searchFormDelete(); ?>
			
			<input type="search" placeholder="更新者" name="update_user" value="<?php echo h($searches['update_user'] ?? ''); ?>" class="form-control search_btn_x">
			<?php echo $cbh->searchFormCreated(); ?>
			<?php echo $cbh->searchFormUpdated(); ?>
			<?php echo $cbh->searchFormLimit(); ?>

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
		<a href="neko/csv_download" class="btn btn-secondary">CSV</a>
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
			<!-- CBBXS-3035 -->
			<th data-field='id'><?php echo $cbh->sortLink($searches, 'neko', 'id', 'ID'); ?></th>
			<th data-field='neko_val'><?php echo $cbh->sortLink($searches, 'neko', 'neko_val', 'ネコ数値'); ?></th>
			<th data-field='neko_name'><?php echo $cbh->sortLink($searches, 'neko', 'neko_name', 'ネコ名'); ?></th>
			<th data-field='neko_date'><?php echo $cbh->sortLink($searches, 'neko', 'neko_date', 'ネコ日付'); ?></th>
			<th data-field='neko_type'><?php echo $cbh->sortLink($searches, 'neko', 'neko_type', '猫種別'); ?></th>
			<th data-field='neko_dt'><?php echo $cbh->sortLink($searches, 'neko', 'neko_dt', 'ネコ日時'); ?></th>
			<th data-field='neko_flg'><?php echo $cbh->sortLink($searches, 'neko', 'neko_flg', 'ネコフラグ'); ?></th>
			<th data-field='img_fn'><?php echo $cbh->sortLink($searches, 'neko', 'img_fn', '画像ファイル名'); ?></th>
			<th data-field='note'><?php echo $cbh->sortLink($searches, 'neko', 'note', '備考'); ?></th>
			<th data-field='sort_no'><?php echo $cbh->sortLink($searches, 'neko', 'sort_no', '順番'); ?></th>
			<th data-field='delete_flg'><?php echo $cbh->sortLink($searches, 'neko', 'delete_flg', '無効フラグ'); ?></th>
			<th data-field='update_user_id'><?php echo $cbh->sortLink($searches, 'neko', 'update_user_id', '更新者'); ?></th>
			<th data-field='ip_addr'><?php echo $cbh->sortLink($searches, 'neko', 'ip_addr', 'IPアドレス'); ?></th>
			<th data-field='created_at'><?php echo $cbh->sortLink($searches, 'neko', 'created_at', '生成日時'); ?></th>
			<th data-field='updated_at'><?php echo $cbh->sortLink($searches, 'neko', 'updated_at', '更新日'); ?></th>

			<!-- CBBXE -->
			<th class='js_btns' 'style="width:280px"></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($data as $ent){?>
		<tr>
			<td><?php echo $cbh->tdId($ent['id']); ?></td>
			<td><?php echo $cbh->tdUnit($ent['neko_val'], 'neko_val', null, 'cm'); ?></td>
			<td><?php echo $cbh->tdStr($ent['neko_name']); ?></td>
			<td><?php echo $cbh->tdDate($ent['neko_date']); ?></td>
			<td><?php echo $cbh->tdList($ent['neko_type'], $nekoTypeList); ?></td>
			<td><?php echo $cbh->tdDate($ent['neko_dt']); ?></td>
			<td><?php echo $cbh->tdFlg($ent['neko_flg']);  ?></td>
			<td><?php echo $cbh->tdImg($ent, 'img_fn'); ?></td>
			<td><?php echo $cbh->tdNote($ent['note'], 'note', 30) ?></td>
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


<?php include($app_path . '\View\Neko\form.php'); ?>

</div><!-- content -->

<?php include($app_path . '\View\Layout\common_footer.php'); ?>

<input type="hidden" id="crud_base_json" value='<?php echo $crud_base_json; ?>' />

</body>
</html>