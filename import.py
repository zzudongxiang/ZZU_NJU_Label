import numpy as np
import uuid
import cv2
import os

# 待处理的文件所在的文件路径
FilePath = "data/"
# 图片压缩率，0~1之间的小数
CompressRate = 0.5
# 将图片复制到指定路径
CopyPath = "data/src/"
# 允许的图片文件类型
AllowFile = [
    ".bmp",
    ".tif",
    ".tiff",
    ".png",
    ".gif",
    ".jpg",
    ".jpeg",
    ".raw",
]


# 遍历全部的文件对象
def GetFileList(FilePath, FileList=[]):
    newDir = FilePath
    if os.path.isfile(FilePath):
        FileList.append(FilePath)
    elif os.path.isdir(FilePath):
        for File in os.listdir(FilePath):
            GetFileList(os.path.join(FilePath, File), FileList)
    return FileList


# 复制图片，并对图片进行压缩
def CopyImage(OldPath, NewPath, CompressRate=0.5):
    try:
        Image = cv2.imdecode(np.fromfile(OldPath, dtype=np.uint8), cv2.IMREAD_COLOR)
        Height, Width = Image.shape[:2]
        Image = cv2.resize(Image, (int(Width * CompressRate), int(Height * CompressRate)), interpolation=cv2.INTER_AREA)
        Image = cv2.resize(Image, (Width, Height))
        cv2.imwrite(NewPath, Image)
    except:
        print("\033[31mError At %s\033[0m" % OldPath)


# 主程序
if __name__ == "__main__":
    print("请输入图片所在父路径：")
    TempPath = input()
    if (os.path.isdir(TempPath)):
        FilePath = TempPath
    FileList = GetFileList(FilePath)
    print("在 %s 中共计发现 %d 张图片" % (FilePath, len(FileList)))
    print("输入 Y 开始处理:")
    Key = input()
    if (str.lower(Key) != 'y' and str.lower(Key) != 'yes'):
        print("用户终止了处理！")
        exit()
    else:
        print("文件将会复制到 %s" % CopyPath)
    Index = 0
    for File in FileList:
        DirPath = os.path.dirname(File)
        FileName = os.path.basename(File)
        Filter = os.path.splitext(FileName)[-1]
        if str.lower(Filter) in AllowFile:
            while True:
                NewName = str(uuid.uuid4().hex) + Filter
                NewPath = os.path.join(CopyPath, NewName)
                if not os.path.exists(NewPath):
                    print("%03.2f%%" % (Index / len(FileList) * 100), end="\r")
                    CopyImage(File, NewPath, CompressRate)
                    Index += 1
                    break
        else:
            print("\033[33mNot Image %s\033[0m" % File)
    print("导出数据成功！")
