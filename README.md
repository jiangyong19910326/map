<h1 align="center"> map </h1>
#
<p align="center"> a map API SDK.</p>
# 

[![Build Status](https://travis-ci.org/jiangyong19910326/map.svg?branch=master)](https://travis-ci.org/jiangyong19910326/map)

## 安装

```shell
$ composer require jiangyong/map -vvv
```

## 配置
```shell
use Jiangyong\Map\Map;

$key = 'xxxxxxxxxxxxxxxxxxxxxxxxxxx';

$map = new Map($key);
```
## 通过输入地址获取解析
```shell
$response = $map->getAddress('IFS国际金融中心','成都')
```


## 通过输入经纬度获取解析

```shell
$response = $map->getLocation('104.081298,30.655321');
```
## 通多输入地址获取解析xml格式 可选参数,json,
```shell
$resopnse = $map->getAddress(('IFS国际金融中心','成都','false','xml'))
```
## 通过输入经纬度获取解析xml格式，可选参数,json
```shell
$resopnse = $map->getLocation(('104.081298,30.655321',1000,'all','false','xml'))
```
## 在laravel 中的使用
在 Laravel 中使用也是同样的安装方式，配置写在 config/services.php 中 加入：
```shell
 'map' => [
        'key' => env('MAP_API_KEY'),
    ],
```
然后在 .env 中配置 WEATHER_API_KEY ：
```shell
MAP_API_KEY=xxxxxxxxxxxxxxxxxxxxx
```

TODO

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/jiangyong/map/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/jiangyong/map/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT