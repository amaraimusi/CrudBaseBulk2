<?php 

require_once 'vendor/autoload.php';
require_once 'functions.php';

// 環境データを取得する
global $g_env;
$g_env = loadEnv('.env');


global $g_baseData; // 基本情報
$url_path = $_SERVER['REQUEST_URI'];
$segments = explode('/', $url_path);// '/'で区切る
$sliced_segments = array_slice($segments, 0, 3);// 2番目の節までを取得
$main_path = implode('/', $sliced_segments);// 配列を文字列に戻し、メインパスを作成
$class_name = $segments[3] ?? 'Neko'; // クラス名を取得
$method_name = $segments[4] ?? 'index'; // メソッド名を取得
$model_name = camelize($class_name); // モデル名
$base_path = dirname(__FILE__); // 基本パス

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


