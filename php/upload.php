<?php

# 获取post提交的数据
$Data = json_decode(file_get_contents("php://input"), true);
$ImageName = $Data["Key"];
$Suffix = substr(strrchr($ImageName, '.'), 1);
$FileID = basename($ImageName, ".".$Suffix);
$Data = json_decode($Data["Value"], true);
$Data["ImageName"] = $ImageName;

# 将数据储存到本地
try
{
    // 将数据保存到本地
    $JsonName = dirname(__FILE__)."/../data/json/".$FileID.".json";
    $File = fopen($JsonName, "w");
    fwrite($File, json_encode($Data, JSON_PRETTY_PRINT));
    fclose($File);
    http_response_code(200);
    die($Query);
}
catch(Exception $Ex)
{
    http_response_code(500);
    die($Ex->getMessage());
}