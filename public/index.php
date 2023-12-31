<?php 

$base_path = dirname(__DIR__); // 基本パス
$app_path = $base_path . '\app';
$public_path = $base_path . '\public';

require_once $app_path . '\vendor\autoload.php';
require_once $app_path . '\functions.php';

// 環境データを取得する
global $g_env;
$g_env = loadEnv($app_path . '\.env');

$scheme_url = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http'; // URLスキーム
$origin_url = $scheme_url . '://' . $_SERVER['HTTP_HOST']; // オリジン
$url_path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$segments = explode('/', $url_path);// '/'で区切る
$sliced_segments = array_slice($segments, 0, 3);// 2番目の節までを取得
$main_path = implode('/', $sliced_segments);// 配列を文字列に戻し、メインパスを作成
$def_model_name = $g_env['def_model_name']; // デフォルトモデル名
$class_name = $segments[3] ?? $def_model_name; // モデル名を取得
if(empty($class_name)) $class_name = $def_model_name;
$method_name = $segments[4] ?? 'index'; // メソッド名を取得
$model_name = camelize($class_name); // モデル名

// 各種パスを環境データにセットする
if(empty($g_env['scheme_url'])) $g_env['scheme_url'] = $scheme_url; // 例→http, https
if(empty($g_env['origin_url'])) $g_env['origin_url'] = $origin_url; // 例→http://localhost, https://example
if(empty($g_env['url_path'])) $g_env['url_path'] = $url_path; // 例→/CrudBaseBulk2/public/neko/index
if(empty($g_env['main_path'])) $g_env['main_path'] = $main_path; // 例→/CrudBaseBulk2/public
if(empty($g_env['class_name'])) $g_env['class_name'] = $class_name; // 例→neko
if(empty($g_env['method_name'])) $g_env['method_name'] = $method_name; // 例→index
if(empty($g_env['model_name'])) $g_env['model_name'] = $model_name; // 例→Neko
if(empty($g_env['base_path'])) $g_env['base_path'] = $base_path; // 例→C:\Users\user\git\CrudBaseBulk2
if(empty($g_env['app_path'])) $g_env['app_path'] = $app_path; // 例→C:\Users\user\git\CrudBaseBulk2\app
if(empty($g_env['public_path'])) $g_env['public_path'] = $public_path; // 例→C:\Users\user\git\CrudBaseBulk2\public

require_once $app_path . '\autoload.php';

global $g_dao;
$g_dao = new CrudBase\PdoDao([
		'host' => $g_env['DB_HOST'], // ホスト名
		'db_name' => $g_env['DB_NAME'], // データベース名
		'user' => $g_env['DB_USER'], // DBユーザー名
		'pw' => $g_env['DB_PASS'], // DBパスワード
]);

require_once $app_path . '\Controller/' . $model_name . 'Controller.php';
$ctrl_class = 'App\\Controller\\' . $model_name . 'Controller';
$ctrlObj = new $ctrl_class;
echo $ctrlObj->$method_name();


