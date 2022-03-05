## 云影评切片上传系统

本系统可实现视频本地切片处理，上传到存储桶内，返回视频m3u8地址。
技术栈：vue，php，易语言

## 搭建

### 后台

- 创建站点，导入根目录下数据库install.sql，站点目录public

### web前端

- npm install --registry=https://registry.npm.taobao.org

> 若出现无法安装，请先安装git，配置 git ssh
> 找到 “C:\Users\你的用户名称 ” 根目录下，打开“.gitconfig”，在最后面添加
> 
    [url "https://github.com/nhn/raphael.git/"]
    insteadOf = git://github.com/nhn/raphael.git/

-   配置项

>   .env.development （开发环境） .env.production （生产环境） 内配置相关信息

​	其余安装问题，查看 [VUE-ELEMENT-ADMIN文档](https://panjiachen.gitee.io/vue-element-admin-site/zh/guide/#%E5%AE%89%E8%A3%85)

## 声明

>   部分源码内因涉及到现有项目的通讯加密算法，所以源码被加密，有相关需求者加群联系群主。

## 交流

Q群：615475093
