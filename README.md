<p align="center">
    <h1 align="center">QLU Online Judge</h2>
    <br>
</p>

### 介绍

	QLUOJ是一款基于JNOJ开发的OJ，由我校OJ开发组进行维护和二次开发。   
<img src="https://user-images.githubusercontent.com/86877361/127628167-7990e8b9-c23e-4f54-97ce-2acd22f837a0.png" width=800px height=450px>

### 更新日志  
 
- - -

	[+] 表示为增添功能   
	[-] 表示为删除功能   
	[&] 表示为修改功能   

- - -

2021.08.02
    
    [+] 增加了题目搜索中模糊搜索Tags的功能
	[&] 修复了SPJ保存时不自动编译的问题

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
