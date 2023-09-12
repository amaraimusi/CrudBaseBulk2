<?php 

// デバッグ関数
function debug($var){
	echo '<pre>';
	
	
	if(is_array($var)){
		$depth = g_array_depth($var);
		switch($depth){
			case 1:
				g_dumpEntity($var);
				break;
			case 2:
				g_dumpData($var);
				break;
			default:
				var_dump($var);
		}
	}else{
		var_dump($var);
	}
		
		
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

// 配列の深度を調べる
function g_array_depth(array $array): int {
	$max_depth = 1;
	
	foreach ($array as $value) {
		if (is_array($value)) {
			$depth = g_array_depth($value) + 1;
			
			if ($depth > $max_depth) {
				$max_depth = $depth;
			}
		}
	}
	
	return $max_depth;
}

// エンティティ用のデバッグ
function g_dumpEntity($ent){
	if(!is_array($ent)){
		echo "<div>{$ent}</div>";
		return;
	}
	foreach($ent as $field => $value){
		echo "<div>{$field} = {$value}</div>";
	}
	
}

// データ用のデバッグ
function g_dumpData($data){
	foreach($data as $i => $ent){
		echo "<div>■{$i}</div>";
		g_dumpEntity($ent);
	}
}

// XSSサニタイズ
function h($text){
	return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}