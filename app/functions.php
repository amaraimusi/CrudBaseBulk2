<?php 

// デバッグ関数
function debug($var){
	echo '<pre>';
	var_dump($var);
	echo '</pre>';
}

// デバッグ関数
function dump($var){
	debug($var);
}

/**
 * キャメルケースにスネークケースから変換する
 *
 * 先頭も大文字になる。
 *
 * @param string $str スネークケースの文字列
 * @return string キャメルケースの文字列
 */
function camelize($str) {
	$str = strtr($str, '_', ' ');
	$str = ucwords($str);
	return str_replace(' ', '', $str);
}

//.envファイルから環境データを取得する
function loadEnv($file)
{
	$env = [];
	if (file_exists($file)) {
		$handle = fopen($file, 'r');
		if ($handle) {
			while (($line = fgets($handle)) !== false) {
				if (preg_match('/\A([a-zA-Z0-9_]+)=(.*)\z/', trim($line), $matches)) {
					$name = $matches[1];
					$value = $matches[2];
					$env[$name] = $value;
					//putenv("$name=$value");
				}
			}
			fclose($handle);
		}
	}
	return $env;
}