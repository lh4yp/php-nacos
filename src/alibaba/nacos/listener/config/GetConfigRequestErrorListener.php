<?php


namespace alibaba\nacos\listener\config;


use alibaba\nacos\listener\Listener;

class GetConfigRequestErrorListener extends Listener
{
    /**
     * @var array 观察者数组
     */
    protected static $observers = array();
}