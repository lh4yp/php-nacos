<?php


namespace alibaba\nacos;


use alibaba\nacos\failover\LocalConfigInfoProcessor;

/**
 * Class DummyNacosClient
 * @author suxiaolin
 * @package alibaba\nacos
 */
class DummyNacosClient implements NacosClientInterface
{
    public static function get($env, $dataId, $group,$userName,$password, $tenant)
    {
        $config = "";
        LocalConfigInfoProcessor::saveSnapshot($env, $dataId, $group, $tenant, $config);
        return $config;
    }

    public static function listener($env, $dataId, $group, $config,$userName,$password, $tenant = "")
    {
        do {
            // 短暂休息会儿
            usleep(500);
        } while (true);
    }

    public static function publish($dataId, $group, $content,$userName,$password, $tenant = "")
    {
        return true;
    }

    public static function delete($dataId, $group,$userName,$password, $tenant)
    {
        return true;
    }
}