<?php 

// autoload.php
spl_autoload_register(function ($class) {
	// プレフィックスまたは名前空間を置き換える（もし必要な場合）
	$prefix = 'App\\';
	$base_dir = __DIR__ . '/';
	
	// クラスにプレフィックスがある場合
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}
	
	// 名前空間やクラス名の残りの部分を取得
	$relative_class = substr($class, $len);
	
	// クラスファイルのパスを取得（ここで適切なディレクトリ構造に変更する）
	$file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
	
	// クラスファイルが存在する場合は読み込む
	if (file_exists($file)) {
		require $file;
	}
});
