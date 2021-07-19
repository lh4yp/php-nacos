<?php


namespace alibaba\nacos;


use ReflectionException;
use alibaba\nacos\exception\ResponseCodeErrorException;
use alibaba\nacos\exception\RequestUriRequiredException;
use alibaba\nacos\exception\RequestVerbRequiredException;

/**
 * Class NacosClientInterface
 * @author suxiaolin
 * @package alibaba\nacos
 */
interface NacosClientInterface
{
    /**
     * @param $env
     * @param $dataId
     * @param $group
     * @param $userName
     * @param $password
     * @param $tenant
     * @return false|string|null
     */
    public static function get($env, $dataId, $group,$userName,$password, $tenant);

    /**
     * @param $env
     * @param $dataId
     * @param $group
     * @param $config
     * @param $userName
     * @param $password
     * @param string $tenant
     */
    public static function listener($env, $dataId, $group, $config,$userName,$password, $tenant = "");

    /**
     * @param $dataId
     * @param $group
     * @param $content
     * @param string $tenant
     * @return bool
     */
    public static function publish($dataId, $group, $content,$userName,$password, $tenant = "");

    /**
     * @param $dataId
     * @param $group
     * @param $userName
     * @param $password
     * @param $tenant
     * @return bool true 删除成功
     * @throws ReflectionException
     * @throws RequestUriRequiredException
     * @throws RequestVerbRequiredException
     * @throws ResponseCodeErrorException
     */
    public static function delete($dataId, $group,$userName,$password, $tenant);
}