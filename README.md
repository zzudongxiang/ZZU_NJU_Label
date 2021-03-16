# 打标签网站

### 1. 安装需求

```bash
# 安装apache2
sudo apt install -y apache2
# 安装php版本应大于7.2
sudo apt install -y php
# 安装php插件
sudo apt install -y php-mysql php-xml php-mbstring php-gd
# 建议python版本大于3.7
sudo apt install -y python
```

### 2. 安装帮助

- **创建数据库`Material`**
- **可选：手动执行`./php/install.sql`脚本**
- **打开`/install.php`页面配置数据库相关信息**
- **将待处理的全部图片放在`./data/src/`路径下**
- **※：可以使用`./import.py`快速遍历指定文件夹**

### 3. 开始使用

- 打开对应的网页开始使用

![sample](README.assets/sample.png)