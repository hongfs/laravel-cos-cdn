![展示图](https://user-images.githubusercontent.com/23376043/50483003-9667db80-0a24-11e9-93ce-fc4d306789b4.png)

<center>基于Laravel+Python+Redis+Cos的自建CDN</center>

---

> 在使用前请确保你对使用到的程序有一定了解，因为不提供基础使用教程

## 安装

环境要求：

- PHP >= 7.1.3
- python >= 3.6.6
- mysql >= 5.7
- redis
- OpenSSL PHP 扩展
- PDO PHP 扩展
- Mbstring PHP 扩展
- Tokenizer PHP 扩展
- XML PHP 扩展
- Ctype PHP 扩展
- JSON PHP 扩展

下载：

```shell
$ composer create-project hongfs/laravel-cos-cdn
```

配置：

```
DB_HOST=            # 数据库地址
DB_PORT=            # 数据库端口
DB_DATABASE=        # 数据库名称
DB_USERNAME=        # 数据库用户名
DB_PASSWORD=        # 数据库密码

REDIS_HOST=         # Redis地址
REDIS_PASSWORD=     # Redis密码
REDIS_PORT=         # Redis端口

STORAGE_BUCKET=     # bucket-appid 例如：libs-1252156936
STORAGE_SECRET_ID=  # SecretId
STORAGE_SECRET_KEY= # SecretKey
STORAGE_REGION=     # 地域
STORAGE_PATH=       # 存储路径
STORAGE_CDN_DOMAIN= # CDN域名
```

安装

```sheel
# 初始化Laravel程序
$ php artisan cdn:install

# 安装Python程序依赖库
$ pip3 install -r python/requirements.txt
```

添加到crontab

> 这块不会配置请自行搜索，没有配置的话将无法自动更新

```
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## 参考

[Laravel 部署优化](https://laravel.com/docs/5.7/deployment#optimization)

## License

MIT
