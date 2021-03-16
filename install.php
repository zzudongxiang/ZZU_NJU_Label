<?php 
# 写入配置文件然后打开数据库对象
if(!empty($_GET))
{
    file_put_contents(dirname(__FILE__)."/php/mysql.json", json_encode($_GET));
    require_once dirname(__FILE__)."/php/core.php";

    # 在对应的数据库创建数据表，执行sql程序
    try
    {
        ExecuteSQLScript(dirname(__FILE__)."/php/install.php");
        $Msg .= "<script>location.href='/';</script>";
    }
    catch(Exception $Ex)
    {
        $Msg = "<script>alert('".$Ex->getMessage()."');</script>";
        $Msg .= "<script>location.href='/install.php';</script>";
    }
}
?>

<!doctype html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>初始化数据库</title>
    <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico">
    <style>
    td {
        font-size: 26px;
    }

    input {
        font-size: 24px;
    }

    .main {
        width: 500px;
        height: 300px;
        margin: auto;
        font-size: 18px;
        padding: 5px;
    }
    </style>
</head>

<body style="text-align: center; background-color: #cccccc; width: 1000px; margin: auto;">
    <?php echo $Msg; ?>
    <h1>配置数据库</h1>
    <hr>
    <form name="Install" action="/install.php" method="get" class="main">
        <table style="text-align: left; width: 300px; margin:auto">
            <colgroup>
                <col width=100px>
                <col width=200px>
            </colgroup>
            <tr>
                <td>Host: </td>
                <td><input type="text" name="Host" value="localhost"></td>
            </tr>
            <tr>
                <td>UserName: </td>
                <td><input type="text" name="UserName" value="root"></td>
            </tr>
            <tr>
                <td>PassWord: </td>
                <td><input type="password" name="PassWord"></td>
            </tr>
            <tr>
                <td>DB_Name: </td>
                <td> <input type="text" name="DB_Name" value="material"></td>
            </tr>
            <tr>
                <td>Encode: </td>
                <td><input type="text" name="Encode" value="utf8"></td>
            </tr>
        </table>
        <hr>
        <input type="submit" value="开始安装">
    </form>

    <h3>
        作者：<a href="mailto:zzudongxiang@163.com">zzudongxiang@163.com</a>
        |
        感谢：<a href="https://github.com/rachelcao277">rachelcao277</a>
    </h3>
</body>

</html>