<?php

define("API_ENDPOINT", "https://notify-api.line.me/api/notify");

class LineNotify {
    private $apiToken;

    public function __construct($apiToken){
        $this->apiToken = $apiToken;
    }

    public function sendMessage($message){
        $query = http_build_query(["message" => $message]);
        $header = [
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer ' . $this->apiToken,
            'Content-Length: ' . strlen($query),
        ];
        $ch = curl_init(API_ENDPOINT);
        $option = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => $header,
            CURLOPT_POSTFIELDS => $query
        ];
        curl_setopt_array($ch, $option);
        curl_exec($ch);
        curl_close($ch);
    }
}

?>