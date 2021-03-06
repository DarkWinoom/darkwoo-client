# darkwoo-client (in development)
> 使用 [vue-admin-template](https://github.com/PanJiaChen/vue-admin-template) 搭建的网页CMS系统后台通用前端（客户端），使用了webpack4.0 & vue2.0 & element-ui & axios等相关技术

## 项目特点
* 前后端完全分离，可以搭配任何开发语言的后端使用（推荐配套后端 [darkwoo-server](https://github.com/DarkWinoom/darkwoo-server) ，使用的是 PHP Laravel）
* 使用 vue-router 驱动路由的SPA前端，响应时间比传统项目更快
* 文件管理系统支持本地存储和扩展云存储，支持快传与断点续传（可以跨浏览器），无需担心因多次上传同一个文件而浪费带宽与硬盘空间
* 精确到增删改查的用户角色权限管理
* ...

## 功能清单
- [ ] 用户登录与鉴权
- [ ] 用户管理
- [ ] 用户角色管理
- [ ] 图片裁剪
- [ ] 文件上传（支持快传与断点续传）
- [ ] 在线文件管理
- [ ] 在线留言
- [ ] 访客会员管理
- [ ] 客服工单
- [ ] 栏目管理
- [ ] 文章列表管理
- [ ] 列表Excel导出
- [ ] 富文本编辑器
- [ ] 文章详图管理（支持多图与多视频的）
- [ ] 文章附图管理
- [ ] 评论管理
- [ ] SEO管理（包括栏目与文章）
- [ ] 计划任务
- [ ] 系统设置
- [ ] 帮助中心
- [ ] ...

**该项目目前正在开发中，每次 commits 文件结构可能会有较大幅度的调整**

## Build Setup

```bash
# 克隆项目
git clone https://github.com/DarkWinoom/darkwoo-client.git

# 进入项目目录
cd darkwoo-client

# 安装依赖
npm install

# 启动服务
npm run dev
```

浏览器访问 [http://localhost:9528](http://localhost:9528)

## 发布

```bash
# 构建测试环境
npm run build:stage

# 构建生产环境
npm run build:prod
```

## 其它

```bash
# 预览发布环境效果
npm run preview

# 预览发布环境效果 + 静态资源分析
npm run preview -- --report

# 代码格式检查
npm run lint

# 代码格式检查并自动修复
npm run lint -- --fix
```

## Reference

* [vue-admin-template](https://github.com/PanJiaChen/vue-admin-template) 本项目使用的底层框架
* [Axios](https://github.com/axios/axios) 浏览器http请求库，用于管理所有请求
* [eslint](https://github.com/eslint/eslint) js代码检测与测试工具
* [Mock](https://github.com/axios/axios) 前端数据伪装工具，可在脱离后端的前提下让前端独立运行
* [vue-cropper](https://github.com/xyxiao001/vue-cropper) vue在线图片截取组件
* [webpack](https://github.com/webpack/webpack) 热门的前端脚手架工具
* [sass](https://github.com/sass/sass) css脚本扩展
* [element-ui](https://github.com/ElemeFE/element) 基于vue的ui工具
* [simple-uploader.js](https://github.com/simple-uploader/Uploader) 由原生js驱动的上传相关组件
* [vue-cropper](https://github.com/xyxiao001/vue-cropper) vue在线图片截取组件

## Browsers support

Modern browsers and Internet Explorer 10+.

| [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/edge/edge_48x48.png" alt="IE / Edge" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>IE / Edge | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/firefox/firefox_48x48.png" alt="Firefox" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>Firefox | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/chrome/chrome_48x48.png" alt="Chrome" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>Chrome | [<img src="https://raw.githubusercontent.com/alrra/browser-logos/master/src/safari/safari_48x48.png" alt="Safari" width="24px" height="24px" />](http://godban.github.io/browsers-support-badges/)</br>Safari |
| --------- | --------- | --------- | --------- |
| IE10, IE11, Edge| last 2 versions| last 2 versions| last 2 versions

## License

[MIT](https://github.com/DarkWinoom/darkwoo-client/blob/master/LICENSE) license.