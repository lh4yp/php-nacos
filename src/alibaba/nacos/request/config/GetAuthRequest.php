<?php

namespace alibaba\nacos\request\config;

/**
 * Class GetAuthRequest
 * @author suxiaolin
 * @package alibaba\nacos\request\config
 */
class GetAuthRequest extends AuthRequest
{

    protected $uri = "/nacos/v1/auth/login";
    protected $verb = "POST";

}