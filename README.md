<p align="center">
    <h1 align="center">QLU Online Judge</h1>
    <br>
</p>

QLUOJ是一款基于JNOJ开发的OJ   
![](docs/images/show.png)

更新日志  
----------  
[+] 表示为增添功能   
[-] 表示为删除功能   
[&] 表示为修改功能   
----------  
20210217

[&] 把Polygon verify data界面修好了，能够正常使用了  

20210205

[-] 删除了之前的ELO计算方法

[+] 添加了新的基于Codeforces的ELO计算方法

20210203  
[+] 在提交界面为每个提交添加了详细的在第几个测试数据错误  
[+] 在提交界面添加了用户的rating颜色和返回状态颜色  
[&] 修改了计算rating方法,可以多次计算并且只要有提交就会计算  

20210201  
[+] 把隐藏的polygon系统调出来了  
[&] 进一步修改了CF赛制的榜单的显示，现在加上了每个题的分数了  


20210131  
[&] 修改了管理员后台为比赛添加用户的SQL语句bug   
[&] 修改了User view的rating设置  
[&] 完善了rating的计算  

20210129  
[+] 为单人赛制增添每道题不同的分值，并且可以前端设置。还可以设置每道题每分钟减多少分  
[-] 删除了单人模式下一血额外加分功能  
[&] 修改了单人赛榜单先排名做题数的问题   
[&] 修改了积分的算分算法  
[&] 把user主界面的rating图调出来了  

联系我  
----------
![](docs/images/contact.png)


目录结构  
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
