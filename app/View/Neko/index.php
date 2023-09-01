<?php 
global $g_env;

$public_url = $g_env['public_url'];

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="google" content="notranslate" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>テンプレート | ワクガンス</title>
	<link rel='shortcut icon' href='<?php echo $public_url; ?>/img/favicon.png' />
	
	<link href="<?php echo $public_url; ?>/node_modules/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
	<link href="<?php echo $public_url; ?>/node_modules/bootstrap-icons/font/bootstrap-icons.min.css" rel="stylesheet">
	<link href="<?php echo $public_url; ?>/css/common/common.css" rel="stylesheet">
	
	<script src="<?php echo $public_url; ?>/node_modules/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
	
	<script src="<?php echo $public_url; ?>/node_modules/vue/dist/vue.min.js"></script>
	<script src="<?php echo $public_url; ?>/node_modules/jquery/dist/jquery.min.js"></script>	<!-- jquery-3.3.1.min.js -->
	<script src="<?php echo $public_url; ?>/js/Neko/index.js"></script>
	
	

</head>
<body>
<div id="header" ><h1>テンプレート | ワクガンス</h1></div>
<div class="container">


<div id="sec1-1" class="sec4">
	<h3>xxx</h3>

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
	<li>テンプレート</li>
</ol>
<ol class="breadcrumb">
	<li><a href="/">ホーム</a></li>
	<li><a href="/sample">サンプルソースコード</a></li>
	<li><a href="/sample/js">JavaScript ｜ サンプル</a></li>
	<li>テンプレート</li>
</ol>
</div><!-- content -->
<div id="footer">(C) amaraimusi 2023-1-1</div>
</body>
</html>