<?php
declare (strict_types = 1);

use Application\Base\ErrorInterceptor;
use Application\Behaviors\LoggerAdapter;
use Psr\Container\ContainerInterface;
use Scaleum\Core\Contracts\KernelInterface;
use Scaleum\DependencyInjection\Helpers\Factory;
use Scaleum\Stdlib\Exceptions\ExceptionOutputHttp;

/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [  
    'error_renderer.config' => [
        'includeDetails'     => true,
        'includeTraces'      => true,
        'allowFullnamespace' => true,
    ],
];