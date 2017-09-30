# 移动开发Laravel脚手架
> 用于移动端开发的脚手架

已集成：
* Passport API
* 阿里云短信（消息服务）
* 社会化登陆
> QQ:807615827

## 安装及配置
1. 检出代码，运行`composer install`
2. 设置配置文件 `cp .env.example .env`
    
    配置数据库，数据库迁移`php artisan migrate`
    设置缓存驱动为`redis`(请先确保已安装redis服务)
    ```$xslt
    # Aliyun SMS
    ALIYUN_SMS_AK=your aliyun_app_key
    ALIYUN_SMS_AS=your aliyun_app_secret
    
    # Passport
    PASSPORT_CLIENT_ID=2
    PASSPORT_CLIENT_SECRET=pQZPJvBdPRLwCcQxxxxxxxxxxxxxxIP1b6F2HJ
    ```
    a. 其中Aliyun SMS的`key`和`secret`从阿里云开通*消息服务*获取
    b. passport配置需在根目录下运行`php artisan passport:install`,然后复制控制台client_id=2的内容
## 使用
1. 移动端路由均放在`route/app.php`中，具体操作请查看`route/app.php`
2. 注册登陆已完成请查看`AuthController`