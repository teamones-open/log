<?php

namespace teamones;


use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;
use teamones\http\Client;

class TeamonesHandler extends AbstractProcessingHandler
{

    /**
     * @var array
     */
    protected $_instance = null;

    protected $_url = "";

    protected $_belongSystem = "";

    /**
     * Create a Cube handler
     *
     * @throws \UnexpectedValueException when given url is not a valid url.
     *                                   A valid url must consist of three parts : protocol://host:port
     *                                   Only valid protocols used by Cube are http and udp
     */
    public function __construct(string $url, $level = Logger::DEBUG, bool $bubble = true, $belongSystem = "not_config")
    {
        $this->_instance = new Client();
        $this->_url = $url;
        $this->_belongSystem = $belongSystem;

        parent::__construct($level, $bubble);
    }

    /**
     * Teamones 写入日志驱动
     * @param array $record
     */
    public function write(array $record): void
    {

        if (empty($record["message"])) {
            return;
        }

        $postData = [];

        // 日志类型
        $postData['level'] = !empty($record['level_name']) ? (string)strtolower($record['level_name']) : "error";

        // 所属系统
        $postData['belong_system'] = (string)$this->_belongSystem;

        // 日志内容
        $postData['record'] = $record["message"];

        if ((class_exists('\think\Request') || class_exists('\Webman\Http\Request')) && function_exists('\request')) {
            // 判断是否有request参数
            $request = \request();
            if (isset($request)) {
                $postData['route'] = $request->path();
                $postData['request_param'] = $request->all();
            }
        }

        if (!empty($postData)) {
            try {
                $response = $this->_instance->setHost($this->_url)
                    ->setBody($postData)
                    ->setMethod("POST")
                    ->request();
            } catch (\Exception $e) {}
        }
    }
}