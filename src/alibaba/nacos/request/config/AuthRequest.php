<?php

namespace alibaba\nacos\request\config;

use alibaba\nacos\NacosConfig;
use alibaba\nacos\util\LogUtil;
use alibaba\nacos\request\Request;
use alibaba\nacos\util\ReflectionUtil;

/**
 * Class GetAuthRequest
 * @author suxiaolin
 * @package alibaba\nacos\request\config
 */
class AuthRequest extends Request
{

    /**
     * 账号。
     * @var
     */
    private $username;

    /**
     * 密码。
     * @var
     */
    private $password;

    /**
     * @param mixed
     */
    public function setUserName($userName)
    {
        $this->username = $userName;
    }

    /**
     * @return mixed
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * @param mixed $tenant
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }
    /**
     * @return mixed $tenant
     */
    public function getPassword()
    {
        return $this->password;
    }
    protected function getParameterAndHeader()
    {
        $headers = [];
        $parameterList = [];

        $properties = ReflectionUtil::getProperties($this);
        foreach ($properties as $propertyName => $propertyValue) {
            $parameterList[$propertyName] = $propertyValue;
        }

        if (NacosConfig::getIsDebug()) {
            LogUtil::info(strtr("parameterList: {parameterList}, headers: {headers}", [
                "parameterList" => json_encode($parameterList),
                "headers" => json_encode($headers)
            ]));
        }
        return [$parameterList, $headers];
    }
}