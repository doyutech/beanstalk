# Beanstalk协程客户端组件

组件：douyu/beanstalk

> composer 安装
```
cmd：composer require douyu/beanstalk:v1.0.0
```


> 发布配置
```
php bin/hyperf.php vendor:publish douyu/beanstalk
```


> 配置异步队列
配置文件：config/autoload/async_queue.php 

> 配置如下，可参看hyperf/async-queue的相关配置与使用
```
'beanstalk'  => [
    'driver'    => \Douyu\Beanstalk\BeanstalkDriver::class,
    'beanstalk' => [
        'pool' => 'default'
    ],
    'channel' => 'btqueue',
    'tube'    => 'default',
    'timeout' => 2,
    'retry_seconds' => [1, 5, 10],
    'handle_timeout' => 10,
    'processes' => 1,
    'concurrent' => [
        'limit' => 5,
    ],
]
```



