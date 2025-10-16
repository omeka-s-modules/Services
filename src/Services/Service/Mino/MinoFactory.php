<?php
namespace Services\Services\Service\Mino;

use Interop\Container\ContainerInterface;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Services\Services\Mino\Mino;

class MinoFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $services, $requestedName, array $options = null)
    {
        return new Mino($services);
    }
}
