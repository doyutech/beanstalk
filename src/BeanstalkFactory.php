<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://hyperf.wiki
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace Douyu\Beanstalk;

use Hyperf\Contract\ConfigInterface;
use RuntimeException;

class BeanstalkFactory
{
    /**
     * @var BeanstalkProxy[]
     */
    protected $proxies;

    public function __construct(ConfigInterface $config)
    {
        $beanstalkConfig = $config->get('beanstalk');

        foreach ($beanstalkConfig as $poolName => $item) {
            $this->proxies[$poolName] = make(BeanstalkProxy::class, ['pool' => $poolName]);
        }
    }

    /**
     * @return BeanstalkProxy
     */
    public function get(string $poolName)
    {
        $proxy = $this->proxies[$poolName] ?? null;
        if (! $proxy instanceof BeanstalkProxy) {
            throw new RuntimeException('Invalid Beanstalk proxy.');
        }

        return $proxy;
    }
}
