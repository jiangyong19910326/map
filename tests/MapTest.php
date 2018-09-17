<?php
/**
 * Created by PhpStorm.
 * User: 82683
 * Date: 2018/9/17 0017
 * Time: 上午 9:39
 */
namespace Jiangyong\Map\Tests;
use Jiangyong\Map\Map;
use PHPUnit\Framework\TestCase;
use Jiangyong\Map\Exceptions\InvalidArgumentException;
use Jiangyong\Map\Exceptions\HttpException;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Response;
use Mockery\Matcher\AnyArgs;


class MapTest extends TestCase
{
    /**
     * @throws InvalidArgumentException
     * @throws \Jiangyong\Map\Exceptions\HttpException
     * 测试参数$output 是否异常
     */
    public function testGetMapWithInvalidOutput()
    {
        // 实例化地图类
        $m = new Map('mock-key');
        //断言抛出参数异常类
        $this->expectException(InvalidArgumentException::class);
        //断言参数异常消息
        $this->expectExceptionMessage('Invalid response output:array');
        $m->getAddress('成都IFS','成都',false,'array');

        $this->fail('Faild to assert getWeather throw exception with invalid argument.');
    }

    /**
     * @test
     * 测试地址解析
     */
    public function testGetAddress()
    {

        // 设置一个响应成功返回值 json测试
        $response = new Response(200, [], '{"success": true}');
        $client = \Mockery::mock(Client::class);

        $client->allows()->get('https://restapi.amap.com/v3/geocode/geo',[
            'query' => [
                'key' => 'mock-key',
                'address' => '成都IFS',
                'city' => '成都',
                'batch' => 'false',
                'output' => 'json',
            ],
        ])->andReturn($response);

        $m = \Mockery::mock(Map::class, ['mock-key'])->makePartial();
        $m->allows()->getHttpClient()->andReturn($client);

        $this->assertSame(['success' => true], $m->getAddress('成都IFS','成都'));

        //xml测试
        $response = new Response(200, [], '<hello>content</hello>');
        $client = \Mockery::mock(Client::class);

        $client->allows()->get('https://restapi.amap.com/v3/geocode/geo',[
            'query' => [
                'key' => 'mock-key',
                'address' => '成都IFS',
                'city' => '成都',
                'batch' => 'false',
                'output' => 'xml',
            ],
        ])->andReturn($response);
        $m = \Mockery::mock(Map::class, ['mock-key'])->makePartial();
        $m->allows()->getHttpClient()->andReturn($client);
        $this->assertSame('<hello>content</hello>', $m->getAddress('成都IFS','成都','false','xml'));
    }

    /**
     * @test
     * 测试经纬度获取地址
     */
    public function testGetLocation()
    {
        //json
        $response = new Response(200, [], '{"success": true}');
        $client = \Mockery::mock(Client::class);
        $client->allows()->get('https://restapi.amap.com/v3/geocode/regeo',[
            'query' => [
                'key' => 'mock-key',
                'location' => '104.081298,30.655321',
                'radius' => 1000,
                'extensions' => 'all',
                'batch' => 'false',
                'output' => 'json'
            ],
        ])->andReturn($response);
        $m = \Mockery::mock(Map::class, ['mock-key'])->makePartial();
        $m->allows()->getHttpClient()->andReturn($client);
        $this->assertSame(['success' => true], $m->getLocation('104.081298,30.655321'));
        //xml
        $response = new Response(200, [], '<hello>content</hello>');
        $client = \Mockery::mock(Client::class);
        $client->allows()->get('https://restapi.amap.com/v3/geocode/regeo',[
            'query' => [
                'key' => 'mock-key',
                'location' => '104.081298,30.655321',
                'radius' => 1000,
                'extensions' => 'all',
                'batch' => 'false',
                'output' => 'xml'
            ],
        ])->andReturn($response);
        $m = \Mockery::mock(Map::class, ['mock-key'])->makePartial();
        $m->allows()->getHttpClient()->andReturn($client);
        $this->assertSame('<hello>content</hello>', $m->getLocation('104.081298,30.655321',1000,'all','false','xml'));
    }

    /**
     * @throws HttpException
     * @throws InvalidArgumentException
     * 测试extension参数是否异常
     */
    public function testGetLocationWithExtensions()
    {
        $m = new Map('mock-key');

        // 断言会抛出此异常类
        $this->expectException(InvalidArgumentException::class);

        // 断言异常消息为 'Invalid extensions value(base/all):foo'
        $this->expectExceptionMessage('Invalid extensions value(base/all):foo');

        $m->getLocation('104.081298,30.655321', 1000,'foo');

        $this->fail('Faild to assert getWeather throw exception with invalid argument.');

    }

    /**
     * 测试超时参数是否设置
     */
    public function testSetGuzzleOptions()
    {
        $m= new Map('mock-key');
        // 设置参数前，timeout 为 null
        $this->assertNull($m->getHttpClient()->getConfig('timeout'));
        // 设置参数
        $m->setGuzzleOptions(['timeout' => 5000]);
        // 设置参数后，timeout 为 5000
        $this->assertSame(5000, $m->getHttpClient()->getConfig('timeout'));
    }

    public function testGetHttpClient()
    {
        $m = new Map('mock-key');
        // 断言返回结果为 GuzzleHttp\ClientInterface 实例
        $this->assertInstanceOf(ClientInterface::class, $m->getHttpClient());
    }

    /**
     * 测试天气接口运行时间是否异常
     */
    public function testGetWeatherWithGuzzleRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()
            ->get(new AnyArgs())
            ->andThrow(new \Exception('request timeout'));

        $w = \Mockery::mock(Map::class, ['mock-key'])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');

        $w->getAddress('成都IFS','成都');
    }


}