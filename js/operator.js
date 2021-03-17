// 设置画布初始属性
const canvasMain = document.querySelector('.canvasMain');
const canvas = document.getElementById('canvas');
const resultGroup = document.querySelector('.resultGroup');

// 设置画布宽高背景色
canvas.width = canvas.clientWidth;
canvas.height = canvas.clientHeight;
canvas.style.background = "#8c919c";

// 设置页面属性
const annotate = new LabelImage({
    canvas: canvas,
    scaleCanvas: document.querySelector('.scaleCanvas'),
    scalePanel: document.querySelector('.scalePanel'),
    annotateState: document.querySelector('.annotateState'),
    canvasMain: canvasMain,
    resultGroup: resultGroup,
    crossLine: document.querySelector('.crossLine'),
    labelShower: document.querySelector('.labelShower'),
    screenShot: document.querySelector('.screenShot'),
    screenFull: document.querySelector('.screenFull'),
    colorHex: document.querySelector('#colorHex'),
    toolTagsManager: document.querySelector('.toolTagsManager'),
    historyGroup: document.querySelector('.historyGroup')
});

// 初始化交互操作节点
const taskName = document.querySelector('.pageName');                   // 标注任务名称
const processIndex = document.querySelector('.processIndex');           // 当前标注进度
const processSum = document.querySelector('.processSum');               // 当前标注任务总数

// 给网页添加Ctrl+S自动保存快捷键
window.addEventListener("keydown", function (e) {
    if (e.key == "s" && (navigator.platform.match("Mac") ? e.metaKey : e.ctrlKey)) {
        e.preventDefault();
        if (annotate.Arrays.imageAnnotateMemory.length > 0) {
            upload(annotate.Arrays.imageAnnotateMemory);
            return true;
        }
        else {
            alert('当前图片未有有效的标定数据');
            return false;
        }
    }
}, false);

// 跳过当前图片
document.getElementById('break').onclick = function () {
    upload({});
}

// 手动提交图片
document.getElementById('upload').onclick = function () {
    upload(annotate.Arrays.imageAnnotateMemory);
}

//切换操作选项卡
let tool = document.getElementById('tools');
tool.addEventListener('click', function (e) {
    for (let i = 0; i < tool.children.length; i++) {
        tool.children[i].classList.remove('focus');
    }
    e.target.classList.add('focus');
    switch (true) {
        case e.target.className.indexOf('toolDrag') > -1:  // 拖拽
            annotate.SetFeatures('dragOn', true);
            break;
        case e.target.className.indexOf('toolRect') > -1:  // 矩形
            annotate.SetFeatures('rectOn', true);
            break;
        case e.target.className.indexOf('toolPolygon') > -1:  // 多边形
            annotate.SetFeatures('polygonOn', true);
            break;
        case e.target.className.indexOf('toolTagsManager') > -1:  // 标签管理工具
            annotate.SetFeatures('tagsOn', true);
            break;
        default:
            break;
    }
});

// 将数据上传到服务器
function upload(data) {
    let filename = taskName.textContent;
    let url = window.location.href + '/php/upload.php';
    let httpRequest = new XMLHttpRequest();
    let statuscheck = 0;
    data = { "Key": filename, "Value": JSON.stringify(data) }
    openBox('#loading', true);
    httpRequest.open('POST', url);
    httpRequest.setRequestHeader("Content-type", "application/json");
    httpRequest.send(JSON.stringify(data));
    httpRequest.onreadystatechange = function () {
        if (statuscheck > 0) {
            let datetime = new Date();
            let savedate = datetime.getFullYear() + "/" + datetime.getMonth() + "/" + datetime.getDate();
            savedate += " " + datetime.getHours() + ":" + datetime.getMinutes() + ":" + datetime.getSeconds();
            if (httpRequest.status == 200) {
                annotate.RecordOperation('add', savedate + "  " + "保存成功!", annotate.Arrays.resultIndex - 1, "save");
                console.log(savedate + ": " + filename + " 保存成功！");
                window.location.href = "/";
            }
            else console.error(savedate + ": " + filename + " 保存失败！")
        }
        else statuscheck++;
    };
}

//弹出框
function openBox(e, isOpen) {
    let el = document.querySelector(e);
    let maskBox = document.querySelector('.mask_box');
    if (isOpen) {
        maskBox.style.display = "block";
        el.style.display = "block";
    }
    else {
        maskBox.style.display = "none";
        el.style.display = "none";
    }
}

// 初始化页面内容
openBox('#loading', true);
processIndex.innerText = imgIndex;
let fileinfo = imgFiles[0].split('/')
taskName.innerText = fileinfo[fileinfo.length - 1];
let content = localStorage.getItem(taskName.textContent);
let img = imgFiles[0].name ? window.URL.createObjectURL(imgFiles[0]) : imgFiles[0];
content ? annotate.SetImage(img, JSON.parse(content)) : annotate.SetImage(img);
