<?php 
    try
    {
        // 获取对应的文件列表与对应的总数
        $ImageList = glob(dirname(__FILE__)."/../data/images/*");
        $JsonList = glob(dirname(__FILE__)."/../data/json/*");
        $SrcList = glob(dirname(__FILE__)."/../data/src/*");
        $TempList = glob(dirname(__FILE__)."/../data/tmp/*");
        $ImageCount = count($ImageList);
        $JsonCount = count($JsonList);
        $SrcCount = count($SrcList);
        $TempCount = count($TempList);

        // 清理数据功能
        if($_GET["Op"] == "Clear")
        {
            // 复原对应的图像数据与多余的Json文件
            $ClearIndex = 0;
            $TempJsonList = $JsonList;
            foreach($ImageList as $Image)
            {
                $Suffix = substr(strrchr(basename($Image), '.'), 1);
                $JsonName = basename($Image, ".".$Suffix).".json";
                $Flag = false;
                foreach($TempJsonList as $Json)
                {
                    $Msg = "basename($Json)";
                    if(basename($Json) == $JsonName)
                    {
                        $Flag = true;
                        $TempJsonList = array_diff($TempJsonList, [$Json]);
                        break;
                    }
                }
                if(!$Flag)
                {
                    // 还原图像文件位置, 并清除数据库中的数据
                    $FileName = basename($Image);
                    rename($Image, dirname(__FILE__).'/../data/src/'.$FileName);
                    $ClearIndex += 1;
                }
            }

            // 清除json文件对应的数据
            foreach($TempJsonList as $Json)
            {
                unlink($Json);
                $ClearIndex += 1;
            }

            // 清除tmp的缓存文件
            foreach($TempList as $Temp)
            {
                unlink($Temp);
                $ClearIndex += 1;
            }

            // 输出信息
            $Msg = "<script>alert('共计处理了 $ClearIndex  条数据！');location.href='/php/admin.php';</script>";
        }
        else
        {
            // 显示数据汇总信息
            $Query = "SELECT COUNT(*) FROM `FileList` WHERE `Status` = 0;";
            $Msg = "<h1>======= 当前处理进度 =======</h1><h2>";
            $Msg .= "<span style='color:#f6033c'>待处理：$SrcCount 个</span> / ";
            $Msg .= "<span style='color:#689a39'>已处理：$JsonCount 个</span></h2>";

            // 显示文件汇总信息
            $Msg .= "<h4>图片文件: $ImageCount 个 | 数据文件: $JsonCount 个 | 临时文件: $TempCount 个";

            // 显示文件列表
            $WebImage = "";
            if($ImageCount > 0)
            {
                foreach($ImageList as $Temp) 
                {
                    $Temp = basename($Temp);
                    $WebImage .= "<a target='view_window' class='text' href='data/images/$Temp'>$Temp</a>";
                }
            }
            $WebJson = "";
            if($JsonCount > 0)
            {
                foreach($JsonList as $Temp) 
                {
                    $Temp = basename($Temp);
                    $WebJson .= "<a target='view_window' class='text' href='data/json/$Temp'>$Temp</a>";
                }
            }
            $WebSrc = "";
            if($SrcCount > 0)
            {
                foreach($SrcList as $Temp) 
                {
                    $Temp = basename($Temp);
                    $WebSrc .= "<a target='view_window' class='text' href='data/src/$Temp'>$Temp</a>";
                }
            }
            $Install = "";
        }
    }
    catch(Exception $Ex)
    {
        $Install = "<script>alert('".$Ex->getMessage()."');</script>";
    }
?>

<!doctype html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>管理员界面</title>
    <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico">
    <style>
    button {
        color: #3d6597;
        text-decoration: none;
        font-size: 24px;
    }

    p {
        white-space: pre-wrap;
        font-size: 16px;
        color: #943336
    }

    .text {
        display: block;
        word-break: keep-all;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        -o-text-overflow: ellipsis;
        -icab-text-overflow: ellipsis;
        -khtml-text-overflow: ellipsis;
        -moz-text-overflow: ellipsis;
        -webkit-text-overflow: ellipsis;
        text-decoration: none;
        color: blue;
    }
    </style>
    <?php echo $Install; ?>
</head>

<body style="text-align: center; background-color: #cccccc; width: 1000px; margin: auto;">
    <?php echo $Msg; ?>
    <h1> ======= 储存文件对象 ======= </h1>
    <div style="width: 550px; height: 300px; margin: auto; text-align: left;">
        <div style="width:31.4%; height:300px; float:left; background-color: #269967; padding: 5px">
            <b>data/src/*</b>
            <hr>
            <div style="height:260px; overflow-x:hidden; overflow-y:auto;"><?php echo $WebSrc;?></div>
        </div>
        <div style="width:31.4%; height:300px; float:left; background-color: #fdff9e; padding: 5px">
            <b>data/images/*</b>
            <hr>
            <div style="height:260px; overflow-x:hidden; overflow-y:auto;"><?php echo $WebImage;?></div>
        </div>
        <div style="width:31.4%; height:300px; float:left; background-color: #a0cbfd; padding: 5px">
            <b>data/json/*</b>
            <hr>
            <div style="height:260px; overflow-x:hidden; overflow-y:auto;"><?php echo $WebJson;?></div>
        </div>
    </div>
    <div style="clear:all"></div>
    <h1> ======= 管理操作指南 ======= </h1>
    <div style="width: 550px; margin: auto; text-align: left">
        <p>+ 首先点击 <b>校准数据</b>，可以查找到一些不在记录中的数据文件；</p>
        <p>+ 对于长时间没有完成标签的数据，可以使用 <b>清理数据</b> 功能清理部分僵尸数据；</p>
        <p>+ 需要注意，在 <b>清理数据</b> 时需要确保当前无人进行打标签操作，否则会导致该成员所记录的标签数据失败。</p>
    </div>
    <h1> ======= 可执行的操作 ======= </h1>
    <span>
        <button type="button" onclick="location.href='/php/admin.php?Op=Clear'"> >>清理数据<< </button>
                <button type="button" onclick="location.href='/'"> >>返回主页<< </button>
    </span>
    <h3>
        作者：<a href="mailto:zzudongxiang@163.com">zzudongxiang@163.com</a>
        |
        感谢：<a href="https://github.com/rachelcao277">rachelcao277</a>
    </h3>
</body>

</html>