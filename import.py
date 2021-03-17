#!/usr/bin/python3
# -*- coding: UTF-8 -*-
import numpy as np
import zipfile
import shutil
import time
import json
import uuid
import cv2
import os

# 待处理的文件所在的文件路径
FilePath = "Seafile"
# 将图片复制到指定路径
CopyPath = "src"
# 归一化图像大小的格式
ImageSize = (1024, 768)
# 每PackCount张图片压缩一次
PackCount = 2500
# 允许的图片文件类型
AllowFile = [
    ".bmp",
    ".tif",
    ".tiff",
    ".png",
    ".gif",
    ".jpg",
    ".jpeg",
]
# 创建临时文件路径
TempPath = CopyPath + "/tmp"


# 写入日志文件
def WriteLog(LogText):
    with open(CopyPath + '/Runtime.log', 'a', encoding='utf-8') as LogFile:
        LogFile.write(LogText + "\r\n")
    return LogText


# 遍历全部的文件对象
def GetFileList(FilePath, FileList=[]):
    newDir = FilePath
    if os.path.isfile(FilePath):
        FileList.append(FilePath)
    elif os.path.isdir(FilePath):
        for File in os.listdir(FilePath):
            GetFileList(os.path.join(FilePath, File), FileList)
    return FileList


# 复制图片，并对图片进行压缩，返回是否压缩成功
def CopyImage(OldPath, NewPath):
    try:
        Image = cv2.imdecode(np.fromfile(OldPath, dtype=np.uint8), cv2.IMREAD_COLOR)
        Height, Width = Image.shape[:2]
        W_Rate = Width / ImageSize[0]
        H_Rate = Height / ImageSize[1]
        Rate = H_Rate if H_Rate > W_Rate else W_Rate
        Width = int(Width / Rate)
        Height = int(Height / Rate)
        Image = cv2.resize(Image, (Width, Height))
        NewImage = np.zeros((ImageSize[1], ImageSize[0], 3))
        X = int(abs(Width - ImageSize[0]) / 2)
        Y = int(abs(Height - ImageSize[1]) / 2)
        NewImage[Y:Height + Y, X:Width + X, :] = Image
        cv2.imwrite(NewPath, NewImage)
        return True
    except:
        print("\033[31m%s\033[0m" % WriteLog("Error At %s" % OldPath))
        return False


# 创建并打开Zip压缩文件
def OpenZip(ZipIndex=0):
    ZipDict = {"DateTime": time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())}
    ZipFile = zipfile.ZipFile(CopyPath + "/Part-" + str(ZipIndex) + ".zip", "w")
    if not os.path.exists(TempPath):
        os.makedirs(TempPath)
    return ZipFile, ZipDict


# 关闭并保存Zip压缩文件
def CloseZip(ZipFile, ZipDict, FileDict):
    FileInfo = json.dumps(ZipDict, ensure_ascii=False, indent=4)
    ZipFile.writestr("FileInfo.json", FileInfo)
    ZipFile.close()
    with open(CopyPath + '/FileInfo.json', 'w', encoding='utf-8') as JsonFile:
        JsonFile.write(json.dumps(FileDict, ensure_ascii=False, indent=4))
    if os.path.exists(TempPath):
        shutil.rmtree(TempPath)
    print("%s Pack Done!" % ZipFile.filename)


# 获取一个不重复的uuid名字
def GetFileName():
    while True:
        NewName = str(uuid.uuid4().hex) + ".bmp"
        NewPath = os.path.join(TempPath, NewName)
        if not os.path.exists(NewPath):
            break
    return NewPath, NewName


# 主程序
if __name__ == "__main__":
    FileList = GetFileList(FilePath)
    if not os.path.exists(TempPath):
        os.makedirs(TempPath)
    FileDict = {"DateTime": time.strftime("%Y-%m-%d %H:%M:%S", time.localtime())}
    ZipFile, ZipDict = OpenZip(1)
    WriteLog("=" * 50)
    for Index in range(len(FileList)):
        DirPath = os.path.dirname(FileList[Index])
        FileName = os.path.basename(FileList[Index])
        Filter = os.path.splitext(FileName)[-1]
        if str.lower(Filter) in AllowFile:
            FilePath, FileName = GetFileName()
            if CopyImage(FileList[Index], FilePath):
                FileDict[FileName] = ZipDict[FileName] = FileList[Index]
                ZipFile.write(FilePath, FileName)
                if Index > 0 and Index % PackCount == 0:
                    CloseZip(ZipFile, ZipDict, FileDict)
                    ZipFile, ZipDict = OpenZip(int(Index / PackCount) + 1)
                print("Percent: %03.2f%% - %d" % (Index / len(FileList) * 100, Index + 1), end="\r")
        else:
            if str.lower(Filter) in [".zip", ".rar", ".7z", ".tar"]:
                print("\033[31m%s\033[0m" % WriteLog("Zip File %s" % FileList[Index]))
            else:
                print(WriteLog("Unknown File %s" % FileList[Index]))
    CloseZip(ZipFile, ZipDict, FileDict)
    print("Process Images Done!")
