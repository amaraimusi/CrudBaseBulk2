# CrudBase

CrudBaseシリーズの管理画面を支援するライブラリです。
汎用的に役に立つ関数だけを集めているため、CrudBaseシリーズではない他のプロジェクトでもご利用できます。
各機能はCrudBase.phpに集約しているので、CrudBase.phpファイルを直接ご確認ください。

since 2013-6-1 | 2023-4-4





## クライアントプロジェクトから当パッケージの更新する例

一般的なパッケージ更新と同じです。

下記はcrud_base_l9というLaravel9で開発された見本管理システムにて、「amaraimusi/CrudBase」パッケージを更新する例です。


```
cd git/crud_base_l9/dev
composer update amaraimusi/CrudBase

```

--------------


## 当クラスの使い方

CrudBase.phpに各機能を集約しています。CrudBase.php内で定義されている各種メソッドを関数のようにご利用ください。


```
use CrudBase\CrudBase;


echo CrudBase::getVersion(); // バージョンを表示する。

```

--------------


## CrudBaseプロジェクトの変更をクライアントプロジェクトへ反映する流れ


当プロジェクトであるCrudBaseを変更したらGitでコミット＆プッシュします。普段使用しているGitと同じ方法です。

あとは、クライアントプロジェクト側でCrudBaseパッケージを更新するだけです。
上記の「クライアントプロジェクトから当パッケージの更新する例」を参考にしてください。


--------------



## PHP Unitでテストを実行する方法



amaraimusi/CrudBaseのプロジェクトのディレクトリへ移動します。


```
cd git/CrudBase

```

以下のコマンドでtests内にあるすべてのテストファイルのテストコードを実行します。


```
./vendor/bin/phpunit

```



--------------