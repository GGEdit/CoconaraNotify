<?php
require_once('linenotify.php');
require_once('pdowrapper.php');

date_default_timezone_set('Asia/Tokyo');
define("LINE_NOTIFY_TOKEN","your key");
define("TARGET_ADDRESS", "https://coconala.com/requests/categories/11?ref=header&recruiting=true&categoryId=11&page=1");
define("REQUEST_MATCH_PATTERN", "/requestId:(.*?),title:\"(.*?)\",requestContent:\"(.*?)\",(.*?),created:(.*?),/");
define("REQUEST_PAGE", "https://coconala.com/requests/");

//LINENotifyインスタンスを作成
$lineNotify = new LineNotify(LINE_NOTIFY_TOKEN);
//PDOWrapperインスタンスを作成
$pdo = new PDOWrapper("host name", "database name", "username", "password");
if(!$pdo->connect()){
    echo "DB接続失敗..!";
    return;
}
//ココナラ、IT・プログラミング の仕事・相談の１ページ目を取得
$contentPageData = file_get_contents(TARGET_ADDRESS);
//案件をリスト化
preg_match_all(REQUEST_MATCH_PATTERN, $contentPageData, $requestListMatch);
//リスト数をカウント
$listCount = count($requestListMatch[0]);
//処理
for($i = 0; $i < $listCount; $i++){
    if(!$pdo->exists("requests", "requestId", $requestListMatch[1][$i], PDO::PARAM_STR)){
        $createdDateTime = date("Y-m-d H:i:s", $requestListMatch[5][$i]);
        $requestPage = REQUEST_PAGE . $requestListMatch[1][$i];
        //DBに存在しなければ、リクエストデータ挿入
        $pdo->insert("requests", "requestId", $requestListMatch[1][$i], PDO::PARAM_STR);
        $message = "\n【投稿日時】" . $createdDateTime . "\n" . "【タイトル】\n" . $requestListMatch[2][$i] . "\n" . "【案件概要】\n" . $requestListMatch[3][$i] . "\n" . "【詳細】\n" . $requestPage;
        //LINE通知
        $lineNotify->sendMessage($message);
    }
}
echo "OK";
?>