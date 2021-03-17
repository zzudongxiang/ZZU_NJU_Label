<?php
    try
    {
        require_once dirname(__FILE__)."/php/core.php";
        $FileList = glob(dirname(__FILE__).'/data/src/*');
        if(!empty($FileList))
        {
            require_once dirname(__FILE__)."/php/image.php";
            
            // 从src中随机拿出一个文件, 并将其移动到images文件夹中
            $FilePath = $FileList[array_rand($FileList)];
            $FileInfo = pathinfo($FilePath);
            $DirPath = $FileInfo["dirname"];
            $FileName = $FileInfo["basename"];
            (new image($FilePath, 0.5)) -> compressImg($DirPath."/../tmp/".$FileName);
            rename($FilePath, "$DirPath/../images/$FileName");

            // 将记录写入到数据库中
            require_once dirname(__FILE__)."/php/core.php";
            $UpdateTime = date('Y-m-d H:i:s', time());
            $Query = "INSERT INTO `FileList` (`UpdateTime`, `FileName`) VALUES ('$UpdateTime', '$FileName') ON DUPLICATE KEY UPDATE `Status` = 0;";
            ExecuteNonQuery($Query); 
        }
        else $FileName = "../../images/sample.png";
        $Install = "";
    }
    catch(Exception $Ex)
    {
        $Install = "<script>alert(\"".$Ex->getMessage()."\");</script>";
    }
?>

<!doctype html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"
        content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>样品标注工具</title>
    <link rel="stylesheet" href="./css/preloader.css">
    <link rel="stylesheet" href="./css/style.css">
    <link rel="stylesheet" href="./css/font-awesome.min.css">
    <link rel="stylesheet" href="./css/switch.css">
    <link rel="Shortcut Icon" type="image/x-icon" href="favicon.ico">
    <?php echo $Install; ?>
</head>

<body>
    <div id='preloader'>
        <div class='loader' hidden>
            <img src='images/loader.gif' alt>
        </div>
    </div>
    <div class="LabelImage">
        <div class="toolHead">
            <div class="toolMuster">
                <div class="logoGroup">
                    <div class="logo"></div>
                </div>
                <div class="selectOperation">
                    <div class="pageControl">
                        <div class="pageInfo inline-block">
                            <p style="display: none;" class="pageName" title="图片名称">File Name</p>
                            <p style="display: none;" class="nameProcess" title="图片位置"><span
                                    class="processIndex">0</span> / <span class="processSum">0</span></p>
                        </div>
                    </div>
                </div>
                <div class="assistTool">
                    <div class="generalFeatures">
                        <p class="featureList crossLine" title="十字线开关">
                            <input class="mui-switch mui-switch-anim" type="checkbox">
                            <span>十字线</span>
                        </p>
                        <p class="featureList labelShower focus" title="标注结果显示开关">
                            <input class="mui-switch mui-switch-anim" type="checkbox" checked="true">
                            <span>标注结果</span>
                        </p>
                        <p class="featureList screenShot" title="标注内容截图">
                            <i class="bg"></i>
                            <span>快照</span>
                        </p>
                        <p class="featureList screenFull" title="全屏开关">
                            <i class="bg"></i>
                            <span>全屏</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="canvasMain">
            <div class="toolFeatures">
                <div id="assistFeatures">
                    <div class="toolSet" id="break" style="background: url('images/break.png') no-repeat +5px +5px"
                        title="跳过"></div>
                    <div class="toolSet" id="upload" style="background: url('images/upload.png') no-repeat +5px +5px"
                        title="上传"></div>
                </div>
                <div class="separator"></div>
                <div id="tools">
                    <div class="toolSet toolDrag" title="图片拖拽"></div>
                    <div class="toolSet toolTagsManager"><span class="icon-tags"></span></div>
                    <div class="toolSet toolRect" title="矩形工具"></div>
                    <div class="toolSet toolPolygon focus" id="default-btn" title="多边形工具"></div>
                </div>
            </div>
            <div class="canvasContent">
                <canvas id="canvas"></canvas>
                <div class="scaleBox">
                    <div class="scaleCanvas"></div>
                    <div class="scalePanel"></div>
                </div>
            </div>
            <div class="commentResult">
                <div class="resultArea">
                    <p class="title">标注结果 (<span class="resultLength">0</span>)</p>
                    <div class="resultList_head">
                        <div class="headChildren">
                            <p class="headName">名称</p>
                            <p class="headEdit">修改</p>
                            <p class="headDelete">删除</p>
                            <p class="headDisplay">显/隐</p>
                        </div>
                    </div>
                    <div class="resultGroup">
                    </div>
                    <div class="resultSelectLabel">
                        <p class="selectLabelTip" hidden>请先创建标签</p>
                        <ul class="selectLabel-ul">
                        </ul>
                        <div class="closeLabelManage"><span class="icon-remove-sign"></span></div>
                    </div>
                </div>
                <div class="historyContent">
                    <p class="title">历史记录</p>
                    <div class="historyGroup">
                    </div>
                </div>
                <div class="tabBtn focus"><span class="icon-double-angle-right"></span></div>
            </div>
            <div class="labelManage">
                <div class="labelManage-Info">
                    <div class="labelManage-menu">
                        <div class="labelManage-search"><input type="text" class="labelSearch-input"
                                placeholder="按回车搜索" /></div>
                        <div class="labelManage-createLabel"><button
                                class="button btn-primary labelManage-createButton">创建</button></div>
                    </div>
                    <div class="labelManage-subList">标签列表：</div>
                    <div class="labelManage-group">
                        <p class="labelTip" hidden>请先创建标签</p>
                        <ul class="labelManage-ul">
                        </ul>
                    </div>
                </div>
                <div class="labelManage-create" hidden>
                    <div class="labelManage-Title">创建标签</div>
                    <div class="labelCreate labelCreate-name">
                        <label>标签名称：</label>
                        <input type="text" class="labelCreate-nameInput">
                    </div>
                    <div class="labelCreate labelCreate-color">
                        <label>标签颜色：</label>
                        <span class="colorPicker" id="colorPicker"></span>
                        <input class="colorHex" id="colorHex" value="#ff0000" data-r="255" data-g="0" data-b="0"
                            readonly>
                    </div>
                    <div class="labelCreate">
                        <button class="button btn-error removeLabel" title="删除标签">删除</button>
                    </div>
                    <div class="labelCreateButtons">
                        <button class="button btn-success addLabel">确定</button>
                        <button class="button btn-default closeAdd">取消</button>
                    </div>
                </div>
                <div class="closeLabelManage"><span class="icon-remove-sign"></span></div>
            </div>
        </div>

        <div class="mask_box" hidden></div>
        <div class="loading_box" hidden id="loading">
            <div class="loaderSpinner">
                <span class="icon-spinner"></span>
            </div>
            <b class="closes"></b>
        </div>
    </div>

    <script src="./js/preloader.js"></script>
    <script src="./js/colorpicker.js"></script>
    <script src="./js/webAnnotate.js"></script>
    <script>
    let imgFiles = ['data/tmp/<?php echo $FileName ?>'];
    let imgSum = imgFiles.length;
    let imgIndex = 1;
    </script>
    <script src="./js/operator.js"></script>
</body>

</html>