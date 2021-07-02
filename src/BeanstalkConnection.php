<?php

namespace Douyu\Beanstalk;

use Hyperf\Contract\ConnectionInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Pool\Connection as BaseConnection;
use Hyperf\Pool\Exception\ConnectionException;
use Hyperf\Pool\Pool;
use Psr\Container\ContainerInterface;

/**
 * Class BeanstalkConnection
 * @package Douyu\Beanstalk
 */
class BeanstalkConnection extends BaseConnection implements ConnectionInterface
{
    
    /**
     * @var SwBeanstalk
     */
    protected $connection;
    
    protected $config;
    
    public function __construct(ContainerInterface $container, Pool $pool, array $config)
    {
        parent::__construct($container, $pool);
        $this->config = $config;
        
        $this->reconnect();
    }
    
    public function __call($name, $arguments)
    {
        try {
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
            $result = $this->retry($name, $arguments, $exception);
        }
        
        return $result;
    }
    
    public function getActiveConnection()
    {
        if ($this->check()) {
            return $this;
        }
        
        if (! $this->reconnect()) {
            throw new ConnectionException('Connection reconnect failed.');
        }
        
        return $this;
    }
    
    public function close(): bool
    {
        unset($this->connection);
        
        return true;
    }
    
    public function release(): void
    {
        parent::release();
    }
    
    protected function retry($name, $arguments, \Throwable $exception)
    {
        $logger = $this->container->get(StdoutLoggerInterface::class);
        $logger->warning(sprintf('Beanstalk::__call failed, because ' . $exception->getMessage()));
        
        try {
            $this->reconnect();
            $result = $this->connection->{$name}(...$arguments);
        } catch (\Throwable $exception) {
            $this->lastUseTime = 0.0;
            throw $exception;
        }
        
        return $result;
    }
    
    public function reconnect(): bool
    {
        $this->connection = $this->createBeanstalk();
        $this->lastUseTime = microtime(true);
        
        return true;
    }
    
    protected function createBeanstalk()
    {
        $client = (new SwBeanstalk($this->config));
        $client->connect();
        return $client;
    }
}