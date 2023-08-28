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


//　.envファイルから設定値を取得するオリジナル関数
function getEnvSimple($env_fn) {
	
	// 引数のiniファイル名が空、もしくは存在しなければ、なら、nullを返して終了
	if (! $env_fn)  return null;
	
	
	$str = null;
	$env_fn=mb_convert_encoding($env_fn,'SJIS','UTF-8');
	if (!is_file($env_fn)){
		return null;
	}
	
	if ($fp = fopen ( $env_fn, "r" )) {
		$data = array ();
		while ( false !== ($line = fgets ( $fp )) ) {
			$str .= mb_convert_encoding ( $line, 'utf-8', 'utf-8,sjis,euc_jp,jis' );
		}
	}
	fclose ( $fp );
	
	$ary = preg_split( "/¥R/", $str );
	
	$envs = [];
	foreach($ary as $line_str){
		$line_str = trim($line_str);
		if(empty($line_str)) continue;
		
		$ary2 = preg_split("/=/", $line_str);
		if(count($ary2) < 2) continue;
		
		$key = trim($ary2[0]);
		if(empty($key)) continue;
		
		$envs[$key] = trim($ary2[1]);
		
	}
	
	return $envs;
}