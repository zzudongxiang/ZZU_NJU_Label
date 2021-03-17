<?php require_once dirname(__FILE__)."/php/core.php";

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
    $JsonName = dirname(__FILE__)."/data/json/".$FileID.".json";
    $File = fopen($JsonName, "w");
    fwrite($File, json_encode($Data, JSON_PRETTY_PRINT));
    fclose($File);

    // 修改数据库记录
    $UpdateTime = date('Y-m-d H:i:s', time());
    $Query = "INSERT INTO `FileList` (`UpdateTime`, `FileName`) VALUES ('$UpdateTime', '$ImageName') ON DUPLICATE KEY UPDATE `Status` = `Status` + 1;";
    ExecuteNonQuery($Query);
    http_response_code(200);
    die($Query);
}
catch(Exception $Ex)
{
    $MSG = "Error Date: ".date('Y-m-d H:i:s', time())."\r\n";
    $MSG .= $Ex->getMessage()."\r\n"; 
    $MSG .= "------------------------------\r\n";
    file_put_contents(dirname(__FILE__)."/php/sql.log", $MSG, FILE_APPEND|LOCK_EX);
    http_response_code(500);
    die($Ex->getMessage());
}

