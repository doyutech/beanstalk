<?php

namespace Douyu\Beanstalk\Pool;

use Psr\Container\ContainerInterface;
use Hyperf\Di\Container;

/**
 * Class PoolFactory
 * @package Douyu\Beanstalk\Pool
 */
class PoolFactory
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    
    /**
     * @var BeanstalkPool[]
     */
    protected $pools = [];
    
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }
    
    public function getPool(string $name): BeanstalkPool
    {
        if (isset($this->pools[$name])) {
            return $this->pools[$name];
        }
        
        if ($this->container instanceof Container) {
            $pool = $this->container->make(BeanstalkPool::class, ['name' => $name]);
        } else {
            $pool = new BeanstalkPool($this->container, $name);
        }
        return $this->pools[$name] = $pool;
    }
}
