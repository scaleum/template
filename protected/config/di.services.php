<?php
declare (strict_types = 1);

use Application\Behaviors\Services;
use Psr\Container\ContainerInterface;
use Scaleum\Core\Contracts\KernelInterface;
use Scaleum\Core\DependencyInjection\Framework;
use Scaleum\DependencyInjection\Helpers\Factory;

/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    Services::class => Factory::create(function (ContainerInterface $container) {
        $result = new Services($container->get(KernelInterface::class));
        $result->register($container->get(Framework::SVC_EVENTS));
        return $result;
    }),
];