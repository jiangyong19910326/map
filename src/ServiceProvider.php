<?php
/**
 * Created by PhpStorm.
 * User: 82683
 * Date: 2018/9/17 0017
 * Time: 上午 11:16
 */

namespace Jiangyong\Map;


class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    protected $defer = true;

    public function register()
    {
        $this->app->singleton(Map::class, function(){
            return new Map(config('services.map.key'));
        });

        $this->app->alias(Map::class, 'map');
    }

    public function provides()
    {
        return [Map::class, 'map'];
    }
}