<?php 

require_once 'vendor/autoload.php';

global $g_baseData;

$url_path = $_SERVER['REQUEST_URI'];
$segments = explode('/', $url_path);// '/'で区切る
$sliced_segments = array_slice($segments, 0, 3);// 2番目の節までを取得
$main_path = implode('/', $sliced_segments);// 配列を文字列に戻し、メインパスを作成
$class_name = $segments[3] ?? 'Neko'; // クラス名を取得
$method_name = $segments[4] ?? 'index'; // メソッド名を取得
$model_name = camelize($class_name); // モデル名
$base_path = dirname(__FILE__); // 基本パス

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// 環境変数にアクセス
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');

$g_baseData = [
		'url_path' => $url_path,
		'main_path' => $main_path,
		'class_name' => $class_name,
		'method_name' => $method_name,
		'model_name' => $model_name,
		'base_path' => $base_path,
];




require_once 'autoload.php';

require_once 'Controller/BaseXContoller.php';
require_once 'Controller/' . $model_name . 'Controller.php';
$ctrl_class = 'App\\Controller\\' . $model_name . 'Controller';
$ctrlObj = new $ctrl_class;
echo $ctrlObj->$method_name();


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