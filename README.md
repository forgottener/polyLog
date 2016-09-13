# polyLog
#### 说明

- 日志统一接收记录管理平台
- 由于考虑想做成服务化的日志平台,固采用RPC的方式提供方法给各个项目调用,不局限于PHP的项目
- 管理后台的权限控制已经实现,后台代码来自我的项目[Laravel5.2-AdminLTE-RBAC](https://github.com/forgottener/Laravel5.2-AdminLTE-RBAC)

#### 环境依赖

- PHP安装swoole扩展
- mongodb

#### 使用方式

- 在项目里开启2个artisan命令:

```
php artisan swoole:server

php artisan hprose:server
```

- 以其他PHP项目为例,在任何PHP项目中,composer引入[hprose/hprose](https://github.com/hprose/hprose-php),日志的内容最好遵循[monolog](https://github.com/Seldaek/monolog/blob/master/doc/01-usage.md)的定义来实现,写个公共的方法调用polyLog/app/Console/Commands/HproseServer.php里写好的方法polyLog即可

```
function platformLog($log)
    {
        try {
            $client = new \Hprose\Socket\Client('tcp://127.0.0.1:1314', false);
            return $client->polyLog($log);
        } catch (\Exception $e) {
            //TODO:需要上报平台日志服务连接不上
            return true;
        }
    }
```
- 其他语言项目均只要实现客户端的逻辑来调用polyLog,[hprose](https://github.com/hprose)支持多种语言的

#### UI展示
- 后台首页
![image](http://note.youdao.com/yws/public/resource/b451d863b514bdc5b9c94a9ae18136df/xmlnote/398F6DBE1DD7483DA12C80DDE3706D0D/4966)
- 权限控制
![image](http://note.youdao.com/yws/public/resource/b451d863b514bdc5b9c94a9ae18136df/xmlnote/18DDFDFB2F12478CA51B811E26130CEB/4970)
- 平台日志
![image](http://note.youdao.com/yws/public/resource/b451d863b514bdc5b9c94a9ae18136df/xmlnote/A5B736A5F3A14C3E8B5549EC566AE4D4/4978)
- Statistics
![image](http://note.youdao.com/yws/public/resource/b451d863b514bdc5b9c94a9ae18136df/xmlnote/2E0CB73CC38543E690B38DBD5BF2A6B7/4973)
- 各平台日志
![image](http://note.youdao.com/yws/public/resource/b451d863b514bdc5b9c94a9ae18136df/xmlnote/C00185915F414B32BD86876CB8BD5709/4975)
![image](http://note.youdao.com/yws/public/resource/b451d863b514bdc5b9c94a9ae18136df/xmlnote/54274C53F0F44C63A37ED1C13D034298/4980)