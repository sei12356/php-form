<?php 
//エラー確認
//ini_set 「エラーを表示する」設定ということ。1を0にするとエラーを表示しない。
ini_set("display_errors",1);
//全ての PHP エラーを表示する (Changelog を参照ください)
error_reporting(E_ALL);

//DB接続 PDO
function insertContact($data){
    //require = require は include とほぼ同じですが、失敗した場合に E_COMPILE_ERROR レベルの致命的なエラーも発生する
    require 'db_connection.php';
    //入力 DB保存 prepare,bind,execute(配列(全て文字列))
    $params = [
        'id'=> null,
        'your_name'=> $data['your_name'],
        'email' => $data['email'],
        'url'=> $data['url'],
        'gender'=> $data['gender'],
        'age'=> $data['age'],
        'contact'=> $data['contact'],
        'created_at'=> null
    ];

    $count = 0;
    $columns = '';
    $values = '';

    //array_keys()を使用するとkeyの部分ももっていくことができる
    //.= 文字の連結
    foreach(array_keys($params) as $key){
        if($count++>0){
            $columns .= ',';
            $values .= ',';
        }
        $columns .= $key;
        $values .= ':' .$key;
    }


    //insert into = レコードの挿入
    //INSERT  INTO  テーブル名 ( テーブルの列名1 [ , テーブルの列名2 ]・・・)  VALUES ( ‘値1′ [ , ‘値2’ ]・・・);
    $sql = 'insert into contacts ('. $columns .')values('. $values .')';//プレースフォルダー

    //prepare() = sqlを利用する準備
    $stmt = $pdo -> prepare($sql);//プリペアードステートメント
    //execute() = 実行する
    $stmt -> execute($params);//実行    
}
?>