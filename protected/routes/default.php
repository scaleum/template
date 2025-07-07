<?php
declare (strict_types = 1);

/**
 * This file is part of Scaleum Framework.
 *
 * (C) 2009-2025 Maxim Kirichenko <kirichenko.maxim@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'home'      => [
        'path'     => '/',
        'methods'  => 'GET|POST',
        'callback' => [
            'controller' => Application\Controllers\Ui\DashboardController::class,
            'method'     => 'index',
        ],
    ],
    'locale'    => [
        'path'     => '/api/settings/i18n(?:/({:any}))?',
        'methods'  => 'GET|POST',
        'callback' => [
            'controller' => Application\Controllers\Rest\LocaleController::class,
            'method'     => '*',
        ],
    ],   
];