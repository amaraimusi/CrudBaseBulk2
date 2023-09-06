<?php 

$base_path = dirname(__DIR__); // 基本パス
$app_path = $base_path . '\app';
$public_path = $base_path . '\public';

require_once $app_path . '\vendor\autoload.php';
require_once $app_path . '\functions.php';

// 環境データを取得する
global $g_env;
$g_env = loadEnv($app_path . '\.env');


//global $g_baseData; // 基本情報■■■□□□■■■□□□
$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', $url_path);// '/'で区切る
$sliced_segments = array_slice($segments, 0, 3);// 2番目の節までを取得
$main_path = implode('/', $sliced_segments);// 配列を文字列に戻し、メインパスを作成
$class_name = $segments[3] ?? 'Neko'; // クラス名を取得
$method_name = $segments[4] ?? 'index'; // メソッド名を取得
$model_name = camelize($class_name); // モデル名

// 各種パスを環境データにセットする
if(empty($g_env['url_path'])) $g_env['url_path'] = $url_path; // // 例→/CrudBaseBulk2/public/neko/index
if(empty($g_env['main_path'])) $g_env['main_path'] = $main_path; // // 例→/CrudBaseBulk2/public
if(empty($g_env['class_name'])) $g_env['class_name'] = $class_name; // // 例→neko
if(empty($g_env['method_name'])) $g_env['method_name'] = $method_name; // // 例→index
if(empty($g_env['model_name'])) $g_env['model_name'] = $model_name; // // 例→Neko
if(empty($g_env['base_path'])) $g_env['base_path'] = $base_path; // // 例→C:\Users\user\git\CrudBaseBulk2
if(empty($g_env['app_path'])) $g_env['app_path'] = $app_path; // // 例→C:\Users\user\git\CrudBaseBulk2\app
if(empty($g_env['public_path'])) $g_env['public_path'] = $public_path; // // 例→C:\Users\user\git\CrudBaseBulk2\public


require_once $app_path . '\autoload.php';
require_once $app_path . '\Controller/BaseXContoller.php';
require_once $app_path . '\Controller/' . $model_name . 'Controller.php';
$ctrl_class = 'App\\Controller\\' . $model_name . 'Controller';
$ctrlObj = new $ctrl_class;
echo $ctrlObj->$method_name();


