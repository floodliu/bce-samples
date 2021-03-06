<?php

require "../../authorization/auth.php";

// 第一步：生成认证字符串

$ak = "";  // AccessKeyId
$sk = "";  // SecretAccessKey
$endpointName = "";  // 物管理对应的 iothub 实例名称

$method = "PUT";
$host = "iotdm.gz.baidubce.com";
$uri = "/v2/iot/management/endpoint/{$endpointName}/device";
$params = array("remove" => "");

date_default_timezone_set('UTC');
$timestamp = new \DateTime();
$expirationInSeconds = 3600;

$authorization = generateAuthorization($ak, $sk, $method, $host, $uri, $params, $timestamp, $expirationInSeconds);

// 第二步：构造HTTP请求的header、body等信息

$url = "http://{$host}{$uri}?remove";
$timeStr = $timestamp->format("Y-m-d\TH:i:s\Z");
$head =  array(
    "Content-Type:application/json",
    "Authorization:{$authorization}",
    "x-bce-date:{$timeStr}"
);
$body = array(
    "cleanThing" => true,  // true：删除iothub中的相应配置
    "deviceOperation" => array(
        "devices" => array("设备名称1", "设备名称2")
    )
);
$bodyStr = json_encode($body);

// 第三步：发送HTTP请求，并输出响应信息。

$curlp = curl_init();
curl_setopt($curlp, CURLOPT_CUSTOMREQUEST, $method);
curl_setopt($curlp, CURLOPT_URL, $url);
curl_setopt($curlp, CURLOPT_HTTPHEADER, $head);
curl_setopt($curlp, CURLOPT_POSTFIELDS, $bodyStr);

curl_setopt($curlp, CURLINFO_HEADER_OUT, 1);
curl_setopt($curlp, CURLOPT_RETURNTRANSFER, 1);

$response = curl_exec($curlp);
$request = curl_getinfo($curlp, CURLINFO_HEADER_OUT);
$status = curl_getinfo($curlp, CURLINFO_HTTP_CODE);
curl_close($curlp);

print("request: {$request}\n");
print("request body: {$bodyStr}\n");
print("status: {$status}\n");
print("response: {$response}\n");

?>