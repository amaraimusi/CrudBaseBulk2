<?php 
namespace App\Helper;

$ver_str = '?v=' . $this_page_version;
$cbh = new CrudBaseHelper($crudBaseData);
$public_url = $crudBaseData['paths']['public_url'];
$app_path = $crudBaseData['paths']['app_path'];

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
	
	<script src="<?php echo $public_url; ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js<?php echo $ver_str; ?>"></script>
	<script src="<?php echo $public_url; ?>/node_modules/vue/dist/vue.min.js<?php echo $ver_str; ?>"></script>
	<script src="<?php echo $public_url; ?>/node_modules/jquery/dist/jquery.min.js<?php echo $ver_str; ?>"></script>	<!-- jquery-3.3.1.min.js -->
	<script src="<?php echo $public_url; ?>/js/Neko/index.js<?php echo $ver_str; ?>"></script>
</head>
<body>

<?php include($app_path . '\View\Layout\common_header.php'); ?>
<div class="container">


<div id="sec1-1" class="sec4">
	<h3>ネコ管理画面</h3>

	<div id="app1">
	  <div>{{ message1 }}</div>
	  <input v-model="message1">
	  <div v-bind:title="text1">アマミノクロウサギ:title属性にセット</div>
	  <div v-bind:class="class1">サキシマハブ:クラス属性にセット</div>
	</div>
	
	<button type="button" class ="btn btn-info"><span class="text-light"><i class="bi bi-0-circle"></i></span></button>

	<br><time>2023-1-1</time>
</div>


<div id="sec1-0" class="sec4" style="display:none">
	<h3>xxx</h3>


	<br><time>2023-1-1</time>
</div>


<div class="yohaku"></div>
<ol class="breadcrumb">
	<li><a href="/">ホーム</a></li>
	<li><a href="/note_prg">プログラミングの覚書</a></li>
	<li><a href="/note_prg/js/">JavaScriptの覚書</a></li>
	<li>ネコ管理画面</li>
</ol>
<ol class="breadcrumb">
	<li><a href="/">ホーム</a></li>
	<li><a href="/sample">サンプルソースコード</a></li>
	<li><a href="/sample/js">JavaScript ｜ サンプル</a></li>
	<li>ネコ管理画面</li>
</ol>
</div><!-- content -->
<div id="footer">(C) amaraimusi 2023-1-1</div>
</body>
</html>