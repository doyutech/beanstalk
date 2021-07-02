<?php

namespace Douyu\Beanstalk;

use Douyu\Beanstalk\Pool\PoolFactory;
use Hyperf\Utils\Context;
use Hyperf\Pool\Connection;

/**
 * Class Beanstalk
 * @package Douyu\Beanstalk
 */
class Beanstalk
{
    /**
     * @var PoolFactory
     */
    protected $factory;
    
    /**
     * @var string
     */
    protected $poolName = 'default';
    
    public function __construct(PoolFactory $factory)
    {
        $this->factory = $factory;
    }
    
    public function __call($name, $arguments)
    {
        // Get a connection from coroutine context or connection pool.
        $hasContextConnection = Context::has($this->getContextKey());
        $connection = $this->getConnection($hasContextConnection);
        
        try {
            $connection = $connection->getConnection();
            // Execute the command with the arguments.
            $result = $connection->{$name}(...$arguments);
        } finally {
            // Release connection.
            if (! $hasContextConnection) {
                // Release the connection after command executed.
                $connection->release();
            }
        }
        
        return $result;
    }
    
    /**
     * Get a connection from coroutine context, or from beanstalk connection pool.
     * @param mixed $hasContextConnection
     */
    private function getConnection($hasContextConnection): BeanstalkConnection
    {
        $connection = null;
        if ($hasContextConnection) {
            $connection = Context::get($this->getContextKey());
        }
        if (! $connection instanceof BeanstalkConnection) {
            $pool = $this->factory->getPool($this->poolName);
            $connection = $pool->get();
        }
        if (! $connection instanceof BeanstalkConnection) {
            throw new \RuntimeException('The connection is not a valid BeanstalkConnection.');
        }
        return $connection;
    }
    
    /**
     * The key to identify the connection object in coroutine context.
     */
    private function getContextKey(): string
    {
        return sprintf('beanstalk.connection.%s', $this->poolName);
    }
}