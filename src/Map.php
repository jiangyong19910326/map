<?php
/**
 * Created by PhpStorm.
 * User: 82683
 * Date: 2018/9/17 0017
 * Time: 上午 9:38
 */

namespace Jiangyong\Map;
use GuzzleHttp\Client;
use Jiangyong\Map\Exceptions\HttpException;
use Jiangyong\Map\Exceptions\InvalidArgumentException;

class map
{
    /**
     * @var
     */
    protected $key;
    /**
     * @var array
     */
    protected $guzzleOptions = [];

    /**
     * map constructor.
     * @param $key
     * 初始化调入搞得api key;
     */
    public function __construct( $key )
    {
        $this->key = $key;
    }

    /**
     * @return Client
     * 获取guzzle 链接
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * @param array $options
     * 设置guzzle 某些参数，比如超时链接等
     */
    public function setGuzzleOptions(array $options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * @param $address
     * @param $city
     * @param string $output
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     * 地址解释
     */
    public function getAddress($address, $city , $batch = 'false' , $output = 'json')
    {
        //api url
        $url = 'https://restapi.amap.com/v3/geocode/geo';

        //异常返回数据格式抛出
        if(!\in_array(\strtolower($output),['json','xml'])) {
            throw new InvalidArgumentException('Invalid response output:'.$output);
        }

        //参数过滤
        $query = array_filter([
            'key' => $this->key,
            'address' => $address,
            'city' => $city,
            'batch' => $batch,
            'output' => $output,
        ]);

        try{
            //返回响应
            $response = $this->getHttpClient()
                ->get($url,['query' => $query])
                ->getBody()
                ->getContents();

            //返回格式判断
            return $output === 'json' ? \json_decode($response,true) : $response;

        } catch (\Exception $e) {
            //请求链接失败异常抛出
            throw new HttpException($e->getMessage(),$e->getCode(),$e);
        }

    }

    /**
     * @param $location
     * @param string $extensions
     * @param string $batch
     * @return mixed|string
     * @throws HttpException
     * @throws InvalidArgumentException
     * 根据经纬度获取地址
     */
    public function getLocation($location,$radius = 1000,$extensions = 'all',$batch = 'false' ,$output = 'json')
    {
        $url = 'https://restapi.amap.com/v3/geocode/regeo';

        if(!\in_array(\strtolower($extensions),['base','all'])) {
            throw new InvalidArgumentException('Invalid extensions value(base/all):'.$extensions);
        }

        //参数过滤
        $query = array_filter([
            'key' => $this->key,
            'location' => $location,
            'radius' => $radius,
            'extensions' => $extensions,
            'batch' => $batch,
            'output' => $output
        ]);

        try{
            //返回响应
            $response = $this->getHttpClient()
                ->get($url,['query' => $query])
                ->getBody()
                ->getContents();

            //返回格式判断
            return $output === 'json' ? \json_decode($response,true) : $response;

        } catch (\Exception $e) {
            //请求链接失败异常抛出
            throw new HttpException($e->getMessage(),$e->getCode(),$e);
        }
    }

}