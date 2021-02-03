<?php

namespace teamones\handler;


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


    /**
     * Create a Cube handler
     *
     * @throws \UnexpectedValueException when given url is not a valid url.
     *                                   A valid url must consist of three parts : protocol://host:port
     *                                   Only valid protocols used by Cube are http and udp
     */
    public function __construct(string $url, $level = Logger::DEBUG, bool $bubble = true)
    {
        $this->_instance = new Client();
        $this->_url = $url;

        parent::__construct($level, $bubble);
    }

    /**
     * Teamones 写入日志驱动
     * @param array $record
     */
    public function write(array $record): void
    {

        var_dump($record);

        $this->_instance->setHost($this->_url)
            ->setBody($record)
            ->setMethod("POST")
            ->request();
    }
}