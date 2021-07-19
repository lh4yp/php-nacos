<?php


namespace alibaba\nacos;


use alibaba\nacos\request\config\GetAuthRequest;
use Exception;
use alibaba\nacos\util\LogUtil;
use alibaba\nacos\listener\config\Config;
use alibaba\nacos\request\config\GetConfigRequest;
use alibaba\nacos\failover\LocalConfigInfoProcessor;
use alibaba\nacos\request\config\DeleteConfigRequest;
use alibaba\nacos\request\config\PublishConfigRequest;
use alibaba\nacos\request\config\ListenerConfigRequest;
use alibaba\nacos\listener\config\GetConfigRequestErrorListener;
use alibaba\nacos\listener\config\ListenerConfigRequestErrorListener;

/**
 * Class NacosClient
 * @author suxiaolin
 * @package alibaba\nacos
 */
class NacosClient implements NacosClientInterface
{
    public static function listener($env, $dataId, $group, $config,$userName,$password,$tenant = "")
    {
        $loop = 0;
        do {
            $loop++;

            $listenerConfigRequest = new ListenerConfigRequest();
            $listenerConfigRequest->setDataId($dataId);
            $listenerConfigRequest->setGroup($group);
            $listenerConfigRequest->setTenant($tenant);
            $listenerConfigRequest->setContentMD5(md5($config));
            $token = self::getAuth($userName,$password);
            $listenerConfigRequest->setToken($token);

            try {
                $response = $listenerConfigRequest->doRequest();
                if ($response->getBody()->getContents()) {
                    // 配置发生了变化
                    $config = self::get($env, $dataId, $group,$userName,$password, $tenant);

                    LogUtil::info("found changed config: " . $config);

                    // 保存最新的配置
                    LocalConfigInfoProcessor::saveSnapshot($env, $dataId, $group, $tenant, $config);
                }
            } catch (Exception $e) {
                LogUtil::error("listener请求异常, e: " . $e->getMessage());
                ListenerConfigRequestErrorListener::notify($env, $dataId, $group, $tenant);
                // 短暂休息会儿
                usleep(500);
            }
            LogUtil::info("listener loop count: " . $loop);
        } while (true);
    }

    public static function get($env, $dataId, $group,$userName,$password, $tenant)
    {
        $getConfigRequest = new GetConfigRequest();
        $getConfigRequest->setDataId($dataId);
        $getConfigRequest->setGroup($group);
        $getConfigRequest->setTenant($tenant);
        $token = self::getAuth($userName,$password);
        $getConfigRequest->setToken($token);
        
        try {
            $response = $getConfigRequest->doRequest();
            $config = $response->getBody()->getContents();
            LocalConfigInfoProcessor::saveSnapshot($env, $dataId, $group, $tenant, $config);
        } catch (Exception $e) {
            LogUtil::error("获取配置异常，开始从本地获取配置, message: " . $e->getMessage());
            $config = LocalConfigInfoProcessor::getFailover($env, $dataId, $group, $tenant);
            $config = $config ? $config
                : LocalConfigInfoProcessor::getSnapshot($env, $dataId, $group, $tenant);
            $configListenerParameter = Config::of($env, $dataId, $group, $tenant, $config);
            GetConfigRequestErrorListener::notify($configListenerParameter);
            if ($configListenerParameter->isChanged()) {
                $config = $configListenerParameter->getConfig();
            }
        }

        return $config;
    }

    public static function publish($dataId, $group, $content,$userName,$password, $tenant = "")
    {
        $publishConfigRequest = new PublishConfigRequest();
        $publishConfigRequest->setDataId($dataId);
        $publishConfigRequest->setGroup($group);
        $publishConfigRequest->setTenant($tenant);

        $publishConfigRequest->setContent($content);

        $token = self::getAuth($userName,$password);
        $publishConfigRequest->setToken($token);

        try {
            $response = $publishConfigRequest->doRequest();
        } catch (Exception $e) {
            return false;
        }
        return $response->getBody()->getContents() == "true";
    }

    public static function delete($dataId, $group,$userName,$password, $tenant)
    {
        $deleteConfigRequest = new DeleteConfigRequest();
        $deleteConfigRequest->setDataId($dataId);
        $deleteConfigRequest->setGroup($group);
        $deleteConfigRequest->setTenant($tenant);

        $token = self::getAuth($userName,$password);
        $deleteConfigRequest->setToken($token);

        $response = $deleteConfigRequest->doRequest();
        return $response->getBody()->getContents() == "true";
    }

    public static function getAuth($userName,$password){
        $getAuthRequest = new GetAuthRequest();
        $getAuthRequest->setUserName($userName);
        $getAuthRequest->setPassword($password);

        $response = $getAuthRequest->doRequest();
        $res = $response->getBody()->getContents();
        $res = json_decode($res,true);
        if ($res && isset($res['accessToken'])) {
            // 获取到了token
            return $res['accessToken'];
        }

        return '';
    }
}
