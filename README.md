<p align="center">
    <h1 align="center">QLU Online Judge</h2>
    <br>
</p>

### 介绍 | [![svg](https://img.shields.io/badge/Github-QLUOJ-green.svg)](https://github.com/linkscx/qluoj)

	QLUOJ是一款基于JNOJ开发的OJ，由我校OJ开发组进行维护和二次开发。

<img src="https://user-images.githubusercontent.com/86877361/127628167-7990e8b9-c23e-4f54-97ce-2acd22f837a0.png" width=800px height=450px>

### 更新日志  
 
- - -

	[+] 表示为增添功能   
	[-] 表示为删除功能   
	[&] 表示为修改功能   

- - -

2021.08.29

	[&] 修复了Problem List 搜索后点击下一页，搜索被取消的问题
	[+] 增加了problem-statistics点击language标签查看源码
	[&] 修复了problem-statistics页面点击属性名不能排序的问题

2021.08.25

	[&] 修改批量生成账号时，需要填写nickname	
	[&] 修改了创建Group的权限，仅管理员和助教可创建
	[&] 修改了Group中"邀请用户-用户同意"才能加入的工作方式，变更为Manager直接添加用户，无需经过用户同意；功能完善了可批量向Group中添加用户
	[&] 把Group中的"Leader"标签修改为"Creator"

2021.08.24

	[+] 为管理员在添加题目到比赛时，增加了“多个添加”的方法

2021.08.23

	[&] 优化了Contest-Problem提交代码的排版
	[&] 修复了Contest浏览题目时因为使用Pjax导致PDF无法跳转打开（取消Contest界面使用Pjax）
	[&] 更改了大部分页面的title(添加了 - OJName字样)；更改了Wiki默认页面

2021.08.22
	
	[&] 完善了Polygon-Create Problem界面中Description,Tags,Hint的标签和提示

2021.08.21

	[+] 为管理员和助教在导入Polygon题目到题库时，增加了“多个添加”的方法

2021.08.19

	[&] 修复了Status界面查看run-info时，测试数据太多无法将run-info插入数据库，导致run-info无法正常显示的问题，把数据库中solution_info表的run_info属性类型改成了longtext

2021.08.15

	[&] 修复了Status界面查看run-info时，测试数据中包含转义字符因而返回的json数据格式错误，导致run-info无法正常显示的问题

2021.08.11
	
	[+] 为助教增加了从审核题目界面点击Polygon ID查看Polygon题目的权限

2021.08.10

	[+] 增加了审核题目界面通过Polygon ID查询题目

2021.08.06
	
	[&] 修复了Status页面无法按属性排序的问题

2021.08.03

	[+] 为助教开放了后台的权限，但助教后台界面只能操作题目，用来辅助管理员审核、导入、删除题目。	
	[&] 修复了设置VIP用户时出现的bug，并且把"VIP用户"更名为"助教"
	[&] 修复了Polygon中Run data和Verrify data评测时一直处于Pending状态的问题

2021.08.02
    
	[+] 增加了题目搜索中模糊搜索Tags的功能
	[&] 修复了SPJ保存时不自动编译的问题(需更改php、gcc配置), 修复方法可查看下方的帮助文档"4.Centos下安装出现的问题及解决方法"

2021.07.29

	[+] 在Polygon系统中，显示Import Problem功能，增加了对从domjudge导出题目类型(.zip)的上传支持，也支持上传自行构建的zip题目压缩包
	[-] 删除了管理员后台的Import Problem接口，使Import Problem只在Polygon中显示，并且导入题目路径重定向至Polygon文件夹

2021.07.28

	[+] 提交程序时增加了C++11、C++14、C++17的编译选项

2021.07.24

	[+] 在Markdown编辑器中增加了上传PDF的功能，上传后自动在内容中追加打开PDF的超链接

2021.02.17

	[&] 把Polygon verify data界面修好了，能够正常使用了  

2021.02.05

	[-] 删除了之前的ELO计算方法
	[+] 添加了新的基于Codeforces的ELO计算方法

2021.02.03  

	[+] 在提交界面为每个提交添加了详细的在第几个测试数据错误  
	[+] 在提交界面添加了用户的rating颜色和返回状态颜色  
	[&] 修改了计算rating方法,可以多次计算并且只要有提交就会计算  

2021.02.01  

	[+] 把隐藏的polygon系统调出来了  
	[&] 进一步修改了CF赛制的榜单的显示，现在加上了每个题的分数了  


2021.01.31  

	[&] 修改了管理员后台为比赛添加用户的SQL语句bug   
	[&] 修改了User view的rating设置  
	[&] 完善了rating的计算  

2021.01.29  

	[+] 为单人赛制增添每道题不同的分值，并且可以前端设置。还可以设置每道题每分钟减多少分  
	[-] 删除了单人模式下一血额外加分功能  
	[&] 修改了单人赛榜单先排名做题数的问题   
	[&] 修改了积分的算分算法  
	[&] 把user主界面的rating图调出来了  


### 帮助文档

----------

1. [安装教程](https://github.com/linkscx/qluoj/blob/master/docs/install.md)
2. [更新教程](https://github.com/linkscx/qluoj/blob/master/docs/update.md)
3. [JNOJ作者Wiki](https://github.com/shi-yang/jnoj/wiki)
4. [Centos下安装出现的问题及解决方法](https://blog.csdn.net/qq_45530271/article/details/119842371)

### 目录结构  

----------

      assets/             资源文件的定义
      commands/           控制台命令
      components/         Web 应用程序组件
      config/             Web 应用程序配置信息
      controllers/        控制器(Controller)文件
      docs/               文档目录
      judge/              判题机所在目录
      judge/data          判题数据目录
      mail/               发邮件时的视图模板
      messages/           多语言翻译
      migrations/         数据库迁移时的各种代码
      models/             模型(Model)文件
      modules/admin       Web 后台应用
      modules/polygon     多边形出题系统
      runtime/            Web 程序运行时生成的缓存
      tests/              各种测试
      vendor/             第三方依赖
      views/              视图(View)文件
      web/                Web 入口目录
      widgets/            各种插件
      socket.php          用于启动 Socket，提供消息通知功能
