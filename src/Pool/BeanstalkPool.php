<?php

namespace Douyu\Beanstalk\Pool;

use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\ConnectionInterface;
use Hyperf\Pool\Connection;
use Hyperf\Pool\Pool;
use Hyperf\Pool\Frequency;
use Hyperf\Utils\Arr;
use Douyu\Beanstalk\BeanstalkConnection;
use Douyu\Beanstalk\SwBeanstalk;
use Psr\Container\ContainerInterface;

class BeanstalkPool extends Pool
{
    
    /**
     * @var string
     */
    protected $name;
    
    /**
     * @var array
     */
    protected $config;
    
    public function __construct(ContainerInterface $container, string $name)
    {
        $this->name = $name;
        $config = $container->get(ConfigInterface::class);
        
        $key = sprintf('beanstalk.%s', $this->name);
        if (! $config->has($key)) {
            throw new \InvalidArgumentException(sprintf('config[%s] is not exist!', $key));
        }
        
        $this->config = $config->get($key);
        $options = Arr::get($this->config, 'pool', []);
        
        $this->frequency = make(Frequency::class);
        
        parent::__construct($container, $options);
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    protected function createConnection(): ConnectionInterface
    {
        return (new BeanstalkConnection($this->container, $this, $this->config));
    }
}