# ZF3_mock-front
自分用に、ZendFramework3 を使ってAPI-CentricなアプローチでWebアプリを作る際の mock を組む時のための、Frontサーバ側雛形を用意した。

- 筆者のブログ記事「ZF3で空のプロジェクトを作成する自分なりの手順」  
http://crapporin.blogspot.jp/2017/05/zf3.html  
にて示した空プロジェクトの状態から、都度必要とするモジュールを composer require しながら作成しています。
- APIサーバ https://github.com/Kimita/ZF3_mock-api とセットで使用します。
- PHP7.1 を使ったEclipseプロジェクトとして作成したので、その設定ファイルが含まれています。

## 参考にした書籍
下記の書籍から学んだ内容をベースにして、自分なりに mockアプリ作成時に取り揃えておきたい要素をまとめたものです。

- 書名: Zend Framework 2 Application Development
- 著者: Christopher Valles
- 発行: 2013年10月25日

# 盛り込んであるもの

- flashmessenger を使ったメッセージ表示処理
- APIサーバへのリクエストおよびそのレスポンスを受け取る処理
- zend-form および zend-filter を使ったLoginフォームの作例
- zend-session を使って Login/Logout のセッション管理
