<?php 

$base_path = dirname(__DIR__); // 基本パス
$app_path = $base_path . '\app';
$public_path = $base_path . '\public';

require_once $app_path . '\vendor\autoload.php';
require_once $app_path . '\functions.php';

// 環境データを取得する
global $g_env;
$g_env = loadEnv($app_path . '\.env');


global $g_baseData; // 基本情報
$url_path = $_SERVER['REQUEST_URI'];
$segments = explode('/', $url_path);// '/'で区切る
$sliced_segments = array_slice($segments, 0, 3);// 2番目の節までを取得
$main_path = implode('/', $sliced_segments);// 配列を文字列に戻し、メインパスを作成
$class_name = $segments[3] ?? 'Neko'; // クラス名を取得
$method_name = $segments[4] ?? 'index'; // メソッド名を取得
$model_name = camelize($class_name); // モデル名

$g_baseData = [
		'url_path' => $url_path,
		'main_path' => $main_path,
		'class_name' => $class_name,
		'method_name' => $method_name,
		'model_name' => $model_name,
		'base_path' => $base_path,
		'app_path' => $app_path,
		'public_path' => $public_path,
];

require_once $app_path . '\autoload.php';
require_once $app_path . '\Controller/BaseXContoller.php';
require_once $app_path . '\Controller/' . $model_name . 'Controller.php';
$ctrl_class = 'App\\Controller\\' . $model_name . 'Controller';
$ctrlObj = new $ctrl_class;
echo $ctrlObj->$method_name();


